@extends('admin.layout')

@section('title', 'Plans - Admin')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="fas fa-box"></i> Plans</h1>
        <p class="text-muted">Manage hosting plans</p>
    </div>
    <a href="{{ route('admin.plans.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add Plan
    </a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Orders</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($plans as $plan)
                    <tr>
                        <td><strong>{{ $plan->name }}</strong></td>
                        <td>{{ $plan->category->name }}</td>
                        <td>
                            @if($plan->price_monthly)
                                ${{ number_format($plan->price_monthly, 2) }}/mo
                            @elseif($plan->price_yearly)
                                ${{ number_format($plan->price_yearly, 2) }}/yr
                            @else
                                Custom
                            @endif
                        </td>
                        <td>
                            @if($plan->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td>{{ $plan->orders_count ?? 0 }}</td>
                        <td>
                            <a href="{{ route('admin.plans.edit', $plan) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.plans.destroy', $plan) }}" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            No plans yet. <a href="{{ route('admin.plans.create') }}">Create one</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<div class="d-flex justify-content-center mt-4">
    {{ $plans->links() }}
</div>
@endsection
