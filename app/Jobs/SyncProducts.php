<?php

namespace App\Jobs;

use App\Models\Product;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Osiset\ShopifyApp\Objects\Values\ShopDomain;
use Osiset\ShopifyApp\Storage\Queries\Shop;

class SyncProducts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $shopDomain, $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($shopDomain, $data = null)
    {
        $this->shopDomain = $shopDomain;
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Shop $shopQuery)
    {
        $shopDomain = $this->shopDomain;
        $this->shopDomain = ShopDomain::fromNative($this->shopDomain);
        $shop = $shopQuery->getByDomain($this->shopDomain);
        $data = $this->data;
        if (!$data) {
            $data = [
                "limit" => 250
            ];
        }
        $products = $shop->syncProducts($data);
        if (count($products) == 0) return;
        $since_id = false;
        foreach ($products as $product) {
            $filter = [
                'title'       => $product['title'],
                'image'       => $product['image']["src"] ?? "",
                '_id'         => $product['id'],
                'shopify_url' => $shop->getDomain()->toNative()
            ];
            $found = Product::where("_id", $product['id'])->where("shopify_url", $shop->getDomain()->toNative())->first();
            if (!$found) {
                Product::create($filter);
            }
            $since_id = $product['id'];
        }
        $nextParams = array_merge($data, [
            "since_id" => $since_id
        ]);
        self::dispatch($shopDomain, $nextParams);
    }
}
