@props(['imgUrl' => null, 'size' => '10'])

<div class="inline-block z-50 w-{{ $size }}  h-{{ $size }} rounded-full ring-2 ml-4 mb-2 ring-gray bg-center bg-cover bg-no-repeat" style="background-image: url('{{ $imgUrl ? asset($imgUrl) : asset('/storage/avatar/user.png') }}')"></div>