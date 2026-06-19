@extends('layouts.app')

@section('title', $product->name)
@section('page-title', $product->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">{{ $product->name }}</h4>
    <a href="{{ route('products.edit', $product) }}" class="btn btn-primary"><i class="bi bi-pencil me-1"></i>Edit</a>
</div>

<div class="card shadow-sm" style="max-width:500px">
    <div class="card-body">
        @if($product->sku)<div class="mb-2"><small class="text-muted">SKU:</small> {{ $product->sku }}</div>@endif
        @if($product->category)<div class="mb-2"><small class="text-muted">Category:</small> {{ $product->category }}</div>@endif
        <div class="mb-2"><small class="text-muted">Price:</small> <strong>£{{ number_format($product->unit_price, 2) }}</strong> per {{ $product->unit }}</div>
        <div class="mb-2"><small class="text-muted">Status:</small> {!! $product->is_active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>' !!}</div>
        @if($product->description)<p class="text-muted mt-2">{{ $product->description }}</p>@endif
    </div>
</div>
@endsection
