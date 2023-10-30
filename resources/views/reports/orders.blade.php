@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
<form method="POST" action="{{ route('export.orders') }}" name="orders-to-spreadsheet" id="orders-to-spreadsheet">
    @csrf
    <div class="mb-2">
        <h2 class="text-90 font-normal text-2xl mb-3">Export as Spreadsheet</h2>
        <div class="card">
            <div class="flex border-b border-40">
               <div class="w-1/3 flex justify-center items-center">
                    <div class="px-8 py-6 w-1/5">
                        <label for="created_at" class="inline-block text-80 pt-2 leading-tight">Store</label>
                    </div>
                    <div class="py-6 px-8 w-full">
                        <select id="store" class="block w-full form-control-sm form-select" name="store">
                            <option value="{{ APP_SELECT_ALL }}" selected>All</option>
                            @foreach (\App\Models\Store::all() as $key => $store)
                            <option value="{{ $store->domain }}">{{ $store->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
               <div class="w-1/3 flex justify-center items-center">
                    <div class="px-8 py-6 w-1/5">
                        <label for="created_by" class="inline-block text-80 pt-2 leading-tight">Created By</label>
                    </div>
                    <div class="py-6 px-8 w-full">
                        @php
                        /** @var \Illuminate\Database\Query\Builder $users **/
                        $users = \App\Models\UserInformation::query()->getStaffs()->get();
                        @endphp
                        <select id="created_by" class="block w-full form-control-sm form-select" name="created_by">
                            <option value="*" selected>Everyone</option>
                            @foreach ($users as $key => $user)
                            @php
                            $stores = '';
                            if ($availableStores = $user->user->available_stores) {
                                $stores = "(Stores: $availableStores)";
                            }
                            @endphp
                            <option value="{{ $user->user_id }}">{{ $user->full_name }} {{ $stores }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
               <div class="w-1/3 flex justify-center items-center">
                    <div class="px-8 py-6 w-1/5">
                        <label for="sales_channel" class="inline-block text-80 pt-2 leading-tight">Sales Channel</label>
                    </div>
                    <div class="py-6 px-8 w-full">
                        <input name="sales_channel" id="sales_channel" type="text" placeholder="Sales Channel" class="w-full form-control form-input form-input-bordered">
                    </div>
                </div>
            </div>
            <div class="flex border-b border-40">
                <div class="w-1/2 flex items-center justify-center">
                    <div class="px-8 py-6 w-1/5">
                        <label for="created_at_from" class="inline-block text-80 pt-2 leading-tight">From</label>
                    </div>
                    <div class="py-6 px-8 w-full">
                        <input name="created_at_from" id="date" dusk="date" type="date" placeholder="Orders Created From Date" class="w-full form-control form-input form-input-bordered">
                    </div>
                </div>
                <div class="w-1/2 flex items-center justify-center">
                    <div class="px-8 py-6 w-1/5">
                        <label for="created_at_to" class="inline-block text-80 pt-2 leading-tight">To</label>
                    </div>
                    <div class="py-6 px-8 w-full">
                        <input name="created_at_to" id="date" dusk="date" type="date" placeholder="Orders Created From Date" class="w-full form-control form-input form-input-bordered">
                    </div>
                </div>
            </div>
            <div class="flex border-b border-40">
                <div class="w-1/2 flex items-center justify-center">
                    <div class="px-8 py-6 w-1/5">
                        <label for="order_status" class="inline-block text-80 pt-2 leading-tight">Order Status</label>
                    </div>
                    <div class="py-6 px-8 w-full">
                        <select id="order_status" class="block w-full form-control-sm form-select" name="order_status">
                            <option value="{{ APP_SELECT_ALL }}" selected>All</option>
                            @foreach (\App\Models\Order::orderStatuses() as $key => $status)
                            <option value="{{ $status }}">{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="w-1/2 flex items-center justify-center">
                    <div class="px-8 py-6 w-1/5">
                        <label for="payment_status" class="inline-block text-80 pt-2 leading-tight">Payment Status</label>
                    </div>
                    <div class="py-6 px-8 w-full">
                        <select id="payment_status" class="block w-full form-control-sm form-select" name="payment_status">
                            <option value="{{ APP_SELECT_ALL }}" selected>All</option>
                            @foreach (\App\Models\Order::paymentStatuses() as $key => $status)
                            <option value="{{ $status }}">{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="flex justify-end">
        <button type="submit" class="btn btn-default btn-primary inline-flex relative" dusk="create-button">
            Generate Report
        </button>
    </div>
</form>
