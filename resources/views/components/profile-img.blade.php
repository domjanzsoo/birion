@props(['imgUrl' => null, 'size' => 11])

<div class="inline-block z-50 h-{{ $size }} w-{{ $size }} rounded-full ring-2 ml-4 mb-2 ring-gray bg-center bg-cover bg-no-repeat" style="background-image: url('{{ $imgUrl ? asset($imgUrl) : asset('/storage/avatar/user.png') }}')"></div>