<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Permissions') }}
        </h2>
    </x-slot>

    <div>
    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            @livewire('permissions.add')
        </div>

        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            @livewire('permissions.all')
        </div>
    </div>
</x-app-layout>