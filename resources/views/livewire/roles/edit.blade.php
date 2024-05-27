<form wire:submit="save">
    <div class="md:grid md:grid-cols-6 md:gap-6">
        <div class="col-span-3 sm:col-span-3 text-left">
            <x-label for="state.role_name" value="{{ __('Role Name') }}" />
            <x-input id="role_name" type="text" class="mt-1 block w-full" wire:model="state.role_name" />
            <x-input-error for="state.role_name" class="mt-2" />
        </div>
        <div class="col-span-3 sm:col-span-3 text-left">
            <x-label for="role_permissions" value="{{ __('Role Permissions') }}" />
            <x-multi-select :options="$permissions" event="role-permissions" :selected="$state['selected_permissions']"/>
            <x-input-error for="role_permissions" class="mt-2" />
        </div>
        <div class="mt-6 col-span-6 sm:col-span-6 flex justify-end">
            <x-button type="submit" class="bg-blue mb-3">
                {{ __('Submit') }}
            </x-button>
        </div>
    </div>
</form>

