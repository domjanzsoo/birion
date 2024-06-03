@props(['data' => null, 'tagClasses' => ''])

    <div class="flex flex-wrap items-center w-full">
        @for ($i = 0; $i < count($data); $i++)
            <div  class="flex justify-center items-center m-1 py-1 px-1 bg-green-light rounded  border">
                <div class="text-xs  leading-none max-w-full {{ $tagClasses }}">
                    {{ $data[$i]['name'] }}
                </div>
            </div>
        @endfor
    </div>