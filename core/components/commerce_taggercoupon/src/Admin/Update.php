<?php

declare(strict_types=1);

namespace PoconoSewVac\TaggerCoupon\Admin;

use modmore\Commerce\Admin\Sections\SimpleSection;
use modmore\Commerce\Admin\Page;

class Update extends Page
{
    public $key = 'discounts/tagger_coupons/update';
    public $title = 'commerce_taggercoupon.update_coupon';
    public static $permissions = ['commerce', 'commerce_discounts'];

    public function setUp()
    {
        $objectId = (int) $this->getOption('id', 0);
        $exists = $this->adapter->getCount('TaggerCoupon', ['id' => $objectId, 'removed' => false]);

        if ($exists) {
            $section = new SimpleSection($this->commerce, [
                'title' => $this->title
            ]);

            $section->addWidget((new Form($this->commerce, ['isUpdate' => true, 'id' => $objectId]))->setUp());
            $this->addSection($section);

            return $this;
        }

        return $this->returnError($this->adapter->lexicon('commerce.item_not_found'));
    }
}