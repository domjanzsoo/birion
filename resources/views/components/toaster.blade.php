<div x-data="{ show: false, message: '', type: '' }" class="fixed top-10 right-10" x-init="
    console.log('init');
    Livewire.on('toastr', payload => {

        type = payload[0].type;
        message = payload[0].message;

        setTimeout(() => {
            type = null;
            message = '';
        }, 1500);
    });
">
    <div x-show="type === 'error'" x-text="message" class="w-auto h-15 bg-red shadow-lg  z-50 px-5 py-2 rounded-lg text-white"></div>
    <div x-text="message" x-show="type === 'confirm'"  class="w-auto h-15 bg-green shadow-lg  px-5 py-2 rounded-lg text-white"></div>
</div>

