@props(['message'])
<script>
    window.addEventListener('load', () => {
        window.addEventListener('toastr', payload => {
            setTimeout(() => {
                @this.clearToast()
            }, 1500);
        })
    })
</script>

@switch($message['type'])
    @case('error')
        <div class="w-auto h-15 bg-red shadow-lg absolute top-10 right-10 z-50 px-5 py-2 rounded-lg text-white">{{ $message['message'] }}</div>
    @case('confirm')
        <div class="w-auto h-15 bg-green shadow-lg absolute top-10 right-10 z-50 px-5 py-2 rounded-lg text-white">{{ $message['message'] }}</div>
    @default
        <div class="w-auto text-white absolute top-10 right-10 z-50 px-5 py-2 rounded-lg">{{ $message['message'] }}</div>
@endswitch

