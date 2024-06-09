<x-form-section submit="addPermission">
    <x-slot name="title">
        {{ __('permissions.add') }}
    </x-slot>
    <x-slot name="description">
        {{ __('permissions.add_full') }}
    </x-slot>

    <x-slot name="form">
        <div class="col-span-6 sm:col-span-4">
            <x-label for="permission_name" value="{{ __('Permission Name') }}" />
            <x-input id="permission_name" type="text" class="mt-1 block w-full" wire:model="state.permission_name" />
            <x-input-error for="state.permission_name" class="mt-2" />
        </div>
    </x-slot>
</x-form-section>
