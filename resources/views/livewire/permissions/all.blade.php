<x-form-section>
  <x-slot name="title">
    {{ __('permissions.permissions') }}
  </x-slot>
  <x-slot name="description">
    {{ __('permissions.permissions_full') }}
  </x-slot>
  <x-slot name="list">
    <x-items-list entity="{{__('permissions.permission_entity')}}" :deleteButtonAccess="$deleteButtonAccess" :items="$permissions" />
  </x-slot>
</x-form-section>