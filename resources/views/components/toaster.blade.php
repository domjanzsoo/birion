<div x-data="{ show: false, message: '', type: '' }" x-init="
    console.log('init');
    Livewire.on('toastr', payload => {
        console.log('from alpine component');
        console.log(event);

        type = payload[0].type;
        message = payload[0].message;

        setTimeout(() => {
            type = null;
            message = '';
        }, 1500);
    });
">
    <div x-show="type === 'error'" x-text="message" class="w-auto h-15 bg-red shadow-lg absolute top-10 right-10 z-50 px-5 py-2 rounded-lg text-white"></div>
    <div x-show="type === 'confirm'" x-text="message" class="w-auto h-15 bg-green shadow-lg absolute top-10 right-10 z-50 px-5 py-2 rounded-lg text-white"></div>
</div>

