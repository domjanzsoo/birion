<div>
    <x-app-layout>
        <x-slot name="header">
            <h2 id="permissions-header" class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('properties.properties') }}
            </h2>
        </x-slot>

        <div>
            @canAccess('"add_properties"')
                <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                    @livewire('properties.add')
                </div>
            @endcanAccess

            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                @canAccess("['view_properties', 'add_property', 'edit_property']")
                    @livewire('properties.all')
                @endcanAccess
            </div>
        </div>
        <x-toaster></x-toaster>
    </x-app-layout>
</div>