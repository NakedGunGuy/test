<div>
    <!-- Filters -->
    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                <input
                    type="text"
                    id="name"
                    wire:model.live="name"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    placeholder="Search products..."
                >
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label for="min_price" class="block text-sm font-medium text-gray-700">Min Price</label>
                    <input
                        type="number"
                        id="min_price"
                        wire:model.live="min_price"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        placeholder="0"
                        step="0.01"
                    >
                </div>

                <div>
                    <label for="max_price" class="block text-sm font-medium text-gray-700">Max Price</label>
                    <input
                        type="number"
                        id="max_price"
                        wire:model.live="max_price"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        placeholder="1000"
                        step="0.01"
                    >
                </div>
            </div>

            <div>
                <label for="set_id" class="block text-sm font-medium text-gray-700">Set</label>
                <select
                    id="set_id"
                    wire:model.live="set_id"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                >
                    <option value="">All Sets</option>
                    @foreach($sets as $set)
                        <option value="{{ $set->id }}">{{ $set->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="is_foil" class="block text-sm font-medium text-gray-700">Foil</label>
                <select
                    id="is_foil"
                    wire:model.live="is_foil"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                >
                    <option value="">All</option>
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                </select>
            </div>

            <div class="flex items-end">
                <label class="flex items-center">
                    <input
                        type="checkbox"
                        wire:model.live="in_stock_only"
                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                    <span class="ml-2 text-sm text-gray-700">In Stock Only</span>
                </label>
            </div>
        </div>
    </div>

    <!-- View Toggle and Per Page -->
    <div class="flex justify-between items-center mb-4">
        <div class="flex space-x-2">
            <button
                wire:click="setView('grid')"
                class="px-3 py-1 text-sm rounded-md {{ $view === 'grid' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700' }}"
            >
                Grid
            </button>
            <button
                wire:click="setView('list')"
                class="px-3 py-1 text-sm rounded-md {{ $view === 'list' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700' }}"
            >
                List
            </button>
        </div>

        <div>
            <label for="per_page" class="text-sm text-gray-700 mr-2">Per page:</label>
            <select
                id="per_page"
                wire:model.live="per_page"
                class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
    </div>

    <!-- Products -->
    @if($view === 'grid')
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @forelse($products as $product)
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-4">
                        <div class="flex justify-center">
                            <div class="bg-gray-200 border-2 border-dashed rounded-xl w-16 h-16" />
                        </div>

                        <h3 class="mt-4 text-lg font-medium text-gray-900">
                            {{ $product->edition?->card?->name ?? $product->name }}
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            {{ $product->edition?->set?->name }}
                        </p>

                        <p class="mt-2 text-sm text-gray-500">
                            @if($product->is_foil)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Foil
                                </span>
                            @endif
                            @if($product->is_used)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Used
                                </span>
                            @endif
                        </p>

                        <div class="mt-4 flex justify-between items-center">
                            <span class="text-lg font-medium text-gray-900">
                                ${{ number_format($product->price, 2) }}
                            </span>
                            <span class="text-sm text-gray-500">
                                Qty: {{ $product->quantity }}
                            </span>
                        </div>

                        <div class="mt-4">
                            <button
                                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            >
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <p class="col-span-full text-center text-gray-500 py-8">No products found.</p>
            @endforelse
        </div>
    @else
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <ul class="divide-y divide-gray-200">
                @forelse($products as $product)
                    <li>
                        <div class="px-4 py-4 sm:px-6 flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="bg-gray-200 border-2 border-dashed rounded-xl w-16 h-16 mr-4" />
                                <div>
                                    <h3 class="text-sm font-medium text-gray-900">
                                        {{ $product->edition?->card?->name ?? $product->name }}
                                    </h3>
                                    <p class="text-sm text-gray-500">
                                        {{ $product->edition?->set?->name }}
                                    </p>
                                    <p class="mt-1 text-sm text-gray-500">
                                        @if($product->is_foil)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 mr-2">
                                                Foil
                                            </span>
                                        @endif
                                        @if($product->is_used)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Used
                                            </span>
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center space-x-4">
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-900">
                                        ${{ number_format($product->price, 2) }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        Qty: {{ $product->quantity }}
                                    </p>
                                </div>

                                <div>
                                    <button
                                        class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                    >
                                        Add to Cart
                                    </button>
                                </div>
                            </div>
                        </div>
                    </li>
                @empty
                    <li>
                        <div class="px-4 py-4 sm:px-6 text-center text-gray-500">
                            No products found.
                        </div>
                    </li>
                @endforelse
            </ul>
        </div>
    @endif

    <!-- Pagination -->
    @if($products->hasPages())
        <div class="mt-6">
            {{ $products->links() }}
        </div>
    @endif
</div>

<script>
function setView(view) {
    @this.call('updatedView');
    @this.view = view;
}
</script>
