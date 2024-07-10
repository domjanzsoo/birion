<div>
  <x-grid-paginated >
    <span> 1 </span>
  </x-grid-paginated>
  <x-items-list entity="{{__('properties.property_entity')}}" title="{{__('properties.properties')}}" description="{{ __('properties.properties_full') }}" :deleteButtonAccess="$deleteButtonAccess" :items="$properties" />
</div>

