<?php

declare(strict_types=1);

namespace PoconoSewVac\TaggerCoupon\Admin;

use modmore\Commerce\Admin\Page;

class Delete extends Page
{
    public $key = 'discounts/tagger_coupons/delete';
    public $title = 'commerce_taggercoupon.delete_coupon';
    public static $permissions = ['commerce', 'commerce_discounts'];

    public function setUp()
    {
        $objectId = (int) $this->getOption('id', 0);
        $object = $this->adapter->getObject('TaggerCoupon', ['id' => $objectId, 'removed' => false]);

        $section = new SimpleSection($this->commerce, [
            'title' => $this->title
        ]);
        if ($object) {
            $widget = new DeleteFormWidget($this->commerce, [
                'title' => 'commerce.delete_discount'
            ]);

            $widget->setRecord($object);
            $widget->setClassKey('TaggerCoupon');
            $widget->setFormAction($this->adapter->makeAdminUrl('discounts/tagger_coupons/delete', ['id' => $object->get('id')]));
            $widget->setUp();
            $section->addWidget($widget);
            $this->addSection($section);

            return $this;
        }

        return $this->returnError($this->adapter->lexicon('commerce.item_not_found'));
    }
}