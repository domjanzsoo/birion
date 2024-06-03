<button {{ $attributes->merge([
            'type'      => 'submit',
            'class'     => 'inline-flex items-center px-3 py-2 border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150',
        ]) }}>
    {{ $slot }}
</button>
