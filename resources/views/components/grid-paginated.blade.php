@props(['cols' => 3, 'items' => [], 'wrapperStyle' => ''])

<div style="{{ $wrapperStyle }}">
    <div>
        <div class="grid grid-cols-{{ $cols }} gap-4">
            @foreach($items as $elm)
                <div class="mb-6" x-data="{ elmData: {{ json_encode($elm->toArray()) }} }" wire:key="elm-{{ $elm->id }}">
                    {{ $slot }}
                </div>
            @endforeach
        </div>
    </div>
    <div> {{ $items->links() }} </div>
</div>