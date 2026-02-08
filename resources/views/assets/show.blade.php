<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Asset Details') }}: {{ $asset->name }}
            </h2>
            <div class="space-x-2">
                @if(auth()->user()->hasWriteAccess())
                <a href="{{ route('assets.edit', $asset) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Edit
                </a>
                @endif
                <a href="{{ route('assets.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Back to List
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Information</h3>
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500">Name</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $asset->name }}</dd>
                                </div>
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500">Serial Number</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $asset->serial_number ?? '-' }}</dd>
                                </div>
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500">Category</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $asset->category }}</dd>
                                </div>
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $asset->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $asset->status === 'maintenance' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $asset->status === 'retired' ? 'bg-red-100 text-red-800' : '' }}
                                            {{ $asset->status === 'lost' ? 'bg-gray-100 text-gray-800' : '' }}
                                        ">
                                            {{ ucfirst($asset->status) }}
                                        </span>
                                    </dd>
                                </div>
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500">Purchase Date</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $asset->purchase_date?->format('M d, Y') ?? '-' }}</dd>
                                </div>
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500">Warranty Expiry</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $asset->warranty_expiry?->format('M d, Y') ?? '-' }}</dd>
                                </div>
                            </dl>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Location & Assignment</h3>
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                                <div class="sm:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500">Location</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $asset->location->name ?? 'None' }}</dd>
                                </div>
                                <div class="sm:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500">Assigned To</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        @if($asset->assignedUser)
                                            <div class="font-medium">{{ $asset->assignedUser->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $asset->assignedUser->email }}</div>
                                            @if($asset->assignedUser->department)
                                                <div class="text-xs text-gray-500">{{ $asset->assignedUser->department->name }}</div>
                                            @endif
                                        @else
                                            <span class="text-gray-500">Unassigned</span>
                                        @endif
                                    </dd>
                                </div>
                            </dl>

                             <h3 class="text-lg font-medium text-gray-900 mt-6 mb-4">Notes</h3>
                             <p class="text-sm text-gray-700 bg-gray-50 p-4 rounded-md">
                                {{ $asset->notes ?? 'No notes available.' }}
                             </p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
