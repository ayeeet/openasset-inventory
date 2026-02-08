@props(['amount', 'decimals' => 2])
@php
    $rate = $usd_to_php_rate ?? 59;
    $equiv = \App\Helpers\CurrencyConverter::equivalentAmount((float) $amount, $currency ?? '$', $rate);
@endphp
<span>
    {{ $currency ?? '$' }}{{ number_format((float) $amount, $decimals) }}
    @if($equiv)
        <span class="text-gray-500 text-sm">({{ $equiv['symbol'] }}{{ number_format($equiv['amount'], $decimals) }})</span>
    @endif
</span>
