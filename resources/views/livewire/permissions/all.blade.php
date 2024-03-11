<div>
  <ul role="list" class="divide-y divide-gray-100">
    @if ($permissions->count() === 0)
      <li> No permission found </li>
    @else
      <li class="flex justify-end">
        <x-button class="bg-red mb-3" wire:click="deletePermissions" disabled="{{ count($permissionsToDelete) === 0 }}">
          {{ __('Delete') }}
          <x-icon name="trash"></x-icon>
        </x-button>
      </li>
      @foreach ($permissions as $permission)
        <li class="flex justify-between gap-x-6 py-5">
          <div class="flex min-w-0 gap-x-4">
              <label class="flex items-center">
                  <x-checkbox wire:model="permissionsToDelete" value="{{ $permission->id }}" />
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
  <x-toaster :message="$toastMessage"></x-toaster>
</div>
