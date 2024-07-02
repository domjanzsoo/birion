@props(['information', 'label', 'entity', 'refreshEvent'])

<div
    wire:key="{{ rand() }}"
    x-data="{
        tooltipContent: '{{ $information }}',
        label: '{{ $label }}',
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
                console.log('tooltip to refresh');
                console.log(this.entity);

                this.replaceInformationData();
            });
        }
    }"
    x-init="init()"
    x-on:mouseleave="displayInfo = false"
    class="flex"
>
    <span x-text="label"></span>
    <div class="relative">
        <span x-on:mouseover="() => {
            if (entity[tooltipContent].length > 0) {
                displayInfo = true;
            }
        }">
            <x-icon name="info" />
        </span>
        <div x-show="displayInfo" style="top: 100%" class="p-4 absolute bg-gray-dark text-white w-36 z-40">
            {{ $entity->{$information} }}
        </div>
    </div>
</div>