<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Infrastructure Costs') }} ({{ $year }})
            </h2>
            @if(auth()->user()->hasWriteAccess())
            <div class="space-x-2">
                <a href="{{ route('infrastructure-costs.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Add Cost
                </a>
            </div>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if($budget)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Infra Total Spent -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-indigo-500">
                    <div class="text-gray-500 text-sm font-medium uppercase">Infrastructure Total</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900">
                        <x-currency-amount :amount="$totalInfraSpent" />
                    </div>
                </div>

                <!-- Total Spent (Resources + Infra) -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                    <div class="text-gray-500 text-sm font-medium uppercase">Total Global Spent</div>
                    <div class="mt-2 text-2xl font-bold text-gray-900">
                        <x-currency-amount :amount="$totalOverallSpent" />
                    </div>
                    <div class="text-xs text-gray-500 mt-1">
                        Out of <x-currency-amount :amount="$budget->annual_budget" /> annual budget
                    </div>
                </div>

                 <!-- Remaining Budget -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-{{ ($budget->annual_budget - $totalOverallSpent) < 0 ? 'red' : 'green' }}-500">
                    <div class="text-gray-500 text-sm font-medium uppercase">Remaining Annual Budget</div>
                    <div class="mt-2 text-2xl font-bold {{ ($budget->annual_budget - $totalOverallSpent) < 0 ? 'text-red-600' : 'text-green-600' }}">
                        <x-currency-amount :amount="$budget->annual_budget - $totalOverallSpent" />
                    </div>
                </div>
            </div>
            
            <!-- Monthly Breakdown of Infra -->
             <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Infrastructure Spending Trend</h3>
                    <div class="grid grid-cols-4 md:grid-cols-6 lg:grid-cols-12 gap-2">
                        @for($i = 1; $i <= 12; $i++)
                            @php 
                                $spent = $monthlySpent[$i] ?? 0;
                                $monthName = DateTime::createFromFormat('!m', $i)->format('M');
                            @endphp
                            <div class="text-center p-2 border rounded bg-gray-50">
                                <div class="text-xs font-bold text-gray-500">{{ $monthName }}</div>
                                <div class="text-sm font-semibold text-indigo-700">
                                    <x-currency-amount :amount="$spent" :decimals="0" />
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
            @else
             <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                <p class="text-sm text-yellow-700">
                    No budget found for {{ $year }}. Please create a budget in the Resources module.
                </p>
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
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Added By</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($costs as $cost)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ date('M', mktime(0, 0, 0, $cost->month, 1)) }} {{ $cost->year }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $cost->service_name }}
                                        @if($cost->description)
                                            <p class="text-xs text-gray-400 truncate max-w-xs">{{ $cost->description }}</p>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $cost->category ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">
                                        ${{ number_format($cost->amount, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $cost->creator->name ?? 'Unknown' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        @if(auth()->user()->hasWriteAccess())
                                        <a href="{{ route('infrastructure-costs.edit', $cost) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                        <form action="{{ route('infrastructure-costs.destroy', $cost) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure?');">
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
                        {{ $costs->appends(['year' => $year])->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
