@props(['cols' => 4, 'data' => []])

<div>
    <div class="grid grid-cols-{{ $cols }} gap-4">
        @foreach($data as $elm)
            <div x-data="{ elmData: {{ json_encode($elm) }} }">
                {{ $slot }}
            </div>
        @endforeach
    </div>
</div>