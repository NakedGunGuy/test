@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex items-start justify-between mb-6">
                    <div>
                        <h1 class="text-3xl font-bold mb-2">{{ $product->edition?->card?->name ?? $product->name }}</h1>
                        <p class="text-gray-600">{{ $product->edition?->set?->name }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-bold text-gray-900">€{{ number_format($product->price, 2) }}</p>
                        <p class="{{ $product->quantity > 0 
                            ? ($product->quantity <= 5 
                                ? 'text-yellow-600 font-bold' 
                                : 'text-green-600') 
                            : 'text-red-600 font-bold' }}">
                            {{ $product->quantity > 0 ? $product->quantity . ' in stock' : 'Out of stock' }}
                        </p>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row gap-8">
                    <div class="md:w-1/3">
                        <img
                            src="{{ asset('images/placeholder-card.jpg') }}"
                            alt="{{ $product->edition?->card?->name ?? $product->name }}"
                            class="w-full rounded-lg shadow-md"
                        />
                    </div>
                    
                    <div class="md:w-2/3">
                        <div class="mb-6">
                            <h2 class="text-xl font-semibold mb-4">Product Details</h2>
                            <ul class="space-y-2">
                                <li class="flex justify-between">
                                    <span class="font-medium">Card Name:</span>
                                    <span>{{ $product->edition?->card?->name ?? $product->name }}</span>
                                </li>
                                <li class="flex justify-between">
                                    <span class="font-medium">Set:</span>
                                    <span>{{ $product->edition?->set?->name }}</span>
                                </li>
                                <li class="flex justify-between">
                                    <span class="font-medium">Price:</span>
                                    <span>€{{ number_format($product->price, 2) }}</span>
                                </li>
                                <li class="flex justify-between">
                                    <span class="font-medium">Stock:</span>
                                    <span>{{ $product->quantity > 0 ? $product->quantity . ' in stock' : 'Out of stock' }}</span>
                                </li>
                                <li class="flex justify-between">
                                    <span class="font-medium">Condition:</span>
                                    <span>{{ $product->is_used ? 'Used' : 'New' }}</span>
                                </li>
                                <li class="flex justify-between">
                                    <span class="font-medium">Foil:</span>
                                    <span>{{ $product->is_foil ? 'Yes' : 'No' }}</span>
                                </li>
                            </ul>
                        </div>

                        @if($product->quantity > 0)
                            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                                <h3 class="text-lg font-medium mb-3">Add to Cart</h3>
                                <div class="flex items-center space-x-3">
                                    <input
                                        type="number"
                                        min="1"
                                        max="{{ $product->quantity }}"
                                        value="1"
                                        class="w-20 px-3 py-2 border border-gray-300 rounded-md"
                                    >
                                    <button class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                        Add to Cart
                                    </button>
                                </div>
                            </div>
                        @else
                            <div class="mb-6 p-4 bg-red-50 text-red-700 rounded-lg">
                                <p>This product is currently out of stock.</p>
                            </div>
                        @endif
                    </div>
                </div>
                
                <div class="mt-8">
                    <a href="{{ route('discover') }}" class="text-indigo-600 hover:text-indigo-800">
                        ← Back to Discover
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection