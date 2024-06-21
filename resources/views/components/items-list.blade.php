@props(['title', 'description', 'entity', 'items', 'deleteButtonAccess', 'extraInformation' => null, 'showEditSubmitButton' => false, 'withProfileImage' => false])

@php
  $dataProperty = $extraInformation ? $extraInformation['dataProperty'] : null;
  $extraDataComponent =  $extraInformation ? $extraInformation['component'] : null;
@endphp

<x-form-section>
    <x-slot name="title">
        {{ $title }}
    </x-slot>
    <x-slot name="description">
        {{ $description }}
    </x-slot>
    <x-slot name="list">
      <div x-data="{ itemsSelected: {}, entity: '{{ $entity }}' }" class="w-full" style="height: calc({{ $items->perPage() }} * 120px)">
        <ul role="list" class="divide-y divide-gray-100 w-full">
          @if ($items->total() === 0)
            <li> No {{ $entity }} found </li>
          @else
            <li class="flex justify-end">
              @canAccess(json_encode('delete_' . $entity))
                @if ($deleteButtonAccess)
                  <x-button x-data x-on:click="$dispatch('open-modal', {id: 'delete-' + entity})" class="bg-red mb-3">
                    {{ __('Delete') }}
                    <x-icon name="trash"></x-icon>
                  </x-button>
                @else
                  <x-button class="bg-red mb-3"  disabled>
                      {{ __('Delete') }}
                      <x-icon name="trash"></x-icon>
                  </x-button>
                @endif
              @endcanAccess
            </li>
            @foreach ($items as $item)
              <li x-data="{ elmId: {{ $item->id }} }" wire:key="item-{$item->id}" class="flex justify-between gap-x-5 pt-4 pb-2">
                <div class="flex min-w-0 gap-x-4">
                    <label class="flex items-center">
                      @canAccess(json_encode('delete_' . $entity))
                          <x-checkbox id="{{ $item->id }}"  x-on:change="e => {
                            itemsSelected[elmId] = e.target.checked;
                            $dispatch('item-selection', {entity: '{{ $entity }}', items: itemsSelected});
                          }"/>
                      @endcanAccess
                      <div @class(['flex' => $withProfileImage])>
                        @if($withProfileImage)
                          <x-profile-img :imgUrl="$item->profile_photo_path" />
                        @endif
                        <span class="ms-2 mt-2 text-sm text-gray-600 min-w-4">{{ $item->name }}</span>
                      </div>
                    </label>
                    @if($extraDataComponent)
                          @switch($extraDataComponent)
                            @case('tags')
                              @if ($item->$dataProperty)
                                <x-tags :data="$item->$dataProperty->toArray()" tagClasses="max-h-3"/>
                              @endif
                            @break
                            @case('data-grid')
                              <x-data-grid :item="$item" :fields="$dataProperty" />
                            @break
                          @endswitch
                        @endif
                </div>
                <div>
                  @canAccess(json_encode('edit_' . $entity))
                    <x-button class="bg-blue hover:bg-blue-dark mr-4" x-on:click="$dispatch('open-edit-modal', { itemId: {{ $item->id }}, entity: entity})">{{ __('Edit') }}</x-button>
                  @endcanAccess
                  </div>
              </li>
            @endforeach
          @endif
        </ul>
        <div>
          <x-modal 
            type='confirmation'
            id="delete-{{ $entity }}s"
            title='{{ __("Are you sure?") }}'
            content='{{ __("Are you sure you want to delete the selected {$entity}s?") }}'
            confirmButtonTitle='{{ __("Delete") }}'
            confirmButtonIcon='trash'
          ></x-modal>
          <x-modal-edit entity='{{ $entity }}' showSubmit="{{ $showEditSubmitButton }}">
            <x-slot name="form">
              @livewire($entity . 's.edit')
            </x-slot>
          </x-modal-edit>
        </div>
      </div>
      <div> {{ $items->links() }} </div>
    </x-slot>
</x-form-section>

