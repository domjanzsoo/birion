@props(['filterTemplate' => []])

<div>
    @foreach ($filterTemplate as $filterElement)
        @switch ($filterElement['type'])
            @case ('dropdown')
                <div class="flex">
                    <label for="{{$filterElement['name']}}">{{ $filterElement['name'] }}</label>
                    <x-input></x-input>
                </div>
            @break
        @endswitch
    @endforeach
</div>