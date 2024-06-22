@props(['options' => [], 'event' => '', 'model' => '', 'selected' => null])

<style>
#select {
    display: none;
}

.svg-icon {
  width: 1em;
  height: 1em;
}

.svg-icon path,
.svg-icon polygon,
.svg-icon rect {
  fill: #333;
}

.svg-icon circle {
  stroke: #4691f6;
  stroke-width: 1;
}
</style>

<div
    x-init="() => {
      console.log('form-submit event');
      selected = {};
      let selections = {{ json_encode($selected) }}

      if (selections) {
        selections.forEach(selection => {
          selected[selection.id] = {
            name: selection.name,
            selected: true
          };
        });
      }

      Livewire.on(event + '-submitted', () => {
            selected = {};
        });
    }"
    x-data="{
        options: {{ json_encode($options) }},
        event: '{{ $event }}',
        selected: {},
        show: false,
        open() { this.show = true; },
        close() { this.show = false },
        isOpen() { 
          return this.show === true;
        },
        select(elm) {
            if (!this.selected[elm.id]) {
                this.selected[elm.id] = {
                    name: elm.name,
                    selected: true
                };
            } else {
                this.selected[elm.id].selected = !this.selected[elm.id].selected;
            }

            $dispatch(this.event, { selections: this.selected });
        },
        remove(id) {
            this.selected[id].selected = false;

            $dispatch(this.event, { selections: this.selected });
        }
    }"
    class="w-full  flex flex-col items-center  mx-auto"
>
  <input name="values" type="hidden" wire.model="{{ $model }}">
  <div class="inline-block relative w-full">
    <div class="flex flex-col items-center relative">
      <div x-on:click="open" class="w-full">
        <div class="my-2 p-1 flex border border-gray-200 bg-white rounded">
          <div class="flex flex-auto flex-wrap">
            <template x-for="[id, value] in Object.entries(selected)" :key="id">
              <div x-show="value.selected"  class="flex justify-center items-center m-1 font-medium py-1 px-1 bg-green-light rounded  border">
                <div class="text-xs font-normal leading-none max-w-full flex-initial" x-model="id" x-text="value.name"></div>
                <div class="flex flex-auto flex-row-reverse">
                  <div x-on:click.stop="remove(id)" class="text-red-dark">
                    <x-icon name="x-mark" classes="cursor-pointer" />
                  </div>
                </div>
              </div>
            </template>
            
          </div>
          <div class="text-gray-300 w-8 py-1 pl-2 pr-1 border-l flex items-center border-gray-200 svelte-1l8159u">
            <button type="button" x-show="isOpen() === true" x-on:click="open" class="cursor-pointer w-6 h-6 text-gray-600 outline-none focus:outline-none">
                <x-icon name="chevron-up" />
            </button>
            
            <button type="button" x-show="isOpen() === false" @click="close" class="cursor-pointer w-6 h-6 text-gray-600 outline-none focus:outline-none">
                <x-icon name="chevron-down" />
            </button>
          </div>
        </div>
      </div>
      <div class="w-full px-4">
        <div x-show.transition.origin.top="isOpen()" class="absolute shadow top-100 bg-white z-40 w-full left-0 rounded max-h-select" x-on:click.away="close">
          <div class="flex flex-col w-full overflow-y-auto h-64">
            <template x-for="option in options" :key="option.id" class="overflow-auto">
              <div class="cursor-pointer w-full border-gray-100 rounded-t border-b hover:bg-gray-100" @click="select(option)">
                <div class="flex w-full items-center p-2 pl-2 border-transparent border-l-2 relative">
                  <div class="w-full items-center flex justify-between">
                    <div class="mx-2 leading-6" x-model="option" x-text="option.name"></div>
                    <div x-show="selected[option.id] ? selected[option.id].selected : false">
                      <x-icon name="check" />
                    </div>
                  </div>
                </div>
              </div>
            </template>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>