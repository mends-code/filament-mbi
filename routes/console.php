<?php

use App\Jobs\Chatwoot\ResetUnansweredConversationsJob;
use App\Jobs\DeleteDiscardedInvoicesJob;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new DeleteDiscardedInvoicesJob)->hourly();
$resetAssigneeInterval = config('services.chatwoot.reset_assignee_interval');

$resetAssigneeEnabled = config('services.chatwoot.reset_assignee_enabled');

if ($resetAssigneeEnabled) {
    Schedule::job(new ResetUnansweredConversationsJob)->everyFiveMinutes();
}
