@props(['page', 'totalPages'])

<div class="flex mt-5 justify-center gap-4">
        @if ($page > 1)
          <button
            class="flex justify-center gap-2 px-6 py-3 font-sans text-xs font-bold text-center text-gray uppercase align-middle transition-all rounded-full select-none hover:bg-gray-dark hover:text-white active:bg-gray-dark disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none"
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
              <button
                class="relative h-10 max-h-[40px] w-10 max-w-[40px] select-none rounded-full text-center align-middle font-sans text-xs font-medium uppercase text-gray transition-all hover:bg-gray-900/10 active:bg-gray-900/20 disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none"
                type="button">
                <span class="absolute transform -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2">
                  1
                </span>
              </button>
              <button
                class="relative h-10 max-h-[40px] w-10 max-w-[40px] select-none rounded-full text-center align-middle font-sans text-xs font-medium uppercase text-gray transition-all hover:bg-gray-900/10 active:bg-gray-900/20 disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none"
                type="button">
                <span class="absolute transform -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2">
                  2
                </span>
              </button>
              <button
                class="relative h-10 max-h-[40px] w-10 max-w-[40px] select-none rounded-full bg-gray-dark text-center align-middle font-sans text-xs font-medium uppercase text-white shadow-md shadow-gray-900/10 transition-all hover:shadow-lg hover:shadow-gray-900/20 focus:opacity-[0.85] focus:shadow-none active:opacity-[0.85] active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none"
                type="button">
                <span class="absolute transform -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2">
                  3
                </span>
              </button>
              <button
                class="relative h-10 max-h-[40px] w-10 max-w-[40px] select-none rounded-full text-center align-middle font-sans text-xs font-medium uppercase text-gray transition-all hover:bg-gray-900/10 active:bg-gray-900/20 disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none"
                type="button">
                <span class="absolute transform -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2">
                  4
                </span>
              </button>
              <button
                class="relative h-10 max-h-[40px] w-10 max-w-[40px] select-none rounded-full text-center align-middle font-sans text-xs font-medium uppercase text-gray transition-all hover:bg-gray-900/10 active:bg-gray-900/20 disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none"
                type="button">
                <span class="absolute transform -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2">
                  5
                </span>
              </button>
            </div>
            <button
              class="flex items-center gap-2 px-6 py-3 font-sans text-xs font-bold text-center text-gray uppercase align-middle transition-all rounded-full select-none hover:bg-gray-dark active:bg-gray-dark hover:text-white disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none"

              type="button">
              Next
              <x-icon name="chevron-right" />
            </button>
        </div>