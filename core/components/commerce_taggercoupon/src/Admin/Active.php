<?php

declare(strict_types=1);

namespace PoconoSewVac\TaggerCoupon\Admin;

use modmore\Commerce\Admin\Page;
use modmore\Commerce\Admin\Sections\SimpleSection;

class Active extends Page
{
    protected $classKey = 'TaggerCoupon';
    public $key = 'discounts/tagger_coupons';
    public $title = 'commerce_taggercoupon.tagger_coupons';
    public static $permissions = ['commerce', 'commerce_discounts'];

    public function setUp()
    {
        $section = new SimpleSection($this->commerce, [
            'title' => $this->title,
        ]);

        $section->addWidget(new Grid($this->commerce, ['active' => true]));;
        $this->addSection($section);

        return $this;
    }
}