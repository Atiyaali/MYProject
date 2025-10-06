<x-filament-panels::page>

    <x-filament::card>
        <form wire:submit.prevent="import" enctype="multipart/form-data">
            <div class="space-y-6">

                <x-filament::input.wrapper>
                    <x-filament::input type="file" wire:model="file" required />
                </x-filament::input.wrapper>
                <x-filament::input.wrapper>
                    @if ($this->batch !== null)
                        @php
                            $batch = App\Models\Batch::find($this->batch);

                        @endphp
                        <x-filament::input. wire:model.defer="batch" label="Batch Name" placeholder="Enter batch name"
                            type="hidden" readonly />

                        <x-filament::input value="{{ ucfirst($batch->name) }}" label="Batch Name"
                            placeholder="Enter batch name" type="text" readonly />
                    @else
                        <x-filament::input.select wire:model="batch" required>
                            <option value="">--Select Batch--</option>
                            @foreach (App\Models\Batch::all() as $batch)
                                <option value="{{ $batch->id }}">{{ ucfirst($batch->name) }}</option>
                            @endforeach

                        </x-filament::input.select>
                    @endif
                </x-filament::input.wrapper>
                                     {{-- Loader --}}

                <div class="text-right">
                       <x-filament::button type="submit">
                        <div wire:loading wire:target="file" class="mt-4">
                            <x-filament::loading-indicator class="h-5 w-5" />
                        </div>Import        <div wire:loading wire:target="import" class="">
    <x-filament::loading-indicator class="w-5 h-5 text-primary-500" />
</div>

                    </x-filament::button>

                </div>

            </div>


        </form>
    </x-filament::card>

    <div>
        {{ $this->table }}
    </div>
</x-filament-panels::page>
