@php
  $extraInformation = [
    'component' => 'tags',
    'dataProperty' => 'permissions'
  ];
@endphp

<x-form-section>
  <x-slot name="title">
    {{ __('roles.roles') }}
  </x-slot>
  <x-slot name="description">
    {{ __('roles.roles_full') }}
  </x-slot>
  <x-slot name="list">
  <x-items-list
    entity="{{__('role')}}"
    :deleteButtonAccess="$deleteButtonAccess"
    :items="$roles"
    :extraInformation="$extraInformation"
    showEditSubmitButton="true"
  />
  </x-slot>
</x-form-section>

