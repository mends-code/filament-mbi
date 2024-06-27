<?php

use App\Jobs\Chatwoot\ResetUnansweredConversationsJob;
use App\Jobs\DeleteDiscardedInvoicesJob;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new DeleteDiscardedInvoicesJob)->hourly();

config('services.chatwoot.reset_assignee_enabled') && Schedule::job(new ResetUnansweredConversationsJob)->everyFiveMinutes();
