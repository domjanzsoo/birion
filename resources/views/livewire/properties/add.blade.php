<x-form-section submit="addUser">
    <x-slot name="title">
        {{ __('properties.add') }}
    </x-slot>
    <x-slot name="description">
        {{ __('properties.add_full') }}
    </x-slot>

    <x-slot name="form">
        <div>
            <x-label for="address" value="{{ __('properties.address') }}" />
            <x-input id="address" type="text" class="mt-1 w-full" wire:model="state.address" />
            <x-input-error for="state.address" class="mt-2" />
        </div>
        <div>
            <x-label for="location" value="{{ __('properties.location') }}" />
            <x-input id="location" type="text" class="mt-1 w-full" wire:model="state.location" />
            <x-input-error for="state.location" class="mt-2 form-control" />
        </div>
        <div>
            <x-label for="country" value="{{ __('properties.country') }}"/>
            <x-input id="country" type="text" class="mt-1 w-full" wire:model.live="state.country" />
            <x-input-error for="state.country" class="mt-2" />
        </div>
        <div class="flex flex-row justify-end col-span-2 pr-5 mt-6">
            <x-button type="submit" class="bg-blue ml-2">
                {{ __('general.submit') }}
            </x-button>
        </div>
    </x-slot>
</x-form-section>
