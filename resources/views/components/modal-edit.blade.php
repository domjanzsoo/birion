@props([
    'id'            => '',
    'maxWidth'      => null,
    'entity'        => null,
    'showSubmit'    => false
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
    x-data="{ show: false, id: '{{ $id }}', entity:'{{ $entity }}' }" 
    x-init="
        console.log('edit modal init');
        Livewire.on(entity + '-edited', payload => {
            if(payload[0].entity === entity) {
                show = false;
            }
        });
    "
    @open-edit-modal.window="show = true"
    x-on:close.stop="show = false"
    x-on:keydown.escape.window="show = false"
    x-show="show"
    id="{{ $id }}"
    class="jetstream-modal fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50"
    style="display: none;"
>
    <div x-show="show" 
        class="fixed inset-0 transform transition-all" @click="show = false" 
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
    </div>

    <div x-show="show" class="mb-6 bg-white rounded-lg shadow-xl transform transition-all sm:w-full {{ $maxWidth }} sm:mx-auto"
        x-trap.inert.noscroll="show"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
        <div class="bg-white px-4 py-6 pt-5">
            <div class="mt-4 text-sm text-gray-dark text-center mb-5"> 
                {{ $form }}
            </div>
        </div>
        <div class="flex flex-row justify-end w-full bg-gray-light text-end rounded-b-lg p-3">
            <x-button @click="() => {
                    $dispatch('modal-closed');
                    show = false;
                }" 
                class="bg-gray-dark">
                {{ __('Cancel') }}
            </x-button>
            @if ($showSubmit)
                <x-button x-data x-on:click="$dispatch('save-modal-edit-{{ $entity }}')" class="bg-blue ml-2">
                    {{ __('Submit') }}
                </x-button>
            @endif
        </div>
    </div>
</div>
