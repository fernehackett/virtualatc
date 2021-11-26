<?php

namespace App\Http\Controllers\Shopify;

use App\Http\Controllers\Controller;
use App\Jobs\SyncProducts;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::where("shopify_url", auth()->user()->name);
        $search = $request->get("search", false);
        if ($search) {
            $products = $products->where("title", "like", "%{$search}%");
        }
        $enable = $request->get("enable", false);
        if ($enable !== false) {
            $products = $products->where("enable", "{$enable}");
        }
        $products = $products->paginate(20);
        if(count($products) == 0){
            $this->sync();
        }
        return view("shopify.products.index", compact("products"));
    }

    public function update(Product $product, Request $request)
    {
        $product->update($request->all());

        if ($product->enable == 1 && !isset($product->metafield_id)) {
            auth()->user()->createMetafield($product);
        }

        if ($product->enable == 0 && isset($product->metafield_id)) {
            auth()->user()->deleteMetafield($product->metafield_id);
            $product->update(["metafield_id" => null]);
        }
        return response()->json(["succeed" => true, "msg" => "Saved!"]);
    }

    public function bulk(Request $request)
    {
        $product_ids = $request->get("product_ids", []);
        $products = Product::whereIn("id", $product_ids)->update($request->get("update"));
        return response()->json(["succeed" => true, "msg" => "Saved!"]);
    }

    public function sync()
    {
        SyncProducts::dispatch(auth()->user()->getDomain()->toNative());
        return response()->json(["succeed" => true, "msg" => "Syncing!"]);
    }
}
