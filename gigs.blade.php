@extends('layouts.app')
@section('title', 'Admin Gigs')
@section('content')
<h2>Gigs</h2>
<div class="table-container"><table><thead><tr><th>Title</th><th>Freelancer</th><th>Price</th><th>Status</th><th></th></tr></thead><tbody>
@foreach($gigs as $gig)<tr><td>{{ $gig->title }}</td><td>{{ $gig->freelancer->name }}</td><td>${{ number_format($gig->price, 2) }}</td><td>{{ $gig->status }}</td><td><form method="post" action="{{ route('admin.gigs.toggle', $gig) }}">@csrf <button class="btn btn-sm btn-danger">Toggle</button></form></td></tr>@endforeach
</tbody></table></div>
@endsection
