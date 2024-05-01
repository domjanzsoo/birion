@props(['title', 'description', 'entity', 'items', 'deleteButtonAccess'])

<x-form-section>
    <x-slot name="title">
        {{ $title }}
    </x-slot>
    <x-slot name="description">
        {{ $description }}
    </x-slot>
    <x-slot name="list">
      <div x-data="{ itemsSelected: {} }" class="w-full" style="height: calc({{ $items->perPage() }} * 73px)">
        <ul role="list" class="divide-y divide-gray-100 w-full">
          @if ($items->total() === 0)
            <li> No permission found </li>
          @else
            <li class="flex justify-end">
              @if ($deleteButtonAccess)
                <x-button x-data x-on:click="$dispatch('open-modal', {id: 'deletePermissions'})" class="bg-red mb-3">
                  {{ __('Delete') }}
                  <x-icon name="trash"></x-icon>
                </x-button>
              @else
                <x-button class="bg-red mb-3"  disabled>
                    {{ __('Delete') }}
                    <x-icon name="trash"></x-icon>
                </x-button>
              @endif
            </li>
            @foreach ($items as $item)
              <li x-data="{ elmId: {{ $item->id }} }" class="flex justify-between gap-x-6 py-5">
                <div class="flex min-w-0 gap-x-4">
                    <label class="flex items-center">
                        <x-checkbox id="{{ $item->id }}"  x-on:change="e => {
                         itemsSelected[elmId] = e.target.checked;
                          $dispatch('itemSelection', {entity: '{{ $entity }}', items: itemsSelected});
                        }"/>
                        <span class="ms-2 text-sm text-gray-600">{{ $item->name }}</span>
                    </label>
                </div>
                <div>
                    <x-button class="bg-blue hover:bg-blue-dark mr-4" x-on:click="$dispatch('open-edit-modal', { itemId: elmId, entity: 'permission'})">{{ __('Edit') }}</x-button>
                </div>
              </li>
            @endforeach
          @endif
        </ul>
        <div>
          <x-modal 
            type='confirmation'
            id="deletePermissions"
            title='{{ __("Are you sure?") }}'
            content='{{ __("Are you sure you want to delete the selected permissions?") }}'
            confirmButtonTitle='{{ __("Delete") }}'
            confirmButtonIcon='trash'
          ></x-modal>
          <x-modal-edit>
            <x-slot name="form">
              @livewire('permissions.edit')
            </x-slot>
          </x-modal-edit>
        </div>
      </div>
      <div> {{ $items->links() }} </div>
    </x-slot>
</x-form-section>

