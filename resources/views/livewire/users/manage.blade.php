<div>
    <x-app-layout>
        <x-slot name="header">
            <h2 id="permissions-header" class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('users.users') }}
            </h2>
        </x-slot>

        <div>
            @canAccess('"add_user"')
                <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                    @livewire('permissions.add')
                </div>
            @endcanAccess

            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                @canAccess("['view_users', 'add_user', 'edit_user']")
                    @livewire('users.all')
                @endcanAccess
            </div>
        </div>
        <x-toaster></x-toaster>
    </x-app-layout>
</div>