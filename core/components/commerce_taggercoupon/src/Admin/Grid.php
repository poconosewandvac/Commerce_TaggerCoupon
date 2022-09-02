<?php

declare(strict_types=1);

namespace PoconoSewVac\TaggerCoupon\Admin;

use modmore\Commerce\Admin\Util\AvailabilityRenderer;

class Grid extends \modmore\Commerce\Admin\Modules\Discounts\Grid
{
    protected $classKey = 'TaggerDiscount';
    protected $classKeyAction = 'tagger_coupons';

    /**
     * @param $item
     * @return mixed
     */
    protected function getOrdersBetween($item)
    {
        return (new AvailabilityRenderer($this->commerce))
            ->renderOrderOptions(
                $item['minimum_order_total'], $item['maximum_order_total'],
                $item['minimum_order_items'], $item['maximum_order_items']
            );
    }
}