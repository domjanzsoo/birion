@props(['fileType' => '', 'multi' => false])

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
</style>

<div
    x-data="{
        inputId: '{{ $fileType . '-field' }}',
        selectedFileName: '',
        fileInput: null,
        multiple: {{ json_encode($multi) }},
        images: [],
        imgCols: 0
    }" 
    x-init="() => {
        fileInput = document.getElementById(inputId);
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
        });

        fileInput.files = dataTransfer.files;

        if (!multiple) {
            selectedFileName = $event.dataTransfer.files[0].name;
        } else {
            images = Array.from(fileInput.files);
        }

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
        <input id="{{ $fileType . '-field' }}" type="file" name="file_upload" {!! $attributes->merge(['class' => 'hidden']) !!}>
    </label>
    <div x-cloak class="w-full text-center" x-show="selectedFileName">
        {{ ucfirst(__('files.selected')) }}: <span x-text="selectedFileName"></span>
    </div>
    <div x-show="multiple" class="w-auto grid mt-3 gap-1" :style="`grid-template-columns: repeat(${images.length < 5 ? images.length : 5}, max-content)`">
        <template x-for="image in images" :key="image.name">
            <div
                x-data="{
                    imgUrl: null,
                    imgSize: image.size + 'kb'
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
                        <h1 id="imgName" class="ml-2 flex-1 text-white opacity-0" x-text="image.name"></h1>
                        <div id="size" class="grid grid-cols-3 text-white opacity-0">
                            <div class="ml-2 p-1 size text-xs col-span-2" x-text="imgSize"></div>
                                <x-icon name="trash" wrapperClasses="w-auto mx-auto pt-1 rounded-md hover:bg-gray" class="pointer-events-none fill-white mx-auto" />             
                            </div>
                        </div>
                    </section>
                </article>
            </div>
        </template>
    </div>
</div>