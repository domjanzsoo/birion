@props([
    'id'                    => '',
    'maxWidth'              => null,
    'type'                  => null,
    'title'                 => '',
    'content'               => '',
    'confirmButtonTitle'    => 'Accept',
    'confirmButtonIcon'     => null
])

@php
$id = $id ?? md5($attributes->wire('model'));

$maxWidth = [
    'sm' => 'sm:max-w-sm',
    'md' => 'sm:max-w-md',
    'lg' => 'sm:max-w-lg',
    'xl' => 'sm:max-w-xl',
    '2xl' => 'sm:max-w-2xl',
][$maxWidth ?? '2xl'];
@endphp

<div
    x-data="{ title: '', content: '', type: '{{ $type }}', show: false, id: '{{ $id }}' }" 
    @open-modal.window="show = true"
    x-on:close.stop="show = false"
    x-on:keydown.escape.window="show = false"
    x-show="show"
    id="{{ $id }}"
    class="jetstream-modal fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50"
    style="display: none;"
>
    <div x-show="show" class="fixed inset-0 transform transition-all" @click="show = false" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0">
        <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
    </div>

    <div x-show="show" class="mb-6 bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full {{ $maxWidth }} sm:mx-auto"
        x-trap.inert.noscroll="show"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
        <!-- Confirmation Modal Content -->
        <div class="bg-white px-4 pt-5 sm:p-6">
            <div x-show="type === 'confirmation'" class="sm:flex sm:items-start">
                <div class="mx-auto shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                    <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                </div>

                <div class="mt-3 text-center sm:mt-0 sm:ms-4 sm:text-start">
                    <h3 class="text-lg font-medium text-gray-900"> {{ $title }}</h3>

                    <div class="px-6 py-4">
                        <div class="mt-4 text-sm text-gray-dark text-center mb-5"> 
                            {{ $content }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex flex-row justify-end w-full bg-gray-light text-end rounded-b-lg p-3">
            <x-button @click="show = false" class="bg-gray-dark">
                {{ __('Cancel') }}
            </x-button>
            
            <x-button 
                x-data
                @click="() => {
                    console.log(id)
                    $dispatch(id);
                    show = false;
                }"
                class="bg-red ml-2"
            >
                {{ __($confirmButtonTitle) }}

                @if (!empty($confirmButtonIcon))
                    <x-icon name="{{ $confirmButtonIcon }}"></x-icon>
                @endif
            </x-button>
        </div>
    </div>
</div>
