@props(['imgUrl' => null, 'size' => '10', 'changable' => false])

@if ($changable)
    <div
        x-data="{
            'inputId': 'upload',
            'selectedFileName': ''
        }" 
        style="background-image: url('{{ $imgUrl ? asset($imgUrl) : asset('/storage/avatar/user.png') }}"
        class="inline-block z-50 w-{{$size}} h-{{ $size }} rounded-full ring-2 ml-4 mb-2 ring-gray bg-center bg-cover bg-no-repeat"
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
            class="flex justify-center w-{{ $size }} h-{{ $size }} cursor-pointer">
            <input id="upload" type="file" name="file_upload" {!! $attributes->merge(['class' => 'hidden']) !!}>
        </label>
    </div>
@else
    <div
        style="background-image: url('{{ $imgUrl ? asset($imgUrl) : asset('/storage/avatar/user.png') }}"
        class="inline-block z-50 w-{{$size}} h-{{ $size }} rounded-full ring-2 ml-4 mb-2 ring-gray bg-center bg-cover bg-no-repeat"
    >
    </div>
@endif