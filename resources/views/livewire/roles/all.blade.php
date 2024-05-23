@php
  $extraInformation = [
    'component' => 'tags',
    'dataProperty' => 'permissions'
  ];
@endphp

<div>
  <x-items-list entity="{{__('role')}}" title="{{__('Roles')}}" description="{{ __('All existing roles listed.') }}" :deleteButtonAccess="$deleteButtonAccess" :items="$roles" :extraInformation="$extraInformation" />
</div>

