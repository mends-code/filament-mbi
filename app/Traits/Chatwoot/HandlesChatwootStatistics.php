<?php

namespace App\Traits\Chatwoot;

use App\Models\Chatwoot\Conversation;
use App\Models\Chatwoot\Message;
use App\Models\Stripe\Invoice;
use Carbon\CarbonInterval;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

trait HandlesChatwootStatistics
{
    // Base query for messages
    private function baseMessageQuery(int $year, int $month, int $chatwootUserId)
    {
        return Message::forYearAndMonth($year, $month)
            ->public()
            ->senderUser($chatwootUserId);
    }

    // Message statistics
    public function getMonthlyMessagesCount(int $year, int $month, int $chatwootUserId): int
    {
        return $this->baseMessageQuery($year, $month, $chatwootUserId)->count();
    }

    public function getMonthlyMessagesWordCount(int $year, int $month, int $chatwootUserId): int
    {
        return $this->baseMessageQuery($year, $month, $chatwootUserId)
            ->get()
            ->sum(fn ($message) => Str::wordCount($message->content ?? ''));
    }

    public function getMonthlyWorkingTenthOfAMonth(int $year, int $month, int $chatwootUserId): int
    {
        $messages = $this->baseMessageQuery($year, $month, $chatwootUserId)
            ->get(['created_at']);

        $tenthOfMonthActivity = [0, 0, 0];

        foreach ($messages as $message) {
            $dayOfMonth = $message['created_at']->day;

            if ($dayOfMonth >= 1 && $dayOfMonth <= 10) {
                $tenthOfMonthActivity[0] = 1;
            } elseif ($dayOfMonth >= 11 && $dayOfMonth <= 20) {
                $tenthOfMonthActivity[1] = 1;
            } else {
                $tenthOfMonthActivity[2] = 1;
            }
        }

        return array_sum($tenthOfMonthActivity);
    }

    public function getMonthlyWorkingDays(int $year, int $month, int $chatwootUserId): int
    {
        $days = $this->baseMessageQuery($year, $month, $chatwootUserId)
            ->distinct()
            ->pluck('created_at')
            ->map(fn ($createdAt) => $createdAt->startOfDay());

        return $days->unique()->count();
    }

    public function getMonthlyWorkingMinutes(int $year, int $month, array $intervals, int $chatwootUserId): Collection
    {
        sort($intervals);

        $messageTimes = $this->baseMessageQuery($year, $month, $chatwootUserId)
            ->orderBy('created_at')
            ->pluck('created_at');

        return $this->calculateTimeWindows($messageTimes, $intervals);
    }

    public function getMonthlyResponseTimeStats(int $year, int $month, array $intervals, int $chatwootUserId): Collection
    {
        sort($intervals);

        $messages = Message::forYearAndMonth($year, $month)
            ->public()
            ->senderUserOrContact($chatwootUserId)
            ->orderBy('created_at', 'asc')
            ->get(['created_at', 'conversation_id', 'sender_type', 'sender_id']);

        $timeDiffs = $this->calculateTimeDiffsByConversation($messages, $chatwootUserId);

        return $this->calculateTimeIntervals($timeDiffs, $intervals);
    }

    public function getMonthlyConversationsCount(int $year, int $month, int $chatwootUserId): int
    {
        return $this->baseMessageQuery($year, $month, $chatwootUserId)
            ->distinct('conversation_id')
            ->count('conversation_id');
    }

    // Invoice statistics
    public function getMonthlyInvoicesAsAgent(int $year, int $month, int $chatwootUserId): Collection
    {
        $invoices = Invoice::where('chatwoot_agent_id', $chatwootUserId)
            ->forYearAndMonth($year, $month)
            ->get();

        return $invoices ? $this->summarizeInvoices($invoices) : collect();
    }

    public function getMonthlyInvoicesAsConversationParticipant(int $year, int $month, int $chatwootUserId): Collection
    {
        // Retrieve the conversation IDs
        $conversationIds = Message::senderUser($chatwootUserId)
            ->forYearAndMonth($year, $month)
            ->distinct()
            ->pluck('conversation_id');

        // If there are no conversation IDs, return an empty collection
        if ($conversationIds->isEmpty()) {
            return collect();
        }

        // Retrieve the invoices based on the conversation IDs
        $invoices = Invoice::whereIn('chatwoot_conversation_id', $conversationIds)->get();

        return $invoices ? $this->summarizeInvoices($invoices) : collect();
    }

    // Helper methods
    private function calculateTimeWindows(Collection $messageTimes, array $intervals): Collection
    {
        $stats = collect();
        foreach ($intervals as $minutes) {
            $totalWindows = 0;
            $currentWindowEnd = null;

            foreach ($messageTimes as $messageTime) {
                if ($currentWindowEnd === null) {
                    $currentWindowEnd = $messageTime->copy()->addMinutes($minutes);
                    $totalWindows++;
                } elseif ($messageTime->greaterThan($currentWindowEnd)) {
                    $currentWindowEnd = $messageTime->copy()->addMinutes($minutes);
                    $totalWindows++;
                }
            }

            $stats->put("{$minutes}", $totalWindows * $minutes);
        }

        return $stats;
    }

    private function calculateTimeDiffsByConversation(Collection $messages, int $chatwootUserId): Collection
    {
        $timeDiffs = collect();

        $messages->groupBy('conversation_id')->each(function ($conversation) use ($timeDiffs, $chatwootUserId) {
            $previousMessage = null;

            foreach ($conversation as $message) {
                if ($previousMessage && $previousMessage->sender_type === 'Contact' && $message->sender_type === 'User' && $message->sender_id === $chatwootUserId) {
                    $diffInMinutes = $previousMessage->created_at->diffInMinutes($message->created_at);
                    if ($diffInMinutes > 0) {
                        $timeDiffs->push($diffInMinutes);
                    }
                }
                $previousMessage = $message;
            }
        });

        return $timeDiffs;
    }

    private function calculateTimeIntervals(Collection $timeDiffs, array $intervals): Collection
    {
        $stats = collect();
        $previousInterval = 0;

        foreach ($intervals as $interval) {
            $stats->put('mniej niż '.CarbonInterval::minutes($interval)->forHumans(), $timeDiffs->filter(fn ($diff) => $diff > $previousInterval && $diff <= $interval)->count());
            $previousInterval = $interval;
        }

        $stats->put('więcej niż '.CarbonInterval::minutes(end($intervals))->forHumans(), $timeDiffs->filter(fn ($diff) => $diff > end($intervals))->count());

        return $stats;
    }

    private function summarizeInvoices(Collection $invoices): Collection
    {
        return $invoices->groupBy('currency')->map(function ($groupedByCurrency) {
            return $groupedByCurrency->groupBy('status')->map(function ($groupedByStatus) {
                return $groupedByStatus->sum('total');
            });
        });
    }
}
