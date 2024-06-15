<form test-id="edit_users" wire:submit="save" class="w-full">
    <div class="grid grid-cols-4 gap-1">
        <div class="py-3">
            <x-label for="state.user_name" value="{{ __('users.user_name') }}" />
        </div>
        <div class="col-span-3">
            <x-input id="user_name" type="text" class="pt-2 block w-full" wire:model="state.user_name" value="{{ $state['user_name'] }}"/>
            <x-input-error for="state.user_name" class="mt-2 text-left" />
        </div>   
    </div>
</form>
