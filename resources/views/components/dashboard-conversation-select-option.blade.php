<div class="flex flex-col gap-y-2 mt-4">
    <!-- First Row: Badge and Inbox Name -->
    <div class="flex items-center gap-x-2">
        <div class="flex-shrink-0">
            @include('vendor.filament.components.badge', [
                'slot' => $conversation->id,
            ])
        </div>
        <div class="text-base font-semibold">
            {{ $conversation->inbox->name }} ({{ $conversation->account->name }})
        </div>
    </div>

    <!-- Second Row: Last Activity Icon and Last Activity -->
    <div class="flex items-center gap-x-2">
        <div class="w-6">
            @include('vendor.filament.components.icon', ['icon' => 'heroicon-o-chat-bubble-left-ellipsis', 'class' => 'h-5 w-5 text-gray-600'])
        </div>
        <div class="text-sm text-gray-600">{{ $conversation->last_activity_at->diffForHumans() }}</div>
    </div>

    <!-- Third Row: Created At Icon and Created At -->
    <div class="flex items-center gap-x-2">
        <div class="w-6">
            @include('vendor.filament.components.icon', ['icon' => 'heroicon-o-user-plus', 'class' => 'h-5 w-5 text-gray-600'])
        </div>
        <div class="text-sm text-gray-600">{{ $conversation->created_at->diffForHumans() }}</div>
    </div>
</div>
