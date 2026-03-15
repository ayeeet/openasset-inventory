<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add Infrastructure Cost') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <form method="POST" action="{{ route('infrastructure-costs.store') }}">
                        @csrf

                        <!-- Service Name -->
                        <div>
                            <x-input-label for="service_name" :value="__('Service/Resource Name')" />
                            <x-text-input id="service_name" class="block mt-1 w-full" type="text" name="service_name" :value="old('service_name')" required autofocus placeholder="e.g. AWS EC2, Cloudflare, Azure Storage" />
                            <x-input-error :messages="$errors->get('service_name')" class="mt-2" />
                        </div>

                         <!-- Category -->
                         <div class="mt-4">
                            <x-input-label for="category" :value="__('Category')" />
                            <x-text-input id="category" class="block mt-1 w-full" type="text" name="category" :value="old('category')" placeholder="e.g. Cloud, Storage, Networking" />
                            <x-input-error :messages="$errors->get('category')" class="mt-2" />
                        </div>

                         <!-- Amount -->
                        <div class="mt-4">
                            <x-input-label for="amount" :value="__('Amount')" />
                            <x-text-input id="amount" class="block mt-1 w-full" type="number" step="0.01" name="amount" :value="old('amount')" required />
                            <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                        </div>

                         <div class="grid grid-cols-2 gap-4 mt-4">
                            <!-- Month -->
                            <div>
                                <x-input-label for="month" :value="__('Month')" />
                                <select id="month" name="month" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    @for($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}" {{ old('month', date('n')) == $i ? 'selected' : '' }}>
                                            {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                                        </option>
                                    @endfor
                                </select>
                                <x-input-error :messages="$errors->get('month')" class="mt-2" />
                            </div>

                            <!-- Year -->
                            <div>
                                <x-input-label for="year" :value="__('Year')" />
                                <x-text-input id="year" class="block mt-1 w-full" type="number" name="year" :value="old('year', date('Y'))" required />
                                <x-input-error :messages="$errors->get('year')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mt-4">
                            <x-input-label for="description" :value="__('Description / Notes')" />
                            <textarea id="description" name="description" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3">{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('infrastructure-costs.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                            <x-primary-button>
                                {{ __('Add Entry') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
