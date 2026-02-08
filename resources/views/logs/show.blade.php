<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Log Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Log details not really needed as index covers most, but implementing for completeness if I made a link to it. -->
                    <!-- Currently no link to show log details in index, but controller supports it. -->
                     <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Date/Time</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $log->created_at->format('Y-m-d H:i:s') }}</dd>
                        </div>
                         <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">User</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $log->user->name ?? 'System' }}</dd>
                        </div>
                         <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Action</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $log->action }}</dd>
                        </div>
                         <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Details</dt>
                            <dd class="mt-1 text-sm text-gray-900 bg-gray-50 p-4 rounded">{{ $log->details }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
