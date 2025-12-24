@extends('app')

@section('title', 'My Orders - ' . config('app.name'))

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-3">
            <!-- Sidebar -->
            <div class="list-group">
                <a href="{{ route('dashboard.index') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a href="{{ route('dashboard.orders') }}" class="list-group-item list-group-item-action active">
                    <i class="fas fa-shopping-bag"></i> My Orders
                </a>
                <a href="{{ url('/shop') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-store"></i> Browse Services
                </a>
            </div>
        </div>

        <div class="col-md-9">
            <h1>My Orders</h1>
            <hr>

            @if($orders->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Plan</th>
                                <th>Category</th>
                                <th>Amount</th>
                                <th>Billing Cycle</th>
                                <th>Status</th>
                                <th>Ordered</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                                <tr>
                                    <td>
                                        <strong>{{ $order->plan->name }}</strong>
                                    </td>
                                    <td>{{ $order->plan->category->name }}</td>
                                    <td>${{ number_format($order->amount, 2) }}</td>
                                    <td>{{ ucfirst($order->billing_cycle) }}</td>
                                    <td>
                                        @switch($order->status)
                                            @case('active')
                                                <span class="badge bg-success">Active</span>
                                                @break
                                            @case('pending')
                                                <span class="badge bg-warning text-dark">Pending</span>
                                                @break
                                            @case('suspended')
                                                <span class="badge bg-danger">Suspended</span>
                                                @break
                                            @case('cancelled')
                                                <span class="badge bg-secondary">Cancelled</span>
                                                @break
                                            @case('expired')
                                                <span class="badge bg-secondary">Expired</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">{{ ucfirst($order->status) }}</span>
                                        @endswitch
                                    </td>
                                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $orders->links() }}
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> 
                    You don't have any orders yet. <a href="{{ url('/shop') }}">Start shopping now</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
