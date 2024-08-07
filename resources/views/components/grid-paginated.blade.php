@props(['cols' => 3, 'data' => []])

<div>
    <div class="grid grid-cols-{{ $cols }} gap-4">
        @foreach($data as $elm)
            <div class="mb-6" x-data="{ elmData: {{ json_encode($elm->toArray()) }} }" >
                {{ $slot }}
            </div>
        @endforeach
    </div>
</div>