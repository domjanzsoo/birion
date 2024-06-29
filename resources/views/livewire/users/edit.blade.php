<form test-id="edit_users" wire:submit="save" class="w-full">
    <div class="grid grid-cols-2 gap-3">
        <div class="grid grid-cols-3">
            <div class="mt-3">
                <x-profile-img :changable="true" wire:model="state.profile_picture" :imgUrl="isset($state['profile_picture']) && method_exists($state['profile_picture'], 'temporaryUrl') ? 
                $state['profile_picture']->temporaryUrl() : 
                $user->profile_photo_path ?? asset(config('filesystems.user_profile_image_path') . '/user.png')" size="16"/>
            </div>
            <div class="col-start-2 col-span-2 text-left">
                <x-label for="state.full_name" value="{{ __('users.full_name') }}" />
                <x-input id="full_name" type="text" class="mt-1 w-full" wire:model="state.full_name"/>
                <x-input-error for="state.full_name" class="mt-2" />
            </div>
        </div>
        <div class="text-left">
            <x-label for="email" value="{{ __('users.email') }}" />
            <x-input id="email" type="text" class="mt-1 w-72" wire:model="state.email" />
            <x-input-error for="state.email" class="mt-2" />
        </div>
        <div class="text-left">
            <x-label for="password" value="{{ __('users.password') }}" />
            <x-input id="password" type="password" class="mt-1 w-full" wire:model.live="state.password" />
            <x-input-error for="state.password" class="mt-2 form-control" />
        </div>
        <div class="text-left">
            <x-label for="confirm_password" value="{{ __('users.confirm_password') }}"/>
            <x-input id="confirm_password" type="password" class="mt-1 w-72" wire:model.live="state.password_confirmation" />
            <x-input-error for="state.password_confirmation" class="mt-2" />
        </div>
        <div class="text-left">
            <x-label for="state.permissions" value="{{ __('users.user_permissions') }}" />
            <x-multi-select :options="$permissions" event="user-permissions" :selected="$state['selected_permissions']" />
            <x-input-error for="state.permissions" class="mt-2" />
        </div>
        <div class="text-left">
            <x-label for="state.roles" value="{{ __('users.user_roles') }}" />
            <x-multi-select :options="$roles" event="user-roles" />
            <x-input-error for="state.roles" class="mt-2" />
        </div>
    </div>
</form>
