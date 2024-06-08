<?php

namespace App\Traits;

trait HandlesInvoiceStatus
{
    /**
     * Get the color for the given invoice status.
     *
     * @param  string|null  $status
     * @return string
     */
    public function getInvoiceStatusColor($status): ?string
    {
        return match ($status) {
            'draft' => 'gray',
            'open' => 'warning',
            'paid' => 'success',
            'uncollectible' => 'danger',
            'void' => 'gray',
            'deleted' => 'gray',
            default => 'gray',
        };
    }

    /**
     * Get the label for the given invoice status.
     *
     * @param  string|null  $status
     * @return string
     */
    public function getInvoiceStatusLabel($status): ?string
    {
        return match ($status) {
            'draft' => 'Szkic',
            'open' => 'W trakcie',
            'paid' => 'Zapłacona',
            'uncollectible' => 'Nieściągalna',
            'void' => 'Unieważniona',
            'deleted' => 'Usunięta',
            default => 'Nieznany',
        };
    }
}
