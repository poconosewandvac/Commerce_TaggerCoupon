<?php

declare(strict_types=1);

namespace PoconoSewVac\TaggerCoupon\Admin;

use modmore\Commerce\Admin\Page;
use modmore\Commerce\Admin\Sections\SimpleSection;

class Create extends Page
{
    public $key = 'discounts/tagger_coupons/create';
    public $title = 'commerce_taggercoupon.add_coupon';
    public static $permissions = ['commerce', 'commerce_discounts'];

    public function setUp()
    {
        $section = new SimpleSection($this->commerce, [
            'title' => $this->title,
        ]);

        $section->addWidget((new Form($this->commerce, ['id' => 0]))->setUp());
        $this->addSection($section);

        return $this;
    }
}