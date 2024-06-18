<?php

namespace App\Services;

use App\Models\Chatwoot\Contact;
use App\Models\Stripe\Customer as StripeCustomer;
use App\Models\Stripe\Price;
use Exception;
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
        try {
            Stripe::setApiKey(config('services.stripe.secret'));
            Log::info('Stripe API key configured.');
        } catch (Exception $e) {
            Log::error('Failed to configure Stripe API key.', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    protected function isValidEmail($email)
    {
        try {
            $validator = Validator::make(['email' => $email], ['email' => 'email']);

            return ! $validator->fails();
        } catch (Exception $e) {
            Log::error('Email validation failed.', ['email' => $email, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    protected function getEmail($email, $contactId)
    {
        try {
            if (empty($email) || ! $this->isValidEmail($email)) {
                Log::warning('Invalid email, using default.', ['email' => $email, 'contactId' => $contactId]);
                $defaultEmail = config('services.stripe.customer.default.email');
                $emailParts = explode('@', $defaultEmail);
                if (count($emailParts) === 2) {
                    $emailParts[0] .= '+'.$contactId;

                    return implode('@', $emailParts);
                }

                return $defaultEmail;
            }

            return $email;
        } catch (Exception $e) {
            Log::error('Failed to get valid email.', ['email' => $email, 'contactId' => $contactId, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function createCustomer(Contact $contact, $chatwootConversationId, $chatwootAccountId, $chatwootAgentId)
    {
        try {
            Log::info("Creating customer for contact ID: {$contact->id}");
            $email = $this->getEmail($contact->email, $contact->id);

            $customer = Customer::create([
                'name' => $contact->name,
                'email' => $email,
                'phone' => $contact->phone_number,
                'metadata' => [
                    'chatwoot_contact_id' => $contact->id,
                    'chatwoot_conversation_id' => $chatwootConversationId,
                    'chatwoot_account_id' => $chatwootAccountId,
                    'chatwoot_agent_id' => $chatwootAgentId,
                ],
            ]);

            Log::info('Customer created', ['customerId' => $customer->id]);

            return $customer;
        } catch (Exception $e) {
            Log::error('Failed to create customer.', ['contactId' => $contact->id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function updateCustomer($stripeCustomerId, array $customerData, $chatwootConversationId, $chatwootAccountId, $chatwootAgentId)
    {
        try {
            Log::info("Updating customer ID: {$stripeCustomerId}");
            $email = $this->getEmail($customerData['email'], $customerData['chatwoot_contact_id']);

            $customer = Customer::retrieve($stripeCustomerId);
            $customer->name = $customerData['name'];
            $customer->email = $email;
            $customer->phone = $customerData['phone'];
            $customer->metadata = [
                'chatwoot_contact_id' => $customerData['chatwoot_contact_id'],
                'chatwoot_conversation_id' => $chatwootConversationId,
                'chatwoot_account_id' => $chatwootAccountId,
                'chatwoot_agent_id' => $chatwootAgentId,
            ];
            $customer->update($customer->id);

            Log::info('Customer updated', ['customerId' => $stripeCustomerId]);

            return $customer;
        } catch (Exception $e) {
            Log::error('Failed to update customer.', ['customerId' => $stripeCustomerId, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function createInvoice(array $items, $chatwootContactId, $chatwootAgentId, $chatwootConversationId, $chatwootAccountId, $collectionMethod = 'send_invoice', $daysUntilDue = 0)
    {
        Log::info('createInvoice called', [
            'items' => $items,
            'chatwootContactId' => $chatwootContactId,
            'chatwootAgentId' => $chatwootAgentId,
            'chatwootConversationId' => $chatwootConversationId,
            'chatwootAccountId' => $chatwootAccountId,
            'collectionMethod' => $collectionMethod,
            'daysUntilDue' => $daysUntilDue,
        ]);

        try {
            Log::info("Creating invoice for contact ID: {$chatwootContactId}");

            $contact = Contact::find($chatwootContactId);

            if (! $contact) {
                Log::error('Contact not found', ['contactId' => $chatwootContactId]);
                throw new \Exception("No query results for model [App\\Models\\ChatwootContact] {$chatwootContactId}");
            }

            Log::info('Contact found', ['contactId' => $contact->id]);

            $stripeCustomer = StripeCustomer::latestForContact($chatwootContactId)->first();

            if (! $stripeCustomer || ! $this->customerExists($stripeCustomer->id)) {
                Log::info('No existing customer or customer deleted, creating new one', ['contactId' => $contact->id]);
                $stripeCustomer = $this->createCustomer($contact, $chatwootConversationId, $chatwootAccountId, $chatwootAgentId);
                Log::info('New customer created', ['customerId' => $stripeCustomer->id]);
            } else {
                Log::info('Existing customer found', ['customerId' => $stripeCustomer->id]);
            }

            $currencies = [];
            foreach ($items as $item) {
                $stripePrice = Price::findOrFail($item['priceId']);
                $currencies[] = $stripePrice->data['currency'];
            }

            if (count(array_unique($currencies)) > 1) {
                throw new Exception('All prices must have the same currency.');
            }

            $invoice = Invoice::create([
                'customer' => $stripeCustomer->id,
                'collection_method' => $collectionMethod,
                'days_until_due' => $daysUntilDue,
                'currency' => strtoupper($currencies[0]),
                'metadata' => [
                    'chatwoot_contact_id' => $chatwootContactId,
                    'chatwoot_conversation_id' => $chatwootConversationId,
                    'chatwoot_account_id' => $chatwootAccountId,
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
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Model not found', ['model' => 'ChatwootContact', 'contactId' => $chatwootContactId, 'error' => $e->getMessage()]);
            throw new \Exception("No query results for model [App\\Models\\ChatwootContact] {$chatwootContactId}");
        } catch (Exception $e) {
            Log::error('Failed to create invoice.', ['contactId' => $chatwootContactId, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    protected function customerExists($customerId)
    {
        try {
            Log::info('Retrieving customer from Stripe.', ['customerId' => $customerId]);

            // Attempt to retrieve the customer from Stripe
            $customer = Customer::retrieve($customerId);

            // Log the retrieved customer details
            Log::info('Customer retrieved.', ['customerId' => $customer->id, 'deleted' => $customer->deleted ?? 'not set']);

            // Check if the 'deleted' attribute is set to true
            if (isset($customer->deleted) && $customer->deleted === true) {
                Log::info('Customer is deleted.', ['customerId' => $customerId]);

                return false;
            }

            // If 'deleted' is not set or false, customer is active
            Log::info('Customer is active.', ['customerId' => $customerId]);

            return true;
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            // InvalidRequestException indicates that the customer does not exist
            Log::error('Customer does not exist.', ['customerId' => $customerId, 'error' => $e->getMessage()]);

            return false;
        } catch (\Exception $e) {
            // General exception handling for other possible errors
            Log::error('Customer retrieval failed.', ['customerId' => $customerId, 'error' => $e->getMessage()]);

            return false;
        }
    }
}
