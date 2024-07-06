@props(['item', 'fields'])

<div class="text-sm text-gray mt-1 ml-6 grid grid-flow-col auto-cols-max">
  @foreach ($fields as $index => $field)
    <div @class([
              'border-gray-light',
              'h-4',
              'pl-1',
              'pr-1',
              'pb-2',
              'border-l-2' => $index
          ])>
          <span class="mb-2">
            @if (gettype($field) === 'array')
              <x-dynamic-component :component="$field['componentName']" :entity="$item" {{ $attributes->merge($field['attributes']) }}  class="mt-4" />
            @else
              {{ $item->$field }} 
            @endif
          </span>
    </div>
  @endforeach
</div>