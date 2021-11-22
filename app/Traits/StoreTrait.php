<?php

namespace App\Traits;

use App\Models\MetaField;
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

    public function getMetafields($product_id)
    {
        $params = [
            "metafield" => [
                "owner_id"       => $product_id,
                "owner_resource" => "product",
                "namespace"      => "virtualatc",
                "key"            => "enable",
            ]
        ];
        $response = $this->api()->rest('GET', '/admin/api/metafields.json', $params);
        $metafields = $response["body"]["metafields"];
        return $metafields;
    }

    public function createMetafield(Product $product, $data = [])
    {
        $data = array_merge([
            "metafield" => [
                "namespace" => "virtualatc",
                "key"       => "enable",
                "value"     => 1,
                "type"      => "boolean"
            ]
        ], $data);
        $response = $this->api()->rest('POST', "/admin/api/products/{$product->_id}/metafields.json", $data);
        \Log::info($response);
        $metafield = $response["body"]["metafield"];
        $product->update([
            "enable"       => 1,
            "metafield_id" => $metafield["id"]
        ]);
        return $metafield;
    }

    public function getMetafield($metaField)
    {
        $response = $this->api()->rest('GET', "/admin/api/metafields/{$metaField}.json");
        $metafield = $response["body"]["metafield"];
        return $metafield;
    }

    public function deleteMetafield($metaField)
    {
        return $this->api()->rest('DELETE', "/admin/api/metafields/{$metaField}.json");
    }

    public function products()
    {
        return $this->hasMany(Product::class, "shopify_url", "name");
    }
}
