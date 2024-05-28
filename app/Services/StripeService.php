<?php

namespace App\Services;

use App\Models\ChatwootContact;
use App\Models\StripeCustomer;
use App\Models\StripeInvoice;
use App\Models\StripePrice;
use Stripe\Customer;
use Stripe\Invoice;
use Stripe\InvoiceItem;
use Stripe\Stripe;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
    }

    /**
     * Create a Stripe customer from a ChatwootContact.
     *
     * @return StripeCustomer
     */
    public function createCustomer(ChatwootContact $contact)
    {
        $customer = Customer::create([
            'name' => $contact->name,
            'email' => $contact->email,
            'phone' => $contact->phone_number,
            'metadata' => ['chatwoot_contact_id' => $contact->id],
        ]);

        $stripeCustomer = StripeCustomer::create([
            'id' => $customer->id,
            'data' => $customer->toArray(),
        ]);

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
        $customer = Customer::retrieve($stripeCustomerId);
        $customer->name = $customerData['name'];
        $customer->email = $customerData['email'];
        $customer->phone = $customerData['phone'];
        $customer->metadata = ['chatwoot_contact_id' => $customerData['chatwoot_contact_id']];
        $customer->save();

        $stripeCustomer = StripeCustomer::updateOrCreate(
            ['id' => $stripeCustomerId],
            [
                'data' => $customer->toArray(),
            ]
        );

        return $stripeCustomer;
    }

    /**
     * Create an invoice for a given customer and price.
     * If no customer is provided, create a new one.
     *
     * @param  int  $priceId
     * @param  string|null  $stripeCustomerId
     * @return StripeInvoice
     */
    public function createInvoice($chatwootContactId, $priceId, $stripeCustomerId = null)
    {
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
            'collection_method' => 'send_invoice',
            'days_until_due' => 0,
        ]);

        // Add invoice item using price ID
        InvoiceItem::create([
            'customer' => $stripeCustomer->id,
            'price' => $stripePrice->id,
            'invoice' => $invoice->id,
        ]);

        $finalizedInvoice = Invoice::retrieve($invoice->finalizeInvoice()->id);

        // Update local StripeInvoice model
        $stripeInvoice = StripeInvoice::create([
            'id' => $finalizedInvoice->id,
            'data' => $finalizedInvoice->toArray(),
        ]);

        return $stripeInvoice;
    }
}
