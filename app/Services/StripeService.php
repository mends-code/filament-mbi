<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Invoice;
use Stripe\InvoiceItem;
use App\Models\ChatwootContact;
use App\Models\StripeCustomer;
use App\Models\StripePrice;
use Carbon\Carbon;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
    }

    public function createCustomer(ChatwootContact $contact)
    {
        $customer = Customer::create([
            'name' => $contact->name,
            'email' => $contact->email,
            'phone' => $contact->phone_number,
            'metadata' => ['chatwoot_contact_id' => $contact->id]
        ]);

        $stripeCustomer = StripeCustomer::create([
            'id' => $customer->id,
            'data' => $customer->toArray(),
            'chatwoot_contact_id' => $contact->id
        ]);

        return $stripeCustomer;
    }

    public function updateCustomer($stripeCustomerId, ChatwootContact $contact)
    {
        $customer = Customer::retrieve($stripeCustomerId);
        $customer->name = $contact->name;
        $customer->email = $contact->email;
        $customer->phone = $contact->phone_number;
        $customer->metadata = ['chatwoot_contact_id' => $contact->id];
        $customer->update($stripeCustomerId);

        $stripeCustomer = StripeCustomer::where('id', $stripeCustomerId)->first();
        if ($stripeCustomer) {
            $stripeCustomer->data = $customer->toArray();
            $stripeCustomer->chatwoot_contact_id = $contact->id;
            $stripeCustomer->save();
        } else {
            $stripeCustomer = $this->createCustomer($contact);
        }

        return $stripeCustomer;
    }

    public function createInvoiceFromPrice($chatwootContactId, $priceId, array $invoiceData = [], $stripeCustomerId = null)
    {
        $contact = ChatwootContact::findOrFail($chatwootContactId);
        
        if ($stripeCustomerId) {
            $stripeCustomer = StripeCustomer::findOrFail($stripeCustomerId);
        } else {
            $stripeCustomer = $contact->stripeCustomers()->first();
            if (!$stripeCustomer) {
                $stripeCustomer = $this->createCustomer($contact);
            }
        }

        $stripePrice = StripePrice::findOrFail($priceId);

        // Create the invoice
        $invoice = Invoice::create(array_merge([
            'customer' => $stripeCustomer->id,
        ], $invoiceData));

        // Add invoice items using price IDs
        InvoiceItem::create([
            'customer' => $stripeCustomer->id,
            'price' => $stripePrice->id,
            'invoice' => $invoice->id,
        ]);

        return Invoice::retrieve($invoice->finalizeInvoice()->id);
    }
}
