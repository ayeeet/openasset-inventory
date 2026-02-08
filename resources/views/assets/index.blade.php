<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Assets') }}
            </h2>
            @if(auth()->user()->hasWriteAccess())
            <a href="{{ route('assets.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Add Asset
            </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{-- Server-side filters: list always comes from controller (same as DB) --}}
                    <form method="GET" action="{{ route('assets.index') }}" class="mb-4 flex flex-wrap items-center gap-4">
                        <input type="hidden" name="sort_field" value="{{ $sortField }}" />
                        <input type="hidden" name="sort_direction" value="{{ $sortDirection }}" />
                        <input type="text" name="search" value="{{ old('search', $search) }}" placeholder="Search assets..." class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <select name="category" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">All Categories</option>
                            @foreach($categories as $id => $name)
                                <option value="{{ $id }}" {{ $categoryFilter === $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="px-4 py-2 bg-gray-200 rounded-md text-sm font-medium hover:bg-gray-300">Filter</button>
                    </form>

                    @if(session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <div class="overflow-x-auto shadow-sm sm:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    @php
                                        $sortUrl = fn($field) => request()->fullUrlWithQuery([
                                            'sort_field' => $field,
                                            'sort_direction' => ($sortField === $field && $sortDirection === 'asc') ? 'desc' : 'asc',
                                            'search' => $search,
                                            'category' => $categoryFilter,
                                        ]);
                                    @endphp
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <a href="{{ $sortUrl('name') }}" class="cursor-pointer">Name @if($sortField === 'name'){{ $sortDirection === 'asc' ? '↑' : '↓' }}@endif</a>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Serial</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <a href="{{ $sortUrl('location_id') }}" class="cursor-pointer">Location @if($sortField === 'location_id'){{ $sortDirection === 'asc' ? '↑' : '↓' }}@endif</a>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned To</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <a href="{{ $sortUrl('status') }}" class="cursor-pointer">Status @if($sortField === 'status'){{ $sortDirection === 'asc' ? '↑' : '↓' }}@endif</a>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($assets as $asset)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        <a href="{{ route('assets.show', $asset) }}" class="text-indigo-600 hover:text-indigo-900">
                                            {{ $asset->name }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $asset->serial_number ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $asset->category ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $asset->location->name ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $asset->assignedUser->name ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            {{ $asset->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $asset->status === 'maintenance' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $asset->status === 'retired' ? 'bg-red-100 text-red-800' : '' }}
                                            {{ $asset->status === 'lost' ? 'bg-gray-100 text-gray-800' : '' }}
                                        ">
                                            {{ ucfirst($asset->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        @if(auth()->user()->hasWriteAccess())
                                        <a href="{{ route('assets.edit', $asset) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                        @if(auth()->user()->role === 'admin')
                                            <form method="POST" action="{{ route('assets.destroy', $asset) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this asset?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                            </form>
                                        @endif
                                        @else
                                        —
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">No assets found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $assets->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
