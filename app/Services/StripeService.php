<?php

namespace App\Services;

use App\Models\ChatwootContact;
use App\Models\StripeCustomer;
use App\Models\StripePrice;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Stripe\Customer;
use Stripe\Invoice;
use Stripe\InvoiceItem;
use Stripe\Stripe;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('stripe.secret'));
        Log::info('Stripe API key configured.');
    }

    protected function isValidEmail($email)
    {
        $validator = Validator::make(['email' => $email], ['email' => 'email']);

        return ! $validator->fails();
    }

    protected function getEmail($email, $contactId)
    {
        if (empty($email) || ! $this->isValidEmail($email)) {
            Log::warning('Invalid email, using default.', ['email' => $email, 'contactId' => $contactId]);
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

    public function createCustomer(ChatwootContact $contact)
    {
        Log::info("Creating customer for contact ID: {$contact->id}");
        $email = $this->getEmail($contact->email, $contact->id);

        $customer = Customer::create([
            'name' => $contact->name,
            'email' => $email,
            'phone' => $contact->phone_number,
            'metadata' => ['chatwoot_contact_id' => $contact->id],
        ]);

        Log::info('Customer created', ['customerId' => $customer->id]);

        return $customer;
    }

    public function updateCustomer($stripeCustomerId, array $customerData)
    {
        Log::info("Updating customer ID: {$stripeCustomerId}");
        $email = $this->getEmail($customerData['email'], $customerData['chatwoot_contact_id']);

        $customer = Customer::retrieve($stripeCustomerId);
        $customer->name = $customerData['name'];
        $customer->email = $email;
        $customer->phone = $customerData['phone'];
        $customer->metadata = ['chatwoot_contact_id' => $customerData['chatwoot_contact_id']];
        $customer->update($customer->id);

        Log::info('Customer updated', ['customerId' => $stripeCustomerId]);

        return $customer;
    }

    public function createInvoice($chatwootContactId, array $items, $stripeCustomerId = null, $chatwootAgentId = null, $collectionMethod = 'send_invoice', $daysUntilDue = 0)
    {
        Log::info("Creating invoice for contact ID: {$chatwootContactId}");

        $contact = ChatwootContact::findOrFail($chatwootContactId);
        Log::info('Contact found', ['contactId' => $contact->id]);

        if ($stripeCustomerId) {
            $stripeCustomer = StripeCustomer::findOrFail($stripeCustomerId);
            Log::info('Customer found', ['customerId' => $stripeCustomerId]);
        } else {
            $stripeCustomer = StripeCustomer::latestForContact($chatwootContactId)->first();
            if (! $stripeCustomer) {
                Log::info('No existing customer, creating new one', ['contactId' => $contact->id]);
                $stripeCustomer = $this->createCustomer($contact);
            }
        }

        $currencies = [];
        foreach ($items as $item) {
            $stripePrice = StripePrice::findOrFail($item['priceId']);
            $currencies[] = $stripePrice->data['currency'];
        }

        if (count(array_unique($currencies)) > 1) {
            throw new \Exception('All prices must have the same currency.');
        }

        $invoice = Invoice::create([
            'customer' => $stripeCustomer->id,
            'collection_method' => $collectionMethod,
            'days_until_due' => $daysUntilDue,
            'currency' => strtoupper($currencies[0]),
            'metadata' => [
                'chatwoot_agent_id' => $chatwootAgentId,
            ],
        ]);
        Log::info('Invoice created', ['invoiceId' => $invoice->id]);

        foreach ($items as $item) {
            InvoiceItem::create([
                'customer' => $stripeCustomer->id,
                'price' => $item['priceId'],
                'quantity' => $item['quantity'] ?? 1,
                'invoice' => $invoice->id,
            ]);
            Log::info('Invoice item added', ['invoiceId' => $invoice->id, 'priceId' => $item['priceId'], 'quantity' => $item['quantity'] ?? 1]);
        }

        $finalizedInvoice = Invoice::retrieve($invoice->finalizeInvoice()->id);
        Log::info('Invoice finalized', ['invoiceId' => $finalizedInvoice->id]);

        return $finalizedInvoice;
    }
}
