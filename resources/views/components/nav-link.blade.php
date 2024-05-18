@props(['active', 'dropdown' => false, 'dropdownHeader' => '', 'dropdownElms' => []])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-indigo-400 text-sm font-medium leading-5 text-gray-900 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out';
@endphp

@if($dropdown)
    <div>
        <x-dropdown align="left"  contentTopMargin="4" triggerClass="{{ $classes . ' cursor-pointer z-40' }}" dropdownClasses="-mt-5">
            <x-slot name="trigger">
                <span class="inline-flex mt-5 pb-5">
                        <span>{{ $slot }}</span>
                        <span class="mt-1 ml-1">
                            <x-icon  class="mt-2" name="chevron-down" />
                        </span>
                </span>
            </x-slot>

            <x-slot name="content">
                <div class="z-50 py-2 text-gray-400">
                    @foreach($dropdownElms as $elm => $route) 
                        <x-dropdown-link href="{{ $route }}">
                                {{ __(ucfirst($elm)) }}
                        </x-dropdown-link>
                    @endforeach
                </div>
            </x-slot>
        </x-dropdown>
    </div>
@else
    <a {{ $attributes->merge(['class' => $classes]) }} >
        {{ $slot }}
    </a>
@endif
