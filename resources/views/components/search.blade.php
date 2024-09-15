@props(['entity' => null])

<div
    x-init="() => {
        $refs.searchInput.onkeydown = (event) => {
            if (event.keyCode === 13) {
                $dispatch(entity + '-search', { search: $refs.searchInput.value });
            }
        }
    }"
    x-data="{ entity: '{{ $entity }}' }"
    class="w-full flex">
    <div class="pt-2 pl-1 pr-2 rounded-l-md text-white bg-gray">
        <x-icon name="magnifying-glass" />
    </div>
    <x-input id="search-{{ $entity }}" x-ref="searchInput" placeholder="{{ __('general.search') }}" class="flex-1 text-sm rounded-none rounded-r-md" type="text" />
</div>