<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\Set;
use Livewire\Attributes\Computed;

class ProductListing extends Component
{
    public $name = '';
    public $min_price = null;
    public $max_price = null;
    public $set_id = null;
    public $is_foil = null;
    public $in_stock_only = true;
    public $page = 1;
    public $per_page = 10;
    public $view = 'grid'; // grid, list, box

    #[Computed]
    public function products()
    {
        $query = Product::with(['edition.card', 'edition.set']);

        if ($this->name) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->name . '%')
                  ->orWhereHas('edition.card', function($q2) {
                      $q2->where('name', 'like', '%' . $this->name . '%');
                  });
            });
        }

        if ($this->min_price) {
            $query->where('price', '>=', $this->min_price);
        }

        if ($this->max_price) {
            $query->where('price', '<=', $this->max_price);
        }

        if ($this->in_stock_only) {
            $query->where('quantity', '>', 0);
        }

        if ($this->set_id) {
            $query->whereHas('edition', function($q) {
                $q->where('set_id', $this->set_id);
            });
        }

        if ($this->is_foil !== null && $this->is_foil !== '') {
            $query->where('is_foil', $this->is_foil ? true : false);
        }

        return $query->orderBy('id', 'desc')
            ->paginate($this->per_page, ['*'], 'page', $this->page);
    }

    #[Computed]
    public function sets()
    {
        return Set::orderBy('name')->get();
    }

    public function mount()
    {
        $this->per_page = session('discover_per_page', 10);
        $this->view = session('view_preference', 'grid');
    }

    public function updatedPerPage()
    {
        session(['discover_per_page' => $this->per_page]);
    }

    public function setView($view)
    {
        $this->view = $view;
        session(['view_preference' => $this->view]);
    }

    public function updatedView()
    {
        session(['view_preference' => $this->view]);
    }

    public function render()
    {
        return view('livewire.product-listing', [
            'products' => $this->products,
            'sets' => $this->sets,
        ]);
    }
}
