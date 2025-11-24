<div class="discover-page">
    <!-- Mobile/Tablet Filter Toggle Button -->
    <div class="md:hidden mb-4">
        <button 
            type="button" 
            wire:click="toggleFilters"
            class="filter-toggle-btn w-full flex justify-between items-center p-3 bg-gray-100 rounded-lg"
        >
            <span class="flex items-center">
                <span class="filter-icon mr-2">🔍</span>
                <span>Search & Filter</span>
            </span>
            <span class="filter-arrow">{{ $show_filters ? '▲' : '▼' }}</span>
        </button>
    </div>

    <!-- Filters -->
    <div class="{{ $show_filters ? 'block' : 'hidden md:block' }} mb-6 p-4 bg-gray-50 rounded-lg">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                <input
                    type="text"
                    id="name"
                    wire:model.live.debounce.500ms="name"
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

    <!-- Stats and Controls -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-4">
        <div>
            <h3 class="products-count text-sm text-gray-600">
                {{ $products->total() }} products found
                @if($products->lastPage() > 1)
                    (page {{ $products->currentPage() }} of {{ $products->lastPage() }})
                @endif
            </h3>
        </div>

        <div class="flex flex-wrap gap-3 items-center">
            <!-- Per Page Control -->
            <div class="flex items-center">
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

            <!-- View Toggle -->
            <div class="flex items-center space-x-1">
                <span class="text-sm text-gray-700 mr-2">View:</span>
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
                <button
                    wire:click="setView('box')"
                    class="px-3 py-1 text-sm rounded-md {{ $view === 'box' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700' }}"
                >
                    Box
                </button>
            </div>
        </div>
    </div>

    <!-- Products -->
    @if($view === 'box')
        <!-- Box View -->
        <div class="box-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem;">
            @forelse($products as $product)
                <div class="product-card bg-white overflow-hidden shadow rounded-lg">
                    <div class="product-card-image">
                        <img
                            src="{{ $this->cardImage($product->edition?->slug) }}"
                            alt="{{ $product->edition?->card?->name ?? $product->name }}"
                            class="w-full h-48 object-cover cursor-pointer"
                            wire:click="$dispatch('show-card-image', {slug: '{{ $product->edition?->slug }}'})"
                        />
                    </div>

                    <div class="product-card-header p-4">
                        <h3 class="product-card-title text-lg font-medium text-gray-900">
                            <a href="{{ route('product.show', $product->id) }}" class="hover:text-indigo-600">
                                {{ $product->edition?->card?->name ?? $product->name }}
                            </a>
                        </h3>
                        <p class="product-card-subtitle text-sm text-gray-500">
                            {{ $product->edition?->set?->name }}
                        </p>
                    </div>

                    <div class="product-card-body p-4">
                        <div class="product-card-price text-xl font-bold text-gray-900">
                            €{{ number_format($product->price, 2) }}
                        </div>

                        <div class="product-card-details mt-2">
                            <div class="stock-info">
                                <span class="stock-badge inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $product->quantity > 0 
                                        ? ($product->quantity <= 5 
                                            ? 'bg-yellow-100 text-yellow-800' 
                                            : 'bg-green-100 text-green-800') 
                                        : 'bg-red-100 text-red-800' }}">
                                    {{ $product->quantity > 0 
                                        ? $product->quantity . ' in stock' 
                                        : 'Out of stock' }}
                                </span>
                            </div>

                            @if($product->is_foil)
                                <div class="foil-info mt-1">
                                    <span class="foil-badge inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        ✨ Foil
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="product-card-footer p-4">
                        @if($product->quantity > 0)
                            <div class="flex">
                                <input 
                                    type="number" 
                                    wire:model.live="cart_quantity.{{ $product->id }}" 
                                    min="1" 
                                    max="{{ $product->quantity }}" 
                                    value="1"
                                    class="w-16 border border-gray-300 rounded-l text-center"
                                >
                                <button
                                    wire:click="addToCart({{ $product->id }})"
                                    class="flex-1 inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-r text-white bg-indigo-600 hover:bg-indigo-700"
                                >
                                    Add to Cart
                                </button>
                            </div>
                        @else
                            <button 
                                disabled 
                                class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded text-gray-500 bg-gray-100"
                            >
                                Out of Stock
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <p class="col-span-full text-center text-gray-500 py-8">No products found.</p>
            @endforelse
        </div>
    @elseif($view === 'list')
        <!-- List View -->
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <ul class="divide-y divide-gray-200">
                @forelse($products as $product)
                    <li>
                        <div class="px-4 py-4 sm:px-6 flex items-center justify-between">
                            <div class="flex items-center">
                                <img
                                    src="{{ $this->cardImage($product->edition?->slug) }}"
                                    alt="{{ $product->edition?->card?->name ?? $product->name }}"
                                    class="w-16 h-16 object-cover rounded mr-4 cursor-pointer"
                                    wire:click="$dispatch('show-card-image', {slug: '{{ $product->edition?->slug }}'})"
                                />
                                <div>
                                    <h3 class="text-sm font-medium text-gray-900">
                                        <a href="{{ route('product.show', $product->id) }}" class="hover:text-indigo-600">
                                            {{ $product->edition?->card?->name ?? $product->name }}
                                        </a>
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
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center space-x-4">
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-900">
                                        €{{ number_format($product->price, 2) }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        Qty: 
                                        <span class="{{ $product->quantity > 0 
                                            ? ($product->quantity <= 5 
                                                ? 'text-yellow-600 font-bold' 
                                                : 'text-green-600') 
                                            : 'text-red-600 font-bold' }}">
                                            {{ $product->quantity }}
                                        </span>
                                    </p>
                                </div>

                                <div>
                                    @if($product->quantity > 0)
                                        <div class="flex items-center space-x-2">
                                            <input 
                                                type="number" 
                                                wire:model.live="cart_quantity.{{ $product->id }}" 
                                                min="1" 
                                                max="{{ $product->quantity }}" 
                                                value="1"
                                                class="w-16 border border-gray-300 rounded text-center"
                                            >
                                            <button
                                                wire:click="addToCart({{ $product->id }})"
                                                class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
                                            >
                                                Add
                                            </button>
                                        </div>
                                    @else
                                        <button 
                                            disabled 
                                            class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-gray-100"
                                        >
                                            Out
                                        </button>
                                    @endif
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
    @else
        <!-- Grid View (Table Style) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @forelse($products as $product)
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-4">
                        <div class="flex justify-center">
                            <img
                                src="{{ $this->cardImage($product->edition?->slug) }}"
                                alt="{{ $product->edition?->card?->name ?? $product->name }}"
                                class="w-32 h-32 object-cover rounded cursor-pointer"
                                wire:click="$dispatch('show-card-image', {slug: '{{ $product->edition?->slug }}'})"
                            />
                        </div>

                        <h3 class="mt-4 text-lg font-medium text-gray-900 text-center">
                            <a href="{{ route('product.show', $product->id) }}" class="hover:text-indigo-600">
                                {{ $product->edition?->card?->name ?? $product->name }}
                            </a>
                        </h3>

                        <p class="mt-1 text-sm text-gray-500 text-center">
                            {{ $product->edition?->set?->name }}
                        </p>

                        <p class="mt-2 text-sm text-center">
                            @if($product->is_foil)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Foil
                                </span>
                            @endif
                        </p>

                        <div class="mt-4 flex justify-between items-center">
                            <span class="text-lg font-medium text-gray-900">
                                €{{ number_format($product->price, 2) }}
                            </span>
                            <span class="text-sm {{ $product->quantity > 0 
                                ? ($product->quantity <= 5 
                                    ? 'text-yellow-600 font-bold' 
                                    : 'text-green-600') 
                                : 'text-red-600 font-bold' }}">
                                Qty: {{ $product->quantity }}
                            </span>
                        </div>

                        <div class="mt-4">
                            @if($product->quantity > 0)
                                <div class="flex">
                                    <input 
                                        type="number" 
                                        wire:model.live="cart_quantity.{{ $product->id }}" 
                                        min="1" 
                                        max="{{ $product->quantity }}" 
                                        value="1"
                                        class="w-16 border border-gray-300 rounded-l text-center"
                                    >
                                    <button
                                        wire:click="addToCart({{ $product->id }})"
                                        class="flex-1 flex justify-center py-2 px-4 border border-transparent rounded-r shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700"
                                    >
                                        Add to Cart
                                    </button>
                                </div>
                            @else
                                <button
                                    disabled
                                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-gray-500 bg-gray-100"
                                >
                                    Out of Stock
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <p class="col-span-full text-center text-gray-500 py-8">No products found.</p>
            @endforelse
        </div>
    @endif

    <!-- Pagination -->
    @if($products->hasPages())
        <div class="mt-6">
            {{ $products->links() }}
        </div>
    @endif

    <!-- Card Image Modal -->
    <div x-data="{ showImage: false, imageSrc: '', imageAlt: '' }" 
         x-show="showImage"
         x-on:show-card-image.window="showImage = true; imageSrc = $event.detail.slug; imageAlt = 'Card Image'"
         x-on:keydown.escape.window="showImage = false"
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-on:click="showImage = false" class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <div class="mt-2">
                                <img :src="imageSrc" :alt="imageAlt" class="max-w-full max-h-[70vh] mx-auto">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button x-on:click="showImage = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Add search functionality with debouncing
    document.addEventListener('livewire:init', () => {
        Livewire.on('product-added-to-cart', (event) => {
            // Show notification when product is added to cart
            console.log('Product added to cart:', event.message);
        });
    });
</script>
@endpush>