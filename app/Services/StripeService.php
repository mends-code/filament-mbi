<?php

namespace App\Services;

use App\Models\ChatwootContact;
use App\Models\StripeCustomer;
use App\Models\StripeInvoice;
use App\Models\StripePrice;
use Illuminate\Support\Facades\Log;
use Stripe\Customer;
use Stripe\Invoice;
use Stripe\InvoiceItem;
use Stripe\Stripe;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('stripe.secret'));
        Log::info('StripeService initialized with API key.');
    }

    /**
     * Validate the email address.
     *
     * @param  string  $email
     * @return bool
     */
    protected function isValidEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Get the fallback email if the provided one is invalid.
     *
     * @param  string|null  $email
     * @param  int  $contactId
     * @return string
     */
    protected function getEmail($email, $contactId)
    {
        if (empty($email) || ! $this->isValidEmail($email)) {
            $defaultEmail = config('stripe.customer.default.email');
            $emailParts = explode('@', $defaultEmail);
            if (count($emailParts) === 2) {
                $emailParts[0] .= '+'.$contactId;

                return implode('@', $emailParts);
            }

            return $defaultEmail;
        }

        return $email;
    }

    /**
     * Create a Stripe customer from a ChatwootContact.
     *
     * @return StripeCustomer
     */
    public function createCustomer(ChatwootContact $contact)
    {
        Log::info("Creating Stripe customer for ChatwootContact ID: {$contact->id}");
        $email = $this->getEmail($contact->email, $contact->id);

        $customer = Customer::create([
            'name' => $contact->name,
            'email' => $email,
            'phone' => $contact->phone_number,
            'metadata' => ['chatwoot_contact_id' => $contact->id],
        ]);

        Log::info("Stripe customer created: {$customer->id}");

        $stripeCustomer = StripeCustomer::updateOrCreate(
            ['id' => $customer->id],
            [
                'data' => $customer->toArray(),
            ]
        );

        Log::info("StripeCustomer record updated/created for Stripe customer ID: {$customer->id}");

        return $stripeCustomer;
    }

    /**
     * Update a Stripe customer with new details.
     *
     * @param  string  $stripeCustomerId
     * @return StripeCustomer
     */
    public function updateCustomer($stripeCustomerId, array $customerData)
    {
        Log::info("Updating Stripe customer ID: {$stripeCustomerId}");
        $email = $this->getEmail($customerData['email'], $customerData['chatwoot_contact_id']);

        $customer = Customer::retrieve($stripeCustomerId);
        $customer->name = $customerData['name'];
        $customer->email = $email;
        $customer->phone = $customerData['phone'];
        $customer->metadata = ['chatwoot_contact_id' => $customerData['chatwoot_contact_id']];
        $customer->save();

        Log::info("Stripe customer updated: {$stripeCustomerId}");

        $stripeCustomer = StripeCustomer::updateOrCreate(
            ['id' => $stripeCustomerId],
            [
                'data' => $customer->toArray(),
            ]
        );

        Log::info("StripeCustomer record updated/created for Stripe customer ID: {$stripeCustomerId}");

        return $stripeCustomer;
    }

    /**
     * Create a quick invoice for a given customer and price.
     * If no customer is provided, create a new one.
     *
     * @param  int  $priceId
     * @param  string|null  $stripeCustomerId
     * @param  string  $collectionMethod  // 'charge_automatically' or 'send_invoice'
     * @param  int  $daysUntilDue
     * @return StripeInvoice
     */
    public function createQuickInvoice($chatwootContactId, $priceId, $stripeCustomerId = null, $collectionMethod = 'send_invoice', $daysUntilDue = 0)
    {
        Log::info("Creating quick invoice for ChatwootContact ID: {$chatwootContactId}, Price ID: {$priceId}, Stripe Customer ID: {$stripeCustomerId}");

        $contact = ChatwootContact::findOrFail($chatwootContactId);

        if ($stripeCustomerId) {
            $stripeCustomer = StripeCustomer::findOrFail($stripeCustomerId);
        } else {
            $stripeCustomer = StripeCustomer::latestForContact($chatwootContactId)->first();
            if (! $stripeCustomer) {
                $stripeCustomer = $this->createCustomer($contact);
            }
        }

        $stripePrice = StripePrice::findOrFail($priceId);

        // Create the invoice
        $invoice = Invoice::create([
            'customer' => $stripeCustomer->id,
            'collection_method' => $collectionMethod,
            'days_until_due' => $daysUntilDue,
            'currency' => strtoupper($stripePrice->data['currency']),
        ]);

        Log::info("Invoice created: {$invoice->id}");

        // Update local StripeInvoice model
        StripeInvoice::updateOrCreate(
            ['id' => $invoice->id],
            [
                'data' => $invoice->toArray(),
            ]);

        // Add invoice item using price ID
        InvoiceItem::create([
            'customer' => $stripeCustomer->id,
            'price' => $stripePrice->id,
            'invoice' => $invoice->id,
        ]);

        $finalizedInvoice = Invoice::retrieve($invoice->finalizeInvoice()->id);

        Log::info("Invoice finalized: {$finalizedInvoice->id}");

        // Update local StripeInvoice model
        $stripeInvoice = StripeInvoice::updateOrCreate(
            ['id' => $finalizedInvoice->id],
            [
                'data' => $finalizedInvoice->toArray(),
            ]);

        Log::info("StripeInvoice record updated/created for Invoice ID: {$finalizedInvoice->id}");

        return $stripeInvoice;
    }
}
