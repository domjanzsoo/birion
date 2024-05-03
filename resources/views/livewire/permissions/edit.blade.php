<form wire:submit="save" class="w-full">
    <div class="grid grid-cols-4 gap-1">
        <div class="py-3">
            <x-label for="permission_name" value="{{ __('Permission Name') }}" />
        </div>
        <div class="col-span-3">
            <x-input id="permission_name" type="text" class="pt-2 block w-full" wire:model="name" value="{{ $name }}"/>
            <x-input-error for="permission_name" class="mt-2" />
        </div>   
    </div>
</form>
