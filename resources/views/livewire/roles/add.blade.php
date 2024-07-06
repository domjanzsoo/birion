<x-form-section submit="addRole">
    <x-slot name="title">
        {{ __('roles.add') }}
    </x-slot>
    <x-slot name="description">
        {{ __('roles.add_full') }}
    </x-slot>

    <x-slot name="form">
        <div class="col-span-6 sm:col-span-5">
            <x-label for="role_name" value="{{ __('roles.role_name') }}" />
            <x-input id="role_name" type="text" class="mt-1 block w-full" wire:model="state.role_name" />
            <x-input-error for="state.role_name" class="mt-2" />
        </div>
        <div class="col-span-6 sm:col-span-5">
            <x-label for="role_permissions" value="{{ __('roles.role_permissions') }}" />
            <x-multi-select :options="$permissions" event="role-permissions" />
            <x-input-error for="role_permissions" class="mt-2" />
        </div>
        <div class="mt-6 col-span-6 sm:col-span-6 flex justify-end">
            <x-button type="submit" class="bg-blue mb-3">
                {{ __('general.submit') }}
            </x-button>
        </div>
    </x-slot>
</x-form-section>
