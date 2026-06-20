@extends('layouts.app')
@section('title', 'Admin Orders')
@section('content')
<h2>Orders</h2>
<div class="table-container"><table><thead><tr><th>Order</th><th>User</th><th>What Ordered</th><th>Total</th><th>Payment</th><th>Status</th></tr></thead><tbody>
@foreach($orders as $order)<tr><td>{{ $order->order_number ?: '#' . $order->id }}</td><td>{{ $order->customer_name ?: ($order->user?->name ?? 'Deleted user') }}</td><td>{{ $order->order_summary ?: 'Gig order' }}</td><td>${{ number_format($order->total_amount, 2) }}</td><td><span class="badge badge-{{ $order->payment_status }}">{{ $order->payment_status }}</span></td><td><span class="badge badge-{{ $order->status }}">{{ $order->status }}</span></td></tr>@endforeach
</tbody></table></div>
@endsection
