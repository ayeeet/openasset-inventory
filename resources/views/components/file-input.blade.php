@props(['name', 'id' => null, 'accept' => null, 'label' => 'Choose File'])

@php
    $id = $id ?? $name;
@endphp

<div x-data="{ fileName: '' }" class="w-full">
    <div class="relative flex items-center justify-between border border-gray-300 rounded-md shadow-sm p-2 bg-white hover:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-500 transition duration-150 ease-in-out">
        <div class="flex items-center space-x-2 overflow-hidden mr-2">
            <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
            </svg>
            <span x-text="fileName || '{{ $label }}'" class="text-sm text-gray-500 truncate"></span>
        </div>
        
        <label for="{{ $id }}" class="cursor-pointer bg-white py-1 px-3 border border-gray-300 rounded-md text-sm leading-4 font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 flex-shrink-0">
            Browse
        </label>

        <input 
            type="file" 
            name="{{ $name }}" 
            id="{{ $id }}" 
            class="sr-only" 
            @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''"
            @if($accept) accept="{{ $accept }}" @endif
            {{ $attributes }}
        >
    </div>
</div>
