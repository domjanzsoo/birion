<x-form-section submit="addProperty">
    <x-slot name="title">
        {{ __('properties.add') }}
    </x-slot>
    <x-slot name="description">
        {{ __('properties.add_full') }}
    </x-slot>

    <x-slot name="form">
        <div
            x-data="{ selectedAddressOption: null }"
            x-init="() => {
                let streetOnFocus = false;
                let currentSelectedOptionIndex = -1;
                let addressOptions = document.getElementsByClassName('address-option');

                document.getElementById('street').onfocus = e => {
                    streetOnFocus = true;
                };

                document.getElementById('street').onblur = e => {
                    streetOnFocus = false;
                    document.getElementById('address-option-' + currentSelectedOptionIndex).classList.remove('bg-gray-light');
                    document.getElementById('address-option-lister').classList.add('hidden');
                    currentSelectedOptionIndex = -1;
                };

                document.onkeydown = e => {
                    if (streetOnFocus) {
                        let currentSelection = document.getElementById('address-option-' + currentSelectedOptionIndex);

                        switch (e.keyCode) {
                            case 40:
                                document.getElementById('address-option-lister').classList.remove('hidden');
                                if (addressOptions.length > 0  && currentSelectedOptionIndex < addressOptions.length - 1) {
                                    if (currentSelection) {
                                        currentSelection.classList.remove('bg-gray-light');
                                    }

                                    currentSelectedOptionIndex ++;

                                    document.getElementById('address-option-' + currentSelectedOptionIndex).classList.add('bg-gray-light');
                                }
                                break;
                            case 38:
                                if (addressOptions.length > 0) {
                                    if (currentSelection) {
                                        currentSelection.classList.remove('bg-gray-light');
                                    }

                                    currentSelectedOptionIndex = currentSelectedOptionIndex <= 0 ? addressOptions.length - 1 : currentSelectedOptionIndex - 1;

                                    document.getElementById('address-option-' + currentSelectedOptionIndex).classList.add('bg-gray-light');
                                }
                                break;
                            case 27:
                                if (currentSelection) {
                                    currentSelection.classList.remove('bg-gray-light');
                                }

                                currentSelection = null;
                                currentSelectedOptionIndex = -1;
                                document.getElementById('address-option-lister').classList.add('hidden');
                                break;
                            case 13:
                                e.preventDefault();

                                if (currentSelection) {
                                    currentSelection.classList.remove('bg-gray-light');
                                }

                                if (currentSelectedOptionIndex > -1) {
                                    $dispatch('address-selected', { addressOptionIndex: currentSelectedOptionIndex })
                                }

                                currentSelection = null;
                                currentSelectedOptionIndex = -1;
                                document.getElementById('address-option-lister').classList.add('hidden');
                                break;
                            default:
                                return;
                        }
                    }
                }
            }"
            class="col-span-2 gap-3 grid grid-cols-5">
            <div>
                <x-label for="street_number" value="{{ __('properties.house_name_number') }}" />
                <x-input id="street_number" type="text" class="mt-1 w-full text-sm" wire:model="state.street_number" />
                <x-input-error for="state.street_number" class="mt-2" />
            </div>
            <div class="col-span-4">
                <x-label for="street" value="{{ __('properties.street') }}" />
                <x-input id="street" type="text" class="mt-1 w-full text-sm" wire:model.live="state.street" />
                @if (count($addressOptions) > 0)
                    <div class="border-2 border-gray-light rounded-b-lg border-separate" id="address-option-lister">
                        <ul>
                            @foreach ($addressOptions as $index => $option)
                                <li class="px-3 hover:bg-gray-light cursor-pointer text-sm address-option" id="address-option-{{$index}}" wire:click="handleStreetSelection('{{$index}}')">
                                    {{ $option->address->freeformAddress }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <x-input-error for="state.street" class="mt-2" />
            </div>
        </div>
        <div>
            <x-label for="location" value="{{ __('properties.location') }}" />
            <x-input id="location" type="text" class="mt-1 w-full text-sm" wire:model="state.location" />
            <x-input-error for="state.location" class="mt-2 form-control" />
        </div>
        <div>
            <x-label for="country" value="{{ __('properties.country') }}"/>
            <x-input id="country" type="text" class="mt-1 w-full text-sm" wire:model="state.country" />
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
                <x-label for="size">{!! __('properties.size') !!}</x-label>
                <x-input id="size" type="number" class="mt-1 w-full text-sm" wire:model="state.size" />
                <x-input-error for="state.size" class="mt-2" />
            </div>
        </div>
        <div class="col-span-2">
            <x-label for="description" value="{{ __('properties.description') }}"/>
            <x-input id="description" type="text-area" class="mt-1 w-full text-sm" wire:model="state.description" />
            <x-input-error for="state.description" class="mt-2" />
        </div>
        <div class="col-span-2 mt-3">
            <x-label for="pictures" value="{{ __('properties.photos') }}"/>
            <x-drag-and-drop-upload wire:model="state.pictures" :multi="true" class="w-full" fileType='property-picture' resetEvent='property-added' />
            <x-input-error for="state.pictures" class="mt-2" />
        </div>
        <div class="flex flex-row justify-end col-span-2 pr-5 mt-6">
            <x-button type="submit" class="bg-blue ml-2">
                {{ __('general.submit') }}
            </x-button>
        </div>
    </x-slot>
</x-form-section>
