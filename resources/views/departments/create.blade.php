<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ isset($department) ? 'Edit Department' : 'Add Department' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <form method="POST" action="{{ isset($department) ? route('departments.update', $department) : route('departments.store') }}">
                        @csrf
                        @if(isset($department))
                            @method('PUT')
                        @endif

                        <!-- Name -->
                        <div>
                            <x-input-label for="name" :value="__('Department Name')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $department->name ?? '')" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                         <!-- Head Name -->
                        <div class="mt-4">
                            <x-input-label for="head_name" :value="__('Head Name (Optional)')" />
                            <x-text-input id="head_name" class="block mt-1 w-full" type="text" name="head_name" :value="old('head_name', $department->head_name ?? '')" />
                            <x-input-error :messages="$errors->get('head_name')" class="mt-2" />
                        </div>

                        <!-- Location -->
                        <div class="mt-4">
                            <x-input-label for="location_id" :value="__('Location')" />
                            <select id="location_id" name="location_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">Select Location</option>
                                @foreach($locations as $location)
                                    <option value="{{ $location->id }}" {{ old('location_id', $department->location_id ?? '') == $location->id ? 'selected' : '' }}>{{ $location->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('location_id')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('departments.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                            <x-primary-button>
                                {{ isset($department) ? __('Update Department') : __('Create Department') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
