<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 disabled:opacity-25 transition ease-in-out duration-150']) }}
style="background-color: #F2A714; font-weight: bold; font-size: 14pt; padding: 15px 10px">
    {{ $slot }}
</button>
