<form wire:submit="save" class="w-full">
    <div class="grid grid-cols-3 gap-1">
        <div class="py-3">
            <x-label for="permission_name" value="{{ __('Permission Name') }}" />
        </div>
        <div>
            <x-input id="permission_name" type="text" class="mt-1 block w-full" wire:model="name" value="{{ $name }}"/>
            <x-input-error for="permission_name" class="mt-2" />
        </div>   
    </div>
</form>
