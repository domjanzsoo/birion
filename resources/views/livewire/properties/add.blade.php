<x-form-section submit="addProperty">
    <x-slot name="title">
        {{ __('properties.add') }}
    </x-slot>
    <x-slot name="description">
        {{ __('properties.add_full') }}
    </x-slot>

    <x-slot name="form">
        <div class="col-span-2">
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
            <x-input id="country" type="text" class="mt-1 w-full" wire:model="state.country" />
            <x-input-error for="state.country" class="mt-2" />
        </div>
        <div class="col-span-2 grid grid-cols-3 gap-2">
            <div>
                <x-label for="heating" value="{{ __('properties.heating_type') }}"/>
                <x-input id="heating" type="select" class="mt-1 w-full text-sm" wire:model="state.heating">
                    <x-slot name="options">
                        @foreach($heatingOptions as $option)
                            <option class="text-sm" value="{{ $option }}">{{ $option }}</option>
                        @endforeach
                    </x-slot>
                </x-input>
                <x-input-error for="state.heating" class="mt-2" />
            </div>
            <div>
            <x-label for="room_number" value="{{ __('properties.room_number') }}"/>
                <x-input id="room_number" type="select" class="mt-1 w-full text-sm" wire:model="state.room_number">
                    <x-slot name="options">
                        @for($i = 1; $i <= $roomNumberOptions; $i++)
                            <option class="text-sm" value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </x-slot>
                </x-input>
                <x-input-error for="state.room_number" class="mt-2" />
            </div>
            <div>
                <x-label for="size" value="{{ __('properties.size') }}"/>
                <x-input id="size" type="number" class="mt-1 w-full" wire:model="state.size" />
                <x-input-error for="state.size" class="mt-2" />
            </div>
        </div>
        <div class="col-span-2">
            <x-label for="description" value="{{ __('properties.description') }}"/>
            <x-input id="description" type="text-area" class="mt-1 w-full" wire:model="state.description" />
            <x-input-error for="state.description" class="mt-2" />
        </div>
        <div class="col-span-2 mt-3">
            <x-label for="photos" value="{{ __('properties.photos') }}"/>
            <x-drag-and-drop-upload wire:model="state.profile_picture" :multi="true" class="w-full" fileType='profile-picture' />
            <x-input-error for="state.profile_picture" class="mt-2" />
        </div>
        <div class="flex flex-row justify-end col-span-2 pr-5 mt-6">
            <x-button type="submit" class="bg-blue ml-2">
                {{ __('general.submit') }}
            </x-button>
        </div>
    </x-slot>
</x-form-section>
