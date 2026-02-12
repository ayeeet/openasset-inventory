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
                                        <img id="current-logo-preview" src="{{ Storage::url($settings['app_logo']) }}" alt="Current Logo" class="h-56 w-auto object-contain">
                                    </div>
                                @else
                                    <div id="current-logo-preview" class="mt-2 mb-2 hidden"></div>
                                @endif
                                <input id="app_logo" type="file" name="app_logo" accept="image/*" class="block mt-1 w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
                                <p class="mt-1 text-sm text-gray-500">SVG, PNG, JPG or GIF (MAX. 10MB). You may crop the image before uploading.</p>

                                <!-- Cropping modal -->
                                <div id="cropper-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
                                    <div class="bg-white rounded-lg overflow-hidden max-w-3xl w-full mx-4">
                                        <div class="p-4">
                                            <h3 class="font-semibold mb-2">Crop Logo</h3>
                                            <div class="w-full overflow-hidden">
                                                <img id="cropper-image" src="" alt="Cropper Preview" class="max-h-[60vh] mx-auto" />
                                            </div>
                                        </div>
                                        <div class="p-4 bg-gray-50 flex justify-end gap-2">
                                            <button id="cropper-cancel" type="button" class="px-4 py-2 bg-white border rounded">Cancel</button>
                                            <button id="cropper-apply" type="button" class="px-4 py-2 bg-blue-600 text-white rounded">Apply Crop</button>
                                        </div>
                                    </div>
                                </div>

                                <link  href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
                                <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
                                <script>
                                    document.addEventListener('DOMContentLoaded', function () {
                                        const input = document.getElementById('app_logo');
                                        const modal = document.getElementById('cropper-modal');
                                        const img = document.getElementById('cropper-image');
                                        const currentPreview = document.getElementById('current-logo-preview');
                                        let cropper = null;

                                        function openModal() {
                                            modal.classList.remove('hidden');
                                        }
                                        function closeModal() {
                                            modal.classList.add('hidden');
                                            if (cropper) { cropper.destroy(); cropper = null; }
                                            img.src = '';
                                        }

                                        input.addEventListener('change', function (e) {
                                            const file = e.target.files && e.target.files[0];
                                            if (!file) return;

                                            // Only allow images
                                            if (!file.type.startsWith('image/')) return;

                                            const reader = new FileReader();
                                            reader.onload = function (event) {
                                                img.src = event.target.result;
                                                openModal();
                                                // initialize cropper after image loads
                                                img.onload = function () {
                                                    cropper = new Cropper(img, {
                                                        aspectRatio: 1,
                                                        viewMode: 1,
                                                        movable: true,
                                                        zoomable: true,
                                                        responsive: true,
                                                        background: false,
                                                    });
                                                }
                                            }
                                            reader.readAsDataURL(file);
                                        });

                                        document.getElementById('cropper-cancel').addEventListener('click', function () {
                                            // reset input
                                            input.value = '';
                                            closeModal();
                                        });

                                        document.getElementById('cropper-apply').addEventListener('click', function () {
                                            if (!cropper) return;
                                            // get cropped canvas at 1024x1024 for higher-resolution logos
                                            const canvas = cropper.getCroppedCanvas({ width: 1024, height: 1024, imageSmoothingQuality: 'high' });
                                            canvas.toBlob(function (blob) {
                                                const fileName = 'app_logo_cropped.' + (blob.type.split('/')[1] || 'png');
                                                const croppedFile = new File([blob], fileName, { type: blob.type, lastModified: Date.now() });

                                                // Replace the file input's files with the cropped file
                                                const dataTransfer = new DataTransfer();
                                                dataTransfer.items.add(croppedFile);
                                                input.files = dataTransfer.files;

                                                // Update preview
                                                const url = URL.createObjectURL(croppedFile);
                                                if (currentPreview.tagName === 'IMG') {
                                                    currentPreview.src = url;
                                                    currentPreview.classList.remove('hidden');
                                                } else {
                                                    currentPreview.innerHTML = '<img src="'+url+'" class="h-32 w-auto object-contain" />';
                                                    currentPreview.classList.remove('hidden');
                                                }

                                                closeModal();
                                            }, 'image/png', 0.9);
                                        });
                                    });
                                </script>
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
