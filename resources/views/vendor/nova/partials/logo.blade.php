@php
$color = request()->route()->uri === 'login' ? 'text-white' : 'text-black';
@endphp
@if (request()->request->get('is_main_store'))
<h2 class="{{ $color }}">Sales Tracker</h2>
@else
@php
$store = request()->request->get('store');
@endphp
    <h2 class="{{ $color }}">{{ $store->name }}</h2>
@endif
