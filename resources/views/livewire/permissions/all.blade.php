<ul role="list" class="divide-y divide-gray-100">
  <li class="flex justify-between gap-x-6 py-5">
    <div class="flex min-w-0 gap-x-4">
        <label class="flex items-center">
            <x-checkbox wire:model="permissionsToDelete" :value="null"/>
            <span class="ms-2 text-sm text-gray-600">{{ 'permission 1' }}</span>
        </label>
    </div>
    <div>
        <x-button class="bg-sky-500">{{ __('Edit') }}</x-button>
    </div>
  </li>
  <li class="flex justify-between gap-x-6 py-5">
    <div class="flex min-w-0 gap-x-4">
    <label class="flex items-center">
            <x-checkbox wire:model="permissionsToDelete" :value="null"/>
            <span class="ms-2 text-sm text-gray-600">{{ 'permission 2' }}</span>
        </label>
    </div>
    <div>
        <x-button class="bg-sky-500">{{ __('Edit') }}</x-button>
    </div>
  </li>
</ul>
