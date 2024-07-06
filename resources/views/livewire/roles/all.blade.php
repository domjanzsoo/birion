@php
  $extraInformation = [
    'component' => 'tags',
    'dataProperty' => 'permissions'
  ];
@endphp

<div>
  <x-items-list
    entity="{{__('role')}}"
    title="{{__('roles.roles')}}"
    description="{{ __('roles.roles_full') }}"
    :deleteButtonAccess="$deleteButtonAccess"
    :items="$roles"
    :extraInformation="$extraInformation"
    showEditSubmitButton="true"
  />
</div>

