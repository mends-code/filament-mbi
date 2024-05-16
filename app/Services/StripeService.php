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

    public function createOrUpdateCustomer(ChatwootContact $contact)
    {
        $stripeCustomer = $contact->customer()->first();
        $payload = [
            'name' => $contact->name,
            'email' => $contact->email,
            'phone' => $contact->phone_number,
            'metadata' => ['chatwoot_contact_id' => $contact->id]
        ];

        if ($stripeCustomer) {
            $customer = Customer::retrieve($stripeCustomer->id);
            $customer = Customer::update($customer->id, $payload);
            $stripeCustomer->update([
                'data' => $customer->toArray(),
                'updated_at' => Carbon::now(),
            ]);
        } else {
            $customer = Customer::create($payload);
            $stripeCustomer = StripeCustomer::create([
                'id' => $customer->id,
                'data' => $customer->toArray(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            $contact->customer()->attach($stripeCustomer->id);
        }

        return $customer;
    }

    public function createInvoiceFromPrice(string $contactId, string $priceId, array $invoiceData = [])
    {
        $contact = ChatwootContact::findOrFail($contactId);
        $customer = $this->createOrUpdateCustomer($contact);

        $stripePrice = StripePrice::findOrFail($priceId);

        $invoice = Invoice::create(array_merge([
            'customer' => $customer->id,
        ], $invoiceData));

        InvoiceItem::create([
            'customer' => $customer->id,
            'price' => $stripePrice->id,
            'invoice' => $invoice->id,
        ]);

        return Invoice::retrieve($invoice->finalizeInvoice()->id);
    }
}
