@extends('layouts.app')
@section('title', 'Checkout')
@section('content')
@php($total = $items->sum(fn($item) => $item->quantity * $item->gig->price))
<div class="form-container">
    <h2>Payment</h2>
    <p>Total: ${{ number_format($total, 2) }}</p>
    <p class="text-muted">Demo mode payment.</p>
    <div class="compact-list">
        @foreach($items as $item)
            <div>
                <strong>{{ $item->gig_title ?: $item->gig->title }}</strong>
                <span>{{ $item->quantity }} x ${{ number_format($item->unit_price ?: $item->gig->price, 2) }}</span>
            </div>
        @endforeach
    </div>
    <form method="post" action="{{ route('checkout.pay') }}">
        @csrf
        <div class="form-group"><label>Payment Method</label><select name="payment_method"><option value="card">Card</option><option value="paypal">PayPal</option></select></div>
        <button class="btn btn-success btn-block">Pay Now</button>
    </form>
</div>
@endsection
