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
        Log::info('Stripe API key set.');
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
            Log::warning('Invalid email provided, using default.', ['email' => $email, 'contactId' => $contactId]);
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

        Log::info('Created Stripe customer', ['customer' => $customer->id]);


        return $customer;
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
        $customer->update($customer->id);

        Log::info('Updated Stripe customer', ['customer' => $stripeCustomerId]);

        return $customer;
    }

    /**
     * Create a quick invoice for a given customer and price.
     * If no customer is provided, create a new one.
     *
     * @param  int  $priceId
     * @param  string|null  $stripeCustomerId
     * @param  string  $collectionMethod  // 'charge_automatically' or 'send_invoice'
     * @param  int  $daysUntilDue
     * @return Invoice
     */
    public function createQuickInvoice($chatwootContactId, $priceId, $stripeCustomerId = null, $collectionMethod = 'send_invoice', $daysUntilDue = 0)
    {
        Log::info("Creating quick invoice for ChatwootContact ID: {$chatwootContactId}, Price ID: {$priceId}, Stripe Customer ID: {$stripeCustomerId}");

        $contact = ChatwootContact::findOrFail($chatwootContactId);
        Log::info('Fetching Chatwoot contact', ['contact' => $contact->id]);

        if ($stripeCustomerId) {
            $stripeCustomer = StripeCustomer::findOrFail($stripeCustomerId);
            Log::info('Stripe customer found', ['stripeCustomerId' => $stripeCustomerId]);
        } else {
            $stripeCustomer = StripeCustomer::latestForContact($chatwootContactId)->first();
            if (! $stripeCustomer) {
                Log::info('Creating new Stripe customer for contact', ['contact' => $contact->id]);
                $stripeCustomer = $this->createCustomer($contact);
            }
        }

        $stripePrice = StripePrice::findOrFail($priceId);
        Log::info('Stripe price found', ['priceId' => $priceId]);

        // Create the invoice
        $invoice = Invoice::create([
            'customer' => $stripeCustomer->id,
            'collection_method' => $collectionMethod,
            'days_until_due' => $daysUntilDue,
            'currency' => strtoupper($stripePrice->data['currency']),
        ]);
        Log::info('Created Stripe invoice', ['invoice' => $invoice->id]);

        Log::info("Invoice created: {$invoice->id}");

        // Add invoice item using price ID
        InvoiceItem::create([
            'customer' => $stripeCustomer->id,
            'price' => $stripePrice->id,
            'invoice' => $invoice->id,
        ]);
        Log::info('Added invoice item', ['invoice' => $invoice->id, 'price' => $stripePrice->id]);

        $finalizedInvoice = Invoice::retrieve($invoice->finalizeInvoice()->id);
        Log::info('Finalized Stripe invoice', ['invoice' => $finalizedInvoice->id]);

        Log::info("Invoice finalized: {$finalizedInvoice->id}");

        return $finalizedInvoice;
    }
}
