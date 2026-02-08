<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('System Settings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    @if(session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('POST')

                        <h3 class="text-lg font-medium text-gray-900 mb-4">General Information</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Company Name -->
                            <div>
                                <x-input-label for="company_name" :value="__('Company/Organization Name')" />
                                <x-text-input id="company_name" class="block mt-1 w-full" type="text" name="company_name" :value="old('company_name', $settings['company_name'] ?? 'OpenAsset Inventory')" required />
                            </div>

                            <!-- Admin Email -->
                            <div>
                                <x-input-label for="admin_email" :value="__('Administrator Email')" />
                                <x-text-input id="admin_email" class="block mt-1 w-full" type="email" name="admin_email" :value="old('admin_email', $settings['admin_email'] ?? 'admin@example.com')" required />
                            </div>

                            <!-- App Logo -->
                            <div class="col-span-1 md:col-span-2">
                                <x-input-label for="app_logo" :value="__('Application Logo')" />
                                @if(isset($settings['app_logo']))
                                    <div class="mt-2 mb-2">
                                        <img src="{{ Storage::url($settings['app_logo']) }}" alt="Current Logo" class="h-16 w-auto object-contain">
                                    </div>
                                @endif
                                <input id="app_logo" type="file" name="app_logo" class="block mt-1 w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
                                <p class="mt-1 text-sm text-gray-500">SVG, PNG, JPG or GIF (MAX. 2MB).</p>
                            </div>
                        </div>

                         <h3 class="text-lg font-medium text-gray-900 mt-6 mb-4">Preferences</h3>
                         <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Currency -->
                            <div>
                                <x-input-label for="currency_symbol" :value="__('Currency Symbol')" />
                                <select id="currency_symbol" name="currency_symbol" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="$" {{ ($settings['currency_symbol'] ?? '$') == '$' ? 'selected' : '' }}>USD ($)</option>
                                    <option value="₱" {{ ($settings['currency_symbol'] ?? '$') == '₱' ? 'selected' : '' }}>PHP (₱)</option>
                                    <option value="€" {{ ($settings['currency_symbol'] ?? '$') == '€' ? 'selected' : '' }}>EUR (€)</option>
                                    <option value="£" {{ ($settings['currency_symbol'] ?? '$') == '£' ? 'selected' : '' }}>GBP (£)</option>
                                </select>
                            </div>

                            <!-- USD to PHP rate (for budget/resources conversion) -->
                            <div>
                                <x-input-label for="usd_to_php_rate" :value="__('USD to PHP Rate')" />
                                <x-text-input id="usd_to_php_rate" class="block mt-1 w-full" type="number" step="0.01" min="0" name="usd_to_php_rate" :value="old('usd_to_php_rate', $settings['usd_to_php_rate'] ?? '59')" />
                                <p class="mt-1 text-sm text-gray-500">1 USD = this many PHP (e.g. 59). Used for converting amounts between USD and PHP in Resources & Budgets.</p>
                            </div>

                             <!-- Date Format -->
                             <div>
                                <x-input-label for="date_format" :value="__('Date Format')" />
                                <select id="date_format" name="date_format" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="Y-m-d" {{ ($settings['date_format'] ?? '') == 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD</option>
                                    <option value="d/m/Y" {{ ($settings['date_format'] ?? '') == 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY</option>
                                    <option value="m/d/Y" {{ ($settings['date_format'] ?? '') == 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY</option>
                                </select>
                            </div>
                         </div>


                        <div class="flex items-center justify-end mt-8">
                            <x-primary-button>
                                {{ __('Save Settings') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
