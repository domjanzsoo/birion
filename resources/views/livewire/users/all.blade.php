<x-form-section>
  <x-slot name="title">
    {{ __('users.users') }}
  </x-slot>
  <x-slot name="description">
    {{ __('users.users_full') }}
  </x-slot>
  <x-slot name="list">
    <x-items-list
      entity="{{__('users.user_entity')}}"
      withProfileImage="true"
      :deleteButtonAccess="$deleteButtonAccess"
      :extraInformation="$extraInformation"
      :items="$users"
      :showEditSubmitButton="true"
    />
  </x-slot>
</x-form-section>

