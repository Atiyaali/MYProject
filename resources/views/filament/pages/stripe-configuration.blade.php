<x-filament-panels::page>
    {{-- <x-filament-panels::form wire:submit="updateStripe"> --}}
        <x-filament::section>
            <div>
                {{ $this->form }}
            </div>



        </x-filament::section>
        <div class="flex flex-wrap items-center gap-4 justify-start">
            <x-filament::button type="submit" wire:click="updateStripe">
                Update
            </x-filament::button>
            <x-filament::button type="button" color="secondary">
                Cancel
            </x-filament::button>
        </div>
    {{-- </x-filament-panels::form> --}}
</x-filament-panels::page>
