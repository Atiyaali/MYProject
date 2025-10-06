<x-filament-panels::page>
    <x-filament::section>
        <div>
            {{ $this->form }}
        </div>
    </x-filament::section>

    <div class="flex flex-wrap items-center gap-4 justify-start">
        <x-filament::button wire:click="refreshPage">
            Refresh Page
        </x-filament::button>
        <x-filament::button type="submit" wire:click="updateSettings">
            Update
        </x-filament::button>
        <x-filament::button type="button" color="secondary">
            Cancel
        </x-filament::button>
    </div>
</x-filament-panels::page>
