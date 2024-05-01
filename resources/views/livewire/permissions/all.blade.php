<div>
  <x-items-list entity="{{__('permission')}}" title="{{__('Permissions')}}" description="{{ __('All existing permissions listed.') }}" :deleteButtonAccess="$deleteButtonAccess" :items="$permissions" />
</div>

