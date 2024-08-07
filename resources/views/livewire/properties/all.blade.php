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
        <div
          x-data="{ imgUrl: null }"
          x-init="() => {
            imgUrl = elmData.images ? elmData.images.filter(image => image.main_image)[0]?.file_route : '';
            console.log(imgUrl)
          }"
          class="flex">
          <img x-bind:src="`${imgUrl}`" alt="">
          <div :style="`background-image: url(/${imgUrl})`" class="inline-block w-16 h-16 bg-center bg-cover bg-no-repeat"></div>
        </div>
        <div class="mt-3 px-3 pt-4 text-sm" x-text="elmData.description"></div>
      </div>
    </x-grid-paginated>
  </x-slot>
</x-form-section>

