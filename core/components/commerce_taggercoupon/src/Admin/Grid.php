<?php

declare(strict_types=1);

namespace PoconoSewVac\TaggerCoupon\Admin;

use modmore\Commerce\Admin\Util\AvailabilityRenderer;
use modmore\Commerce\Admin\Util\Action;

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

    /**
     * Override coupons top toolbar
     * @param array $options
     * @return array
     */
    public function getTopToolbar(array $options = [])
    {
        $toolbar = [];

        $toolbar[] = [
            'name' => 'add-coupon',
            'title' => $this->adapter->lexicon('commerce_taggercoupon.add_coupon'),
            'type' => 'button',
            'link' => $this->adapter->makeAdminUrl('discounts/tagger_coupons/create'),
            'button_class' => 'commerce-ajax-modal',
            'icon_class' => 'icon-plus',
            'modal_title' => $this->adapter->lexicon('commerce_taggercoupon.add_coupon'),
            'position' => 'top',
        ];

        $toolbar[] = [
            'name' => 'limit',
            'title' => $this->adapter->lexicon('commerce.limit'),
            'type' => 'textfield',
            'value' => (int)$options['limit'],
            'position' => 'bottom',
            'width' => 'four wide',
        ];

        return $toolbar;
    }

    public function prepareItem(\comCoupon $coupon)
    {
        $item = $coupon->toArray();
        $discount = [];
        if ($item['discount'] !== 0) {
            $discount[] = $item['discount_formatted'];
        }
        if ($item['discount_percentage'] !== 0.0) {
            $discount[] = $item['discount_percentage_formatted'];
        }
        $item['discount'] = implode(' + ', $discount);

        $used = $this->adapter->getCount('comCouponUsage', ['coupon' => $item['id']]);
        $usedString = $used . '/' . $item['max_uses'];
        switch (true) {
            case $item['max_uses'] === 0:
                $usedString = $used . '/&infin;';
                $usedString = '<span style="color: green;">' . $usedString . '</span>';
                break;
            case $used === $item['max_uses']:
                $usedString = '<span style="color:red;">' . $usedString . '</span>';
                break;
            case $used >= ($item['max_uses'] / 2):
                $usedString = '<span style="color:orange;">' . $usedString . '</span>';
                break;
            default:
                $usedString = '<span style="color: green;">' . $usedString . '</span>';
                break;
        }
        $item['usage'] = $usedString;

        $item['active'] = $item['active'] ? $this->adapter->lexicon('commerce.active') : $this->adapter->lexicon('commerce.inactive');
        $item['available_from'] = $item['available_from_formatted'];
        $item['available_until'] = $item['available_until_formatted'];

        $item['orders_between'] = (new AvailabilityRenderer($this->commerce))
            ->renderOrderOptions(
                $item['minimum_order_total'], $item['maximum_order_total'],
                $item['minimum_order_items'], $item['maximum_order_items']
            );

        if (!empty($item['products'])) {
            $item['orders_between'] .= ', ' . $this->adapter->lexicon('commerce.on_specific_products');
        }

        $item['time_between'] = ($item['available_from'] > 0) ? 'From ' . $item['available_from_formatted'] : '';
        if ($item['available_until']) {
            $item['time_between'] .= ((empty($item['time_between'])) ? 'Until ' : ' until ') . $item['available_until_formatted'];
        }

        $updateUrl = $this->adapter->makeAdminUrl('discounts/tagger_coupons/update', ['id' => $item['id']]);
        $item['code'] = '<a href="' . $updateUrl . '" class="commerce-ajax-modal" data-modal-title="' . $this->adapter->lexicon('commerce.update_coupon') . ': ' . $this->encode($item['code']) . '">' . $this->encode($item['code']) . '</a>';

        // Define the actions for the item
        $item['actions'] = [];
        $item['actions'][] = (new Action())
            ->setUrl($this->adapter->makeAdminUrl('discounts/tagger_coupons/update', ['id' => $item['id']]))
            ->setTitle($this->adapter->lexicon('commerce.update_coupon'))
            ->setModalTitle($this->adapter->lexicon('commerce.update_coupon') . ': ' . $this->encode($coupon->get('code')))
            ->setIcon('icon-edit');

        $item['actions'][] = (new Action())
            ->setUrl($this->adapter->makeAdminUrl('discounts/tagger_coupons/usage', ['coupon' => $item['id']]))
            ->setTitle($this->adapter->lexicon('commerce.coupon_usage'))
            ->setIcon('icon-tags');

        $item['actions'][] = (new Action())
            ->setUrl($this->adapter->makeAdminUrl('discounts/tagger_coupons/delete', ['id' => $item['id']]))
            ->setTitle($this->adapter->lexicon('commerce.delete_coupon'))
            ->setIcon('icon-trash');

        return $item;
    }
}