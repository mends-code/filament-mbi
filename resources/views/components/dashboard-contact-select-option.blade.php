<div class="flex flex-col gap-y-2">
    <!-- First Row: Badge and Name -->
    <div class="flex items-center gap-x-2">
        <div class="flex-shrink-0">
            @include('vendor.filament.components.badge', [
                'slot' => $contact->id,
            ])
        </div>
        <div class="text-base font-semibold">{{ $contact->name }}</div>
    </div>

    <!-- Second Row: Email Icon and Email -->
    <div class="flex items-center gap-x-2">
        <div class="w-6">
            @include('vendor.filament.components.icon', ['icon' => 'heroicon-o-envelope', 'class' => 'h-5 w-5 text-gray-600'])
        </div>
        <div class="text-sm text-gray-600">{{ $contact->email }}</div>
    </div>

    <!-- Third Row: Phone Icon and Phone Number -->
    <div class="flex items-center gap-x-2">
        <div class="w-6">
            @include('vendor.filament.components.icon', ['icon' => 'heroicon-o-phone', 'class' => 'h-5 w-5 text-gray-600'])
        </div>
        <div class="text-sm text-gray-600">{{ $contact->phone_number }}</div>
    </div>
</div>
