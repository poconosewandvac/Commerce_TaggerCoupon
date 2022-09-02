<?php

declare(strict_types=1);

namespace PoconoSewVac\TaggerCoupon\Admin;

use modmore\Commerce\Admin\Util\AvailabilityRenderer;

class Grid extends \modmore\Commerce\Admin\Modules\Coupons\Grid
{
    protected $classKey = 'TaggerCoupon';
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

    /**
     * Override getItems to workaround Commerce core just looking for comCoupon in the getItems query in \modmore\Commerce\Admin\Modules\Coupons\Grid::getItems()
     * @param array $options
     * @return array
     */
    public function getItems(array $options = array())
    {
        $items = [];

        $c = $this->adapter->newQuery($this->classKey);
        $c->where([
            'removed' => false,
            'class_key' => $this->classKey,
        ]);

        $sortby = array_key_exists('sortby', $options) && !empty($options['sortby']) ? $this->adapter->escape($options['sortby']) : $this->defaultSort;
        $sortdir = array_key_exists('sortdir', $options) && strtoupper($options['sortdir']) === 'ASC' ? 'ASC' : 'DESC';
        $c->sortby($sortby, $sortdir);

        $count = $this->adapter->getCount($this->classKey, $c);
        $this->setTotalCount($count);

        $c->limit($options['limit'], $options['start']);
        /** @var \comDiscount[] $collection */
        $collection = $this->adapter->getCollection($this->classKey, $c);

        foreach ($collection as $object) {
            $items[] = $this->prepareItem($object);
        }

        return $items;
    }

}