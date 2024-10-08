@props(['submit'])

<div {{ $attributes->merge(['class' => 'md:grid md:grid-cols-4 md:gap-4']) }}>
    <x-section-title>
        <x-slot name="title">{{ $title }}</x-slot>
        <x-slot name="description">{{ $description }}</x-slot>
        @if (isset($additional))
            <x-slot name="additional">{{ $additional }}</x-slot>
        @endif
    </x-section-title>

    <div class="mt-5 md:mt-0 md:col-span-3">
        @if (isset($form))
            <form wire:submit="{{ $submit }}" x-on:submit="() => $dispatch('form-submit')">
                <div class="px-4 py-5 bg-white sm:p-6 shadow {{ isset($actions) ? 'sm:rounded-tl-md sm:rounded-tr-md' : 'sm:rounded-md' }}">
                    <div class="grid grid-cols-2 gap-3">
                        {{ $form }}
                    </div>
                </div>

                @if (isset($actions))
                    <div class="flex items-center justify-end px-4 py-3 bg-gray-50 text-end sm:px-6 shadow sm:rounded-bl-md sm:rounded-br-md">
                        {{ $actions }}
                    </div>
                @endif
        </form>
        @endif

        @if (isset($list))
            <div class="px-4 py-5 bg-white sm:p-6 shadow sm:rounded-md">
                <div class="w-full">
                    {{ $list }}
                </div>
            </div>
        @endif
    </div>
</div>
