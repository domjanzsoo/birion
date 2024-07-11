<x-form-section>
  <x-slot name="title">
    {{ __('properties.properties') }}
  </x-slot>
  <x-slot name="description">
    {{ __('properties.properties_full') }}
  </x-slot>
  <x-slot name="list">
    <x-grid-paginated :data="$properties">
      <div class="bg-white px-2 py-2">
        <div class="flex">
          <div :style="`background-image: url(${elmData.main_photo_path})`" class="inline-block w-16 h-16 bg-center bg-cover bg-no-repeat"></div>
          <div class="ml-5 mt-3 text-sm" x-text="elmData.address + ', ' + elmData.location + ', ' + elmData.country"></div>
        </div>
        <div class="mt-3 px-3 pt-4 text-sm" x-text="elmData.description"></div>
      </div>
    </x-grid-paginated>
  </x-slot>
</x-form-section>

