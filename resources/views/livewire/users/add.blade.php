<x-form-section submit="addUser">
    <x-slot name="title">
        {{ __('users.add') }}
    </x-slot>
    <x-slot name="description">
        {{ __('users.add_full') }}
    </x-slot>

    <x-slot name="form">
        <div>
            <x-label for="full_name" value="{{ __('users.full_name') }}" />
            <x-input id="full_name" type="text" class="mt-1 w-72" wire:model="state.full_name"/>
            <x-input-error for="state.full_name" class="mt-2" />
        </div>
        <div>
            <x-label for="email" value="{{ __('users.email') }}" />
            <x-input id="email" type="text" class="mt-1 w-72" wire:model="state.email" />
            <x-input-error for="state.email" class="mt-2" />
        </div>
        <div>
            <x-label for="password" value="{{ __('users.password') }}" />
            <x-input id="password" type="password" class="mt-1 w-72" wire:model.live="state.password" />
            <x-input-error for="state.password" class="mt-2 form-control" />
        </div>
        <div>
            <x-label for="confirm_password" value="{{ __('users.confirm_password') }}"/>
            <x-input id="confirm_password" type="password" class="mt-1 w-72" wire:model.live="state.password_confirmation" />
            <x-input-error for="state.password_confirmation" class="mt-2" />
        </div>
        <div class="col-span-2 flex">
            @if ($state['profile_picture'])
                Photo Preview:
                <img src="{{ $state['profile_picture']->temporaryUrl() }}">
            @endif
            <div>
                <x-label for="profile_picture" value="{{ __('users.profile_picture') }}"/>
                <x-input id="profile_picture" type="file" class="mt-1 w-72" wire:model="state.profile_picture" />
                <x-input-error for="state.profile_picture" class="mt-2" />
            </div>
        </div>
        <div class="flex flex-row justify-end col-span-2 pr-5 mt-6">
            <x-button @click="show = false" class="bg-gray-dark">
                {{ __('Cancel') }}
            </x-button>
            <x-button x-data x-on:click="addUser" class="bg-blue ml-2">
                {{ __('Submit') }}
            </x-button>
        </div>
    </x-slot>
</x-form-section>
