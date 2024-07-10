@props(['information', 'label', 'entity', 'refreshEvent'])

<style>
[x-cloak] { display: none !important }
</style>

<div
    wire:key="{{ rand() }}"
    x-data="{
        tooltipContent: '{{ $information }}',
        label: '{{ $label }}',
        loading: true,
        entity: {{ json_encode($entity) }},
        refreshEvent: '{{ $refreshEvent }}',
        displayInfo: false,
        replaceInformationData() {
            let attributePlaceholderMatches = this.label.match(/\[([^\][]*)]/g) || [];

            attributePlaceholderMatches.forEach(match => {
                this.label = this.label.replace(match, this.entity[match.substring(1, match.length - 1)]);
            });
        },
        init() {
            this.replaceInformationData();

            Livewire.on(this.refreshEvent, () => {
                this.entity = {{ json_encode($entity) }}

                this.replaceInformationData();
            });

            this.loading = false;
        }
    }"
    x-init="init()"
    x-on:mouseleave="displayInfo = false"
    class="flex"
>
    <span x-show="!loading" x-text="label"></span>
    <span x-show="loading">loading...</span>
    <div class="relative">
        <span x-on:mouseover="() => {
            if (entity[tooltipContent].length > 0) {
                displayInfo = true;
            }
        }">
            <x-icon name="info" />
        </span>
        <div x-cloak x-show="displayInfo" style="top: 100%" class="p-4 absolute bg-gray-dark text-white w-36 z-40">
            {{ $entity->{$information} }}
        </div>
    </div>
</div>