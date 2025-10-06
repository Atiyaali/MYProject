<x-filament::page>
    <div class="space-y-6">

        <h2 class="text-2xl font-bold">Form Builder</h2>

        <form wire:submit.prevent="submit">
            {{ $this->form }}

            <div class="mt-4">
                <x-filament::button type="submit">
                    Save Form
                </x-filament::button>
            </div>
        </form>

    </div>
</x-filament::page>
