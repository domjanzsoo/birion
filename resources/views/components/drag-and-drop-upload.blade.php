@props(['fileType' => ''])

<style>
[x-cloak] { display: none !important }
</style>

<div
    x-data="{
        'inputId': '{{ $fileType . '-field' }}',
        'selectedFileName': ''
    }" 
    class="w-full"
    x-on:drop="$event => {
        $event.preventDefault();

        fileInput = document.getElementById(inputId);

        const dataTransfer = new DataTransfer();
        dataTransfer.items.add($event.dataTransfer.files[0])
        fileInput.files = dataTransfer.files;
        selectedFileName = $event.dataTransfer.files[0].name

        fileInput.dispatchEvent(new Event('change', { bubbles: true }));
    }"
    x-on:dragover.prevent="() => console.log('drag over prevented')"
>
    <label
        class="flex justify-center w-full h-32 px-4 transition bg-white border-2 border-gray-300 border-dashed rounded-md appearance-none cursor-pointer hover:border-gray-400 focus:outline-none">
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
</div>