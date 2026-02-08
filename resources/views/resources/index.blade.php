<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Resources & Expenses') }} ({{ $year }})
            </h2>
            @if(auth()->user()->hasWriteAccess())
            <div class="space-x-2">
                <a href="{{ route('budgets.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Manage Budgets
                </a>
                <a href="{{ route('resources.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Add Expense
                </a>
            </div>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- USD ⇄ PHP Converter -->
            @if(in_array($currency, ['$', '₱']))
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-3">Currency Converter (1 USD = {{ $usd_to_php_rate }} PHP)</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="conv_usd" class="block text-sm font-medium text-gray-700">USD ($)</label>
                        <input type="number" id="conv_usd" step="0.01" min="0" placeholder="0.00" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="conv_php" class="block text-sm font-medium text-gray-700">PHP (₱)</label>
                        <input type="number" id="conv_php" step="0.01" min="0" placeholder="0.00" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                </div>
                <p class="mt-2 text-xs text-gray-500">Convert between USD and PHP. Values update as you type.</p>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var rate = {{ $usd_to_php_rate }};
                    var usd = document.getElementById('conv_usd');
                    var php = document.getElementById('conv_php');
                    if (!usd || !php) return;
                    usd.addEventListener('input', function() {
                        var v = parseFloat(this.value) || 0;
                        php.value = v ? (v * rate).toFixed(2) : '';
                    });
                    php.addEventListener('input', function() {
                        var v = parseFloat(this.value) || 0;
                        usd.value = v ? (v / rate).toFixed(2) : '';
                    });
                });
            </script>
            @endif

            <!-- Budget Overview Cards -->
            @if($budget)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Annual Budget -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-gray-500 text-sm font-medium uppercase">Annual Budget</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900"><x-currency-amount :amount="$budget->annual_budget" /></div>
                    <div class="text-sm text-gray-500 mt-1">For Year {{ $year }}</div>
                </div>

                 <!-- Total Spent -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-gray-500 text-sm font-medium uppercase">Total Spent</div>
                    <div class="mt-2 text-3xl font-bold {{ $totalSpent > $budget->annual_budget ? 'text-red-600' : 'text-blue-600' }}">
                        <x-currency-amount :amount="$totalSpent" />
                    </div>
                    <div class="text-sm text-gray-500 mt-1">
                        {{ number_format(($totalSpent / $budget->annual_budget) * 100, 1) }}% used
                    </div>
                </div>

                 <!-- Remaining -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-gray-500 text-sm font-medium uppercase">Remaining</div>
                    <div class="mt-2 text-3xl font-bold {{ ($budget->annual_budget - $totalSpent) < 0 ? 'text-red-600' : 'text-green-600' }}">
                        <x-currency-amount :amount="$budget->annual_budget - $totalSpent" />
                    </div>
                </div>
            </div>
            
            <!-- Monthly Breakdown (Simulated Chart/Table) -->
             <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Monthly Spending</h3>
                    <div class="grid grid-cols-4 md:grid-cols-6 lg:grid-cols-12 gap-2">
                        @for($i = 1; $i <= 12; $i++)
                            @php 
                                $spent = $monthlySpent[$i] ?? 0;
                                $monthName = DateTime::createFromFormat('!m', $i)->format('M');
                                $isOver = $spent > $budget->monthly_budget;
                            @endphp
                            <div class="text-center p-2 border rounded {{ $isOver ? 'bg-red-50 border-red-200' : 'bg-gray-50' }}">
                                <div class="text-xs font-bold text-gray-500">{{ $monthName }}</div>
                                <div class="text-sm font-semibold {{ $isOver ? 'text-red-600' : 'text-gray-800' }}"><x-currency-amount :amount="$spent" :decimals="0" /></div>
                            </div>
                        @endfor
                    </div>
                     <div class="mt-2 text-xs text-gray-500 text-right">Monthly Budget Limit: <x-currency-amount :amount="$budget->monthly_budget" /></div>
                </div>
            </div>
            @else
             <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                <div class="flex">
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            No budget found for {{ $year }}. @if(auth()->user()->hasWriteAccess())Please <a href="{{ route('budgets.create') }}" class="font-bold underline">create a budget</a> to track spending properly.@else Contact an administrator to set up a budget.@endif
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                     @if(session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created By</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($resources as $resource)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ DateTime::createFromFormat('!m', $resource->month)->format('M') }} {{ $resource->year }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $resource->title }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                         <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $resource->type === 'invoice' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                            {{ ucfirst($resource->type) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><x-currency-amount :amount="$resource->amount" /></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $resource->creator->name ?? 'Unknown' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        @if(auth()->user()->hasWriteAccess())
                                        <a href="{{ route('resources.edit', $resource) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                        <form action="{{ route('resources.destroy', $resource) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                        </form>
                                        @else
                                        —
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">No entries found for this year.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                     <div class="mt-4">
                        {{ $resources->appends(['year' => $year])->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
