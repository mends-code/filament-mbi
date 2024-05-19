<div class="flex flex-col gap-y-2 mt-4">
    <!-- First Row: Badge and Inbox Name -->
    <div class="flex items-center gap-x-2">
        <div class="flex-shrink-0">
            @include('vendor.filament.components.badge', [
                'slot' => $conversation->id ?? 'N/A',
            ])
        </div>
        <div class="text-base font-semibold">
            {{ $conversation->inbox->name ?? 'Skrzynka odbiorcza'}} ({{ $conversation->account->name ?? __('Konto') }})
        </div>
    </div>

    <!-- Second Row: Last Activity Icon and Last Activity -->
    <div class="flex items-center gap-x-2">
        <div class="">
            @include('vendor.filament.components.icon', [
                'icon' => 'heroicon-o-chat-bubble-left-ellipsis',
                'class' => 'h-5 w-5 text-gray-600',
            ])
        </div>
        <div class="text-sm text-gray-600">
            @if($conversation)
                {{ $conversation->last_activity_at->diffForHumans() }}
            @else
                {{ 'Data ostatniej aktywności' }}
            @endif
        </div>
    </div>

    <!-- Third Row: Created At Icon and Created At -->
    <div class="flex items-center gap-x-2">
        <div class="">
            @include('vendor.filament.components.icon', [
                'icon' => 'heroicon-o-user-plus',
                'class' => 'h-5 w-5 text-gray-600',
            ])
        </div>
        <div class="text-sm text-gray-600">
            @if($conversation)
                {{ $conversation->created_at->diffForHumans() }}
            @else
                {{ 'Data pierwszego kontaktu' }}
            @endif
        </div>
    </div>
</div>
