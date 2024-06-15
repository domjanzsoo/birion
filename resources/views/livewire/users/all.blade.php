@php
  $extraInformation = [
    'component' => 'data-grid',
    'dataProperty' => ['email', 'verified']
  ];
@endphp

<div>
  <x-items-list
    entity="{{__('users.user_entity')}}"
    title="{{__('users.users')}}"
    description="{{ __('users.users_full') }}"
    :deleteButtonAccess="$deleteButtonAccess"
    :extraInformation="$extraInformation"
    :items="$users"
  />
</div>

