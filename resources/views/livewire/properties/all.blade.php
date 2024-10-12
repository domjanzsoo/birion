<div>
<x-form-section>
  <x-slot name="title">
    {{ __('properties.properties') }}
  </x-slot>
  <x-slot name="description">
    {{ __('properties.properties_full') }}
  </x-slot>
  <x-slot name="additional">
    <div class="w-92">
      <x-search entity="property" class="w-full"/>
    </div>
    @foreach ($filters as $filter => $config)
      <div class="flex flex-wrap w-92">
        <div class=" text-center flex-1">
          <x-label class="pt-4 mr-2" for="{{ $filter }}" value="{{ $config['label'] }}"/>
        </div>
        @if (array_key_exists('options', $config))
          <div class="flex-1">
            <x-input wire:change="filter('{{ $filter }}', $event.target.value)" wire:key="$filter" type="select" class="mt-1 text-sm">
              <x-slot name="options">
                  @foreach ($config['options'] as $name => $value)
                      <option wire:key="option-{{$name}}" value="{{ $value }}" class="text-sm">{{ $name }}</option>
                  @endforeach
              </x-slot>
            </x-input>
          </div>
        @elseif (array_key_exists('range', $config))
          <div class="flex-1 relative">
            <x-input wire:change="filter('{{ $filter }}', $event.target.value)" wire:key="$filter" type="range" class="mt-1 text-sm" />
          </div>
        @endif
      </div>
    @endforeach
  </x-slot>
  <x-slot name="list">
    <x-grid-paginated :items="$properties" wrapperStyle="height: 980px">
      <div style="height: 400px" class="bg-white border-solid border-2 border-gray px-2 py-2 rounded-sm">
        <div
          x-data="{ imgUrl: null, header: '' }"
          x-init="() => {
            const address = elmData.address;
            imgUrl = elmData.images ? elmData.images.filter(image => image.main_image)[0]?.file_route : '';

            if (address.municipality) {
              header += address.municipality + ', ';
            } else if (address.municipality_sub_division) {
             header += address.municipality_sub_division + ', ';
            } else if (address.municipality_secondary_sub_division) {
              header += address.municipality_secondary_sub_division + ', ';
            }

            if (address.house_number) {
              header += address.house_number + ' ';
            }

            header += address.street;
          }"
          class="flex">
          <div :style="`background-image: url(${imgUrl})`" class="inline-block w-20 h-20 bg-center bg-cover bg-no-repeat"></div>
          <div class="ml-2 text-sm mt-2">
            <b x-text="header"></b>
          </div>
        </div>
        <div class="w-full h-24">
          <div class="mt-3 px-3 pt-4 text-sm relative">
            <div x-text="elmData.description.substring(0, 80) + '...'"></div>
            <div style="width: 97%" class="h-4 bg-white absolute bottom-0 opacity-80"></div>
          </div>
        </div>
        <div class="mt-6 grid grid-cols-3 gap-2 w-full">
          <template x-for="(image, index) in elmData.images">
            <div
              x-show="index < 6" :style="`background-image: url(${image.file_route})`" class="h-20 bg-center bg-cover bg-no-repeat">
            </div>
          </template>
        </div>
      </div>
    </x-grid-paginated>
  </x-slot>
</x-form-section>
</div>

