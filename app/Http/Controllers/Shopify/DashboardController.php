<?php

namespace App\Http\Controllers\Shopify;

use App\Http\Controllers\Controller;
use App\Models\Background;
use App\Models\ScriptTag;
use App\Models\Store;
use Illuminate\Http\Request;
use PHPShopify\ShopifySDK;

class DashboardController extends Controller
{

    public function __construct()
    {

    }

    public function index(Request $request)
    {
        return view("shopify.dashboard.index");
    }

    public function setup(Request $request)
    {
        $data = array_merge([
            "enable" => 0
        ], $request->all());
        auth()->user()->update($data);
        return response()->json(["succeed" => true, "msg" => "Saved!", "user" => auth()->user()]);
    }
}
