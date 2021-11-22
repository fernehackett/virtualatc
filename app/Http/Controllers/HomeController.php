<?php

namespace App\Http\Controllers;

use App\Models\Template;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
//        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function policy()
    {
        return view("policy");
    }

    public function getScriptTags(Request $request)
    {
        $shop_url = $request->get("shop");
        $user = User::where("name", $shop_url)->first();
        if ($user) {
            return response()->view("shopify.script-tags.index", compact("user"))
                ->header("Content-Type", "application/javascript")->header("Cache-Control", "no-store, no-cache, must-revalidate");
        } else
            return response("")->header("Content-Type", "application/javascript");
    }
}
