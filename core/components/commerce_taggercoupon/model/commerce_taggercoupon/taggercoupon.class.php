<?php
/**
 * Tagger Coupon for Commerce.
 *
 * Copyright 2022 by Tony Klapatch <tony@klapatch.net>
 *
 * This file is meant to be used with Commerce by modmore. A valid Commerce license is required.
 *
 * @package commerce_taggercoupon
 * @license See core/components/commerce_taggercoupon/docs/license.txt
 */
class TaggerCoupon extends comCoupon
{
    public function getProductRestriction()
    {
        // The products here are repurposed tagger tag IDs
        $tags = $this->get('products');
        $tags = explode(',', $tags);
        $tags = array_map('intval', $tags);

        // Exclude products from output
        $excludedProducts = $this->getProperty('excluded_products') ?? [];
        $excludedProducts = array_map('intval', $excludedProducts);

        if (empty($tags)) {
            return [];
        }

        $c = $this->adapter->newQuery('TaggerTagResource');
        $c->leftJoin('TaggerTag', 'TaggerTag', 'TaggerTagResource.tag = TaggerTag.id');
        $c->leftJoin('comProduct', 'comProduct', 'comProduct.target = TaggerTagResource.resource');
        $c->where(['TaggerTag.id:IN' => $tags]);
        $c->select('comProduct.id');

        $resources = $this->adapter->getIterator('TaggerTagResource', $c);

        $products = [];
        foreach ($resources as $resource) {
            $products[] = $resource->get('id');
        }

        $res = array_diff($products, $excludedProducts);

        return $res;
    }
}
