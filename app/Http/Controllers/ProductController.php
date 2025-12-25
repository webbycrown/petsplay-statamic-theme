<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Statamic\Facades\Entry;
use Carbon\Carbon;
use Statamic\Facades\Form;
use Illuminate\Support\Facades\Validator;
use Statamic\Facades\Antlers;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductController extends Controller
{
    public function productFilter(Request $request)
    {   
        // Read filter inputs
        $categorySlug = $request->get('category'); 
        $colors = $request->get('colors');
        $search = $request->get('search');

        // Convert values into arrays (to support multiple filters)
        $colorArray = is_array($colors) ? $colors : ($colors ? explode(',', $colors) : []);
        $categoryArray = is_array($categorySlug) ? $categorySlug : ($categorySlug ? explode(',', $categorySlug) : []);

        // Base product query
        $query = Entry::query()->where('collection', 'products');
        
        // Category filter
        if (!empty($categoryArray)) {
            foreach ($categoryArray as $category) {
                $query->where('categories', 'like', "%$category%");
            }
        }

        // Search filter (title + slug)
        if( $search ){
            $query->where(fn($q) => $q->where('title', 'like', "%$search%")
            ->orWhere('slug', 'like', "%$search%"));
        }

        // Color filter
        if (!empty($colorArray)) {
            foreach ($colorArray as $color) {
                $query->where('colors', 'like', "%$color%");
            }
        }

        // Pagination setup
        $currentPage = request()->get('page', 1);
        $perPage = 6;

        // Get all matching products
        $allItemsCollection = $query->get()->collect();     

        // Manually slice items for pagination
        $products = $allItemsCollection
        ->slice(($currentPage - 1) * $perPage, $perPage)
        ->values();

        // Create paginator object
        $paginator = new LengthAwarePaginator(
            $products,
            $allItemsCollection->count(),
            $perPage,
            $currentPage,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );

        // Product count for display
        $currentCount = $products->count();
        $totalCount   = $allItemsCollection->count();

        // Prepare pagination links manually
        $baseUrl = $request->url();
        $queryParams = $request->except('page');
        $links = [];
        $message = '';
        if ( count($products) > 0 ){

            // Add numbered page links
            for ($i = 1; $i <= $paginator->lastPage(); $i++) {
                $links[] = [
                    'page' => $i,
                    'url'  => $baseUrl . '?' . http_build_query(array_merge($queryParams, ['page' => $i])),
                ];
            }

        }else{
            // No result message
            $message = 'Search Result: No Products Found';
        }

        // Load Antlers template file
        $antlersView = resource_path('views/partials/product-list.antlers.html');

        $template = file_get_contents($antlersView);


        // Render & return HTML to AJAX
        return Antlers::parse($template, [
            'message'        => $message,
            'products'       => $products,
            'products_count' => $currentCount,
            'total_count'    => $totalCount,

            // Pagination data used in Antlers
            'paginate' => [
                'current_page'   => $paginator->currentPage(),
                'last_page'      => $paginator->lastPage(),
                'prev_page'      => $paginator->currentPage() > 1 ? $paginator->currentPage() - 1 : null,
                'prev_page_url'  => $paginator->currentPage() > 1 ? $baseUrl . '?page=' . ($paginator->currentPage() - 1) : null,
                'next_page'      => $paginator->currentPage() < $paginator->lastPage() ? $paginator->currentPage() + 1 : null,
                'next_page_url'  => $paginator->currentPage() < $paginator->lastPage() ? $baseUrl . '?page=' . ($paginator->currentPage() + 1) : null,
                'links'          => $links,
                'total_items'    => $allItemsCollection->count(),
                'per_page'       => $perPage,
            ],
        ]);
    }

     public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'product_id' => 'required',
                'title'      => 'required|string|max:255',
                'email'      => 'required|email',
                'rating'     => 'required|integer|min:1|max:5',
                'comment'    => 'required|string',
            ],
            [
                'product_id.required' => 'Product is missing. Please refresh the page.',

                'title.required'      => 'Please enter your name.',
                'title.max'           => 'Name must not exceed 255 characters.',

                'email.required'      => 'Please enter your email address.',
                'email.email'         => 'Please enter a valid email address.',

                'rating.required'     => 'Please select a rating.',
                'rating.integer'      => 'Rating must be a number.',
                'rating.min'          => 'Rating must be at least 1 star.',
                'rating.max'          => 'Rating cannot be more than 5 stars.',

                'comment.required'    => 'Please write your comment.',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        Entry::make()
            ->collection('product_comments')
            ->data([
                'product_id' => $request->product_id,
                'parent_id'  => $request->parent_id,
                'title'      => $request->title,
                'email'      => $request->email,
                'rating'     => $request->rating,
                'comment'    => $request->comment,
                'date_field' => Carbon::today()->toDateString(),
                'status'     => false, // admin approval
            ])
            ->save();

        return response()->json([
            'message' => 'Comment submitted successfully. Awaiting approval.'
        ]);
    }
}
