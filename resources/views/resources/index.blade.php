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
            
            <!-- Monthly Breakdown (Interactive Chart/Table) -->
             <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6" x-data="budgetBreakdown({{ $year }})">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Monthly Spending Breakdown</h3>
                    <div class="grid grid-cols-4 md:grid-cols-6 lg:grid-cols-12 gap-2">
                        @for($i = 1; $i <= 12; $i++)
                            @php 
                                $spent = $monthlySpent[$i] ?? 0;
                                $monthName = DateTime::createFromFormat('!m', $i)->format('M');
                                $isOver = $spent > $budget->monthly_budget;
                            @endphp
                            <div @click="openModal({{ $i }}, '{{ $monthName }}')" class="cursor-pointer transition-transform hover:scale-105 text-center p-2 border rounded shadow-sm {{ $isOver ? 'bg-red-50 border-red-300 ring-1 ring-red-400' : 'bg-gray-50 border-gray-200 hover:bg-gray-100' }}" title="Click to view details">
                                <div class="text-xs font-bold {{ $isOver ? 'text-red-700' : 'text-gray-500' }}">{{ $monthName }}</div>
                                <div class="text-sm font-semibold {{ $isOver ? 'text-red-700' : 'text-gray-800' }}"><x-currency-amount :amount="$spent" :decimals="0" /></div>
                            </div>
                        @endfor
                    </div>
                     <div class="mt-2 text-xs text-gray-500 flex justify-between">
                         <span>Click any month card to see a detailed cost breakdown.</span>
                         <span>Monthly Budget Limit: <span class="font-bold text-gray-800"><x-currency-amount :amount="$budget->monthly_budget" /></span></span>
                     </div>
                </div>

                <!-- Breakdown Modal -->
                <div x-show="isOpen" style="display: none;" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                    
                    <div x-show="isOpen" x-transition.opacity class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

                    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                            <div x-show="isOpen" x-transition.opacity @click.away="isOpen = false" class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl">
                                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4 border-b">
                                    <div class="sm:flex sm:items-start">
                                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                            <h3 class="text-base font-semibold leading-6 text-gray-900" id="modal-title">
                                                Spending Breakdown for <span x-text="selectedMonthName"></span> <span x-text="year"></span>
                                            </h3>
                                            <div class="mt-2">
                                                
                                                <div x-show="isLoading" class="flex justify-center py-8">
                                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                    </svg>
                                                    <span class="text-gray-600">Loading breakdown...</span>
                                                </div>

                                                <div x-show="!isLoading && breakdownData.length === 0" class="text-center py-8 text-gray-500">
                                                    No expenses recorded for this month.
                                                </div>

                                                <div x-show="!isLoading && breakdownData.length > 0" class="mt-4">
                                                    <table class="min-w-full divide-y divide-gray-200">
                                                        <thead class="bg-gray-50">
                                                            <tr>
                                                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                                                                <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                                                <th scope="col" class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                                                <th scope="col" class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">% of Total</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="bg-white divide-y divide-gray-200">
                                                            <template x-for="item in breakdownData" :key="item.id">
                                                                <tr>
                                                                    <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900" x-text="item.name"></td>
                                                                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                                                        <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset" 
                                                                            :class="item.type === 'Infrastructure' ? 'bg-indigo-50 text-indigo-700 ring-indigo-600/20' : 'bg-gray-50 text-gray-600 ring-gray-500/10'"
                                                                            x-text="item.type"></span>
                                                                    </td>
                                                                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900 font-semibold text-right" x-text="formatCurrency(item.amount)"></td>
                                                                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500 text-right">
                                                                        <div class="flex items-center justify-end">
                                                                            <span class="w-8 inline-block text-right" x-text="item.percentage + '%'"></span>
                                                                            <div class="ml-2 w-16 bg-gray-200 rounded-full h-1.5 dark:bg-gray-700 overflow-hidden">
                                                                               <div class="bg-indigo-600 h-1.5 rounded-full" :style="'width: ' + item.percentage + '%'"></div>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            </template>
                                                        </tbody>
                                                        <tfoot>
                                                            <tr class="bg-gray-50 font-bold">
                                                                <td colspan="2" class="px-3 py-3 text-right text-sm text-gray-900">Total:</td>
                                                                <td class="px-3 py-3 text-right text-sm text-indigo-700" x-text="formatCurrency(totalAmount)"></td>
                                                                <td></td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                                    <button type="button" @click="isOpen = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function budgetBreakdown(year) {
                        return {
                            isOpen: false,
                            isLoading: false,
                            year: year,
                            selectedMonth: null,
                            selectedMonthName: '',
                            breakdownData: [],
                            totalAmount: 0,
                            openModal(month, monthName) {
                                this.selectedMonth = month;
                                this.selectedMonthName = monthName;
                                this.isOpen = true;
                                this.fetchData();
                            },
                            fetchData() {
                                this.isLoading = true;
                                this.breakdownData = [];
                                fetch(`/api/budgets/${this.year}/months/${this.selectedMonth}/breakdown`)
                                    .then(res => res.json())
                                    .then(data => {
                                        this.breakdownData = data.breakdown || [];
                                        this.totalAmount = data.total || 0;
                                        this.isLoading = false;
                                    })
                                    .catch(err => {
                                        console.error('Error fetching breakdown', err);
                                        this.isLoading = false;
                                    });
                            },
                            formatCurrency(amount) {
                                return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(amount);
                            }
                        }
                    }
                </script>
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
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice Attachment</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Added By</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($resources as $resource)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ date('M', mktime(0, 0, 0, $resource->month, 1)) }} {{ $resource->year }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $resource->title }}
                                        @if($resource->description)
                                            <p class="text-xs text-gray-400 truncate max-w-xs">{{ $resource->description }}</p>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                         <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $resource->type === 'invoice' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ ucfirst($resource->type) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">
                                        ${{ number_format($resource->amount, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($resource->attachment)
                                            <a href="{{ Storage::url($resource->attachment) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900">View Attachment</a>
                                        @else
                                            <span class="text-gray-400">None</span>
                                        @endif
                                    </td>
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
