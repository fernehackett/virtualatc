<?php

Route::group(['middleware' => ["verify.shopify", "billable"]], function () {
    Route::get('/', "Shopify\DashboardController@index")->name("home");
    Route::group(["as" => "shopify.", "namespace" => "Shopify"], function () {
        Route::post("shop/setup", "DashboardController@setup")->name("shop.setup");
        Route::resource("products", "ProductController");
        Route::post("product/bulk", "ProductController@bulk")->name("product.bulk");
    });
});
Route::get('policy', 'HomeController@policy')->name("policy");
Route::get('script-tags.js', 'HomeController@getScriptTags')->name("public.script-tags");
