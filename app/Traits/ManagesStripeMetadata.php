<?php

namespace App\Traits;

trait ManagesStripeMetadata
{
    public $stripeCustomerId;

    public function setStripeMetadataFromFilters(array $filters)
    {
        $this->stripeCustomerId = $filters['stripeCustomerId'] ?? null;
    }
}
