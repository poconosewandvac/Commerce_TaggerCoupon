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

        if (empty($tags)) {
            return [];
        }

        $products = [];

        $products = explode(',', $products);
        $products = array_map('intval', $products);
        return $products;
    }
}
