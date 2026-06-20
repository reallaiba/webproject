@extends('layouts.app')
@section('title', 'Post Project')
@section('content')
<div class="form-container form-wide">
    <h2>Post a Project</h2>
    <form method="post" action="{{ route('projects.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="form-group"><label>Title</label><input name="title" required></div>
        <div class="form-group"><label>Description</label><textarea name="description" required></textarea></div>
        <div class="form-group"><label>Category</label><select name="category_id"><option value="">Select category</option>@foreach($categories as $category)<option value="{{ $category->id }}">{{ $category->name }}</option>@endforeach</select></div>
        <div class="form-group"><label>Budget</label><input type="number" name="budget" min="1" step="0.01" required></div>
        <div class="form-group"><label>Attachment</label><input type="file" name="attachment"></div>
        <button class="btn btn-primary btn-block">Post Project</button>
    </form>
</div>
@endsection
