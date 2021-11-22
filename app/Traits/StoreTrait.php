<?php

namespace App\Traits;

use App\Models\Product;
use App\Models\ScriptTag;
use App\Models\Store;

trait StoreTrait
{
    public function createScriptTag($data)
    {
        return $this->api()->rest('POST', '/admin/api/script_tags.json', $data);
    }

    public function deleteScriptTag($script_tag_id)
    {
        return $this->api()->rest('DELETE', "/admin/api/script_tags/{$script_tag_id}.json");
    }

    public function syncProducts($params)
    {
        $response = $this->api()->rest('GET', '/admin/api/products.json', $params);
        $products = $response["body"]["products"];
        return $products;
    }

    public function products()
    {
        return $this->hasMany(Product::class, "shopify_url", "name");
    }
}
