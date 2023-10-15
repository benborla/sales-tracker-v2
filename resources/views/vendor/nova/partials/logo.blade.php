@if (request()->getPathInfo() === '/login')
<h2 class="text-white">
{{ is_main_store() ? 'Sales Tracker' : store()->name }}
</h2>
@else
<h2 class="text-white">Sales Tracker</h2>
@endif
