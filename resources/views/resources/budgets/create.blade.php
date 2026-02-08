<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add Budget') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <form method="POST" action="{{ route('budgets.store') }}">
                        @csrf

                        <!-- Year -->
                        <div>
                            <x-input-label for="year" :value="__('Year')" />
                            <x-text-input id="year" class="block mt-1 w-full" type="number" name="year" :value="old('year', date('Y'))" required autofocus />
                            <x-input-error :messages="$errors->get('year')" class="mt-2" />
                        </div>

                        @if(in_array($currency ?? '$', ['$', '₱']))
                        <p class="text-sm text-gray-600 mb-2">Conversion rate: 1 USD = {{ $usd_to_php_rate ?? 59 }} PHP. Amounts are stored in your selected display currency ({{ $currency ?? '$' }}).</p>
                        @endif

                        <!-- Annual Budget -->
                        <div class="mt-4">
                            <x-input-label for="annual_budget" :value="__('Annual Budget')" />
                            <x-text-input id="annual_budget" class="block mt-1 w-full" type="number" step="0.01" name="annual_budget" :value="old('annual_budget')" required />
                            <x-input-error :messages="$errors->get('annual_budget')" class="mt-2" />
                        </div>

                        <!-- Monthly Budget -->
                        <div class="mt-4">
                            <x-input-label for="monthly_budget" :value="__('Monthly Budget Limit')" />
                            <x-text-input id="monthly_budget" class="block mt-1 w-full" type="number" step="0.01" name="monthly_budget" :value="old('monthly_budget')" required />
                            <x-input-error :messages="$errors->get('monthly_budget')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('budgets.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                            <x-primary-button>
                                {{ __('Create Budget') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
