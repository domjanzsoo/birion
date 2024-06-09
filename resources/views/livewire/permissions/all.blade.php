<div>
  <x-items-list entity="{{__('permissions.permission_entity')}}" title="{{__('permissions.permissions')}}" description="{{ __('permissions.permissions_full') }}" :deleteButtonAccess="$deleteButtonAccess" :items="$permissions" />
</div>

