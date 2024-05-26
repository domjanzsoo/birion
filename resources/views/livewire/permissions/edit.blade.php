<form wire:submit="save" class="w-full">
    <div class="grid grid-cols-4 gap-1">
        <div class="py-3">
            <x-label for="state.permission_name" value="{{ __('Permission Name') }}" />
        </div>
        <div class="col-span-3">
            <x-input id="permission_name" type="text" class="pt-2 block w-full" wire:model="state.name" value="{{ $state['name'] }}"/>
            <x-input-error for="state.name" class="mt-2 text-left" />
        </div>   
    </div>
</form>
