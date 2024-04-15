<x-form-section>
    <x-slot name="title">
        {{ __('All Permissions') }}
    </x-slot>

    <x-slot name="description">
        {{ __('All existing permissions listed.') }}
    </x-slot>

    <x-slot name="list">
      <div class="w-full">
        <ul role="list" class="divide-y divide-gray-100 w-full">
          @if ($permissions->count() === 0)
            <li> No permission found </li>
          @else
            <li class="flex justify-end">
              @if ($deleteButtonAccess)
                <x-button x-data x-on:click="$dispatch('confirm', {title: 'here is some title'})" class="bg-red mb-3">
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
            @foreach ($permissions as $permission)
              <li class="flex justify-between gap-x-6 py-5">
                <div class="flex min-w-0 gap-x-4">
                    <label class="flex items-center">
                        <x-checkbox wire:model="permissionsToDelete.{{ $permission->id }}" wire:change="processPermissionCheck()"/>
                        <span class="ms-2 text-sm text-gray-600">{{ $permission->name }}</span>
                    </label>
                </div>
                <div>
                    <x-button class="bg-blue hover:bg-blue-dark mr-4">{{ __('Edit') }}</x-button>
                </div>
              </li>
            @endforeach
          @endif
        </ul>
      </div>
    </x-slot>
</x-form-section>

