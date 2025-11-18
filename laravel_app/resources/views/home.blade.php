@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-3xl font-bold mb-6">Welcome to {{ config('app.name', 'Cardpoint') }}</h1>
                    <p class="mb-4">Discover our collection of cards and products.</p>

                    <div class="mt-8">
                        <a href="{{ route('discover') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Browse Products
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection