<?php

namespace App\Models\Chatwoot;

use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class User extends BaseModel
{
    protected $table = 'mbi_chatwoot.users';

    // We will not use this relationship directly
    public function messages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function monthlyMessagesCount(int $year, int $month): int
    {
        return Message::forYearAndMonth($year, $month)
            ->senderUser($this->id)
            ->count();
    }

    public function monthlyMessagesWordCount(int $year, int $month): int
    {
        return Message::forYearAndMonth($year, $month)
            ->senderUser($this->id)
            ->get()
            ->sum(function ($message) {
                return Str::wordCount($message->content ?? '');
            });
    }

    public function monthlyWorkingDays(int $year, int $month): int
    {
        $messages = Message::forYearAndMonth($year, $month)
            ->senderUser($this->id)
            ->get()
            ->map(function ($message) {
                return Carbon::parse($message->created_at)->startOfDay()->format('Y-m-d');
            });

        return $messages->unique()->count();
    }

    public function monthlyWorkingHours(int $year, int $month): int
    {
        $messages = Message::forYearAndMonth($year, $month)
            ->senderUser($this->id)
            ->get()
            ->map(function ($message) {
                return Carbon::parse($message->created_at)->startOfHour()->format('Y-m-d H:00');
            });

        return $messages->unique()->count();
    }

    public function monthlyWorkingMinutes(int $year, int $month, int $minutes): int
    {
        // Ensure $minutes is a divisor or multiple of 60
        if (60 % $minutes !== 0 && $minutes % 60 !== 0) {
            throw new \InvalidArgumentException('Minutes must be a divisor or multiple of 60');
        }

        $messages = Message::forYearAndMonth($year, $month)
            ->senderUser($this->id)
            ->select('created_at')
            ->orderBy('created_at')
            ->get();

        $totalWindows = 0;
        $currentWindowEnd = null;

        foreach ($messages as $message) {
            $messageTime = Carbon::parse($message->created_at);

            if ($currentWindowEnd === null) {
                // Initialize the first window
                $currentWindowEnd = $messageTime->copy()->addMinutes($minutes);
                $totalWindows++;
            } elseif ($messageTime->greaterThan($currentWindowEnd)) {
                // The message is outside the current window
                $currentWindowEnd = $messageTime->copy()->addMinutes($minutes);
                $totalWindows++;
            }
        }

        return $totalWindows * $minutes;
    }

}
