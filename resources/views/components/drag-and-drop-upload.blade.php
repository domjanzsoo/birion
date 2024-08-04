@props(['fileType' => '', 'multi' => false, 'resetEvent' => '', 'checkable' => false, 'checkLabel' => 'Select', 'checkEvent' => null])

<style>
[x-cloak] { display: none !important }

.hasImage:hover section {
  background-color: rgba(5, 5, 5, 0.4);
}
.hasImage:hover button:hover {
  background: rgba(5, 5, 5, 0.45);
}

.hasImage:hover section #imgName,
.hasImage:hover section #size {
    opacity: 1;
}

input[type="radio"] {
  -ms-transform: scale(0.7); /* IE 9 */
  -webkit-transform: scale(0.7); /* Chrome, Safari, Opera */
  transform: scale(0.7);
}
</style>

<div
    x-data="{
        fileType: '{{ $fileType }}',
        inputId: '{{ $fileType . '-field' }}',
        selectedFileName: '',
        fileInput: null,
        multiple: {{ json_encode($multi) }},
        resetEvent: '{{ $resetEvent }}',
        checkEvent: '{{ $checkEvent }}',
        images: [],
        removeImage(image, index) {
            const dataTransfer = new DataTransfer();

            Array.from(this.fileInput.files).forEach(file => {
                if (file.name !== image.name) {
                    dataTransfer.items.add(file);
                }
            });

            this.fileInput.value = '';

            if (dataTransfer.files.length > 0) {
                this.fileInput.files = dataTransfer.files;
            } else {
                $dispatch(this.fileType + '-empty');
            }

            this.images.splice(index, 1);

            this.fileInput.dispatchEvent(new Event('change', { bubbles: true }));
        }
    }" 
    x-init="() => {
        console.log('init');

        Livewire.on(resetEvent, event => {
            selectedFileName = '';
            images = []
        });

        $nextTick(() => {
            fileInput = document.getElementById(inputId);

            if (multiple) {
                fileInput.setAttribute('multiple', '');
            }
        });
    }"
    class="w-full"
    x-on:drop="$event => {
        $event.preventDefault();

        const dataTransfer = new DataTransfer();

        if (multiple) {
            Array.from(fileInput.files).forEach(file => {
                dataTransfer.items.add(file);
            });
        }

        Array.from($event.dataTransfer.files).forEach(file => {
            dataTransfer.items.add(file);

            if (multiple) {
                images.push(file);
            } else {
                selectedFileName = file.name;
            }
        });

        fileInput.files = dataTransfer.files;

        fileInput.dispatchEvent(new Event('change', { bubbles: true }));
    }"
    x-on:dragover.prevent="() => console.log('drag over prevented')"
>
    <label class="flex justify-center w-full h-32 px-4 transition bg-white border-2 border-gray-300 border-dashed rounded-md appearance-none cursor-pointer hover:border-gray-400 focus:outline-none">
        <span class="flex items-center space-x-2">
            <x-icon name="upload" />
            <span class="font-medium text-gray-600">
                {{ __('files.drop_files_or') }}
                <span class="text-blue-600 underline">{{ __('files.browse') }}</span>
            </span>
        </span>
        @if ($multi)
            <input
                :id="inputId"
                type="file"
                name="file_upload"
                {!! $attributes->merge(['class' => 'hidden']) !!}
                multiple
            >
        @else
            <input
                :id="inputId"
                type="file"
                name="file_upload"
                {!! $attributes->merge(['class' => 'hidden']) !!}
            >
        @endif
    </label>
    <div x-cloak class="w-full text-center" x-show="selectedFileName">
        {{ ucfirst(__('files.selected')) }}: <span x-text="selectedFileName"></span>
    </div>
    <div wire:ignore class="w-auto grid mt-3 gap-1" :style="`grid-template-columns: repeat(${images.length < 5 ? images.length : 5}, max-content)`">
        <template x-for="(image, index) in images" :key="index + image.lastModified">
            <div
                x-data="{
                    imgUrl: null,
                    imgSize: Math.ceil(image.size/1000) + 'kb'
                }"
                x-init="() => {
                    let reader = new FileReader();

                    reader.onload = e => {
                        imgUrl = e.target.result;
                    };

                    reader.readAsDataURL(image);
                }"
                class="w-32 h-24 relative"
            >
                <article class="cursor-pointer hasImage flex flex-col rounded-md text-xs break-words w-full h-full z-20 absolute top-0">
                    <img :src="imgUrl" class="w-full h-full sticky object-cover rounded-md bg-fixed" />

                    <section class="flex flex-col rounded-md text-xs break-words w-full h-full z-20 absolute top-0 py-2">
                        <div id="imgName" class="ml-2 flex-1 text-white opacity-0">
                            <h1  x-text="image.name"></h1>
                            @if ($checkable)
                                <div class="flex flex-col">
                                    <label>
                                        <x-input type="radio" name="image-radio-button" x-on:click="$dispatch(checkEvent, { itemIndex: index })" />{{ $checkLabel }}
                                    </label>
                                </div>
                            @endif
                        </div>
                        <div id="size" class="grid grid-cols-3 text-white opacity-0">
                            <div class="ml-2 p-1 size text-xs col-span-2" x-text="imgSize"></div>
                            <div class="rounded-md w-7 h-auto hover:bg-gray" x-on:click="removeImage(image, index)">
                                <div class="mx-1 my-1">
                                    <x-icon name="trash" wrapperClasses="mx-auto" class="pointer-events-none fill-white mx-auto" />
                                </div>
                            </div>
                        </div>
                    </section>
                </article>
            </div>
        </template>
    </div>
</div>
