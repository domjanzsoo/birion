<div class="flex mt-5 justify-center gap-4">
  @if ($paginator->currentPage() > 1)
    <button
      class="flex justify-center gap-2 px-6 py-3 font-sans text-xs font-bold text-center text-gray uppercase align-middle transition-all rounded-full select-none hover:bg-gray-dark hover:text-white active:bg-gray-dark disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none"
      wire:click="previousPage"
      type="button">
      <x-icon name="chevron-left" />
      {{ __('Previous')  }}
    </button>
  @else
    <button
      class="flex justify-center gap-2 px-6 py-3 font-sans text-xs font-bold text-center text-gray uppercase align-middle transition-all rounded-full select-none hover:bg-gray-dark hover:text-white active:bg-gray-dark disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none"
      type="button"
      disabled>
      <x-icon name="chevron-left" />
      {{ __('Previous')  }}
    </button>
  @endif
  <div class="flex items-center gap-2">
    @for ($i = 1; $i <= $paginator->lastPage(); $i++)
      <button
        @class([
          'relative', 
          'h-10',  
          'max-h-[40px]', 
          'w-10', 
          'max-w-[40px]', 
          'select-none', 
          'rounded-full', 
          'text-center', 
          'align-middle', 
          'font-sans', 
          'text-xs',
          'font-medium',
          'uppercase', 
          'text-gray' => $paginator->currentPage() != $i, 
          'text-white' => $paginator->currentPAge() == $i, 
          'transition-all',
          'hover:bg-gray-900/10' => $paginator->currentPage() != $i,
          'active:bg-gray-900/20' => $paginator->currentPage() != $i,
          'disabled:pointer-events-none',
          'disabled:opacity-50',
          'disabled:shadow-none',
          'bg-gray-dark' => $paginator->currentPage() == $i
          ])
          wire:click="gotoPage({{$i}})"
          type="button">
          <span class="absolute transform -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2">
            {{ $i }}
          </span>
      </button>
    @endfor
  </div>
  @if ($paginator->currentPage() < $paginator->lastPage())
    <button
      class="flex items-center gap-2 px-4 py-3 font-sans text-xs font-bold text-center text-gray uppercase align-middle transition-all rounded-full select-none hover:bg-gray-dark active:bg-gray-dark hover:text-white disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none"
      wire:click="nextPage"
      type="button">
      Next
      <x-icon name="chevron-right" />
    </button>
  @else
    <button
      class="flex items-center gap-2 px-4 py-3 font-sans text-xs font-bold text-center text-gray uppercase align-middle transition-all rounded-full select-none hover:bg-gray-dark active:bg-gray-dark hover:text-white disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none"
      type="button"
      disabled>
      Next
      <x-icon name="chevron-right" />
    </button>
  @endif
</div>