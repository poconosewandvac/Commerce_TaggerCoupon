<?php

declare(strict_types=1);

namespace PoconoSewVac\TaggerCoupon\Admin;

use modmore\Commerce\Admin\Widgets\Form\CheckboxField;
use modmore\Commerce\Admin\Widgets\Form\DateTimeField;
use modmore\Commerce\Admin\Widgets\Form\HiddenField;
use modmore\Commerce\Admin\Widgets\Form\NumberField;
use modmore\Commerce\Admin\Widgets\Form\SectionField;
use modmore\Commerce\Admin\Widgets\Form\SelectMultipleField;
use modmore\Commerce\Admin\Widgets\Form\SelectField;
use modmore\Commerce\Admin\Widgets\Form\TextField;
use modmore\Commerce\Admin\Widgets\Form\Validation\Required;
use modmore\Commerce\Admin\Widgets\Form\Validation\Length;

class Form extends \modmore\Commerce\Admin\Modules\Coupons\Form
{
    protected $targetClassKey = 'comProduct';
    protected $targetClassField = 'name';
    protected $classKeyAction = 'tagger_coupons';
    protected $classKey = 'TaggerCoupon';

    public function getFields(array $options = array())
    {
        $fields = [];

        $fields[] = new TextField($this->commerce, [
            'name' => 'code',
            'label' => $this->adapter->lexicon('commerce.code'),
            'description' => $this->adapter->lexicon('commerce.code.desc'),
            'validation' => [
                new Required(),
                new Length(3, 190),
            ]
        ]);


        $fields[] = new SectionField($this->commerce, [
            'label' => 'Discount'
        ]);

        $fields[] = new NumberField($this->commerce, [
            'name' => 'discount',
            'label' => $this->adapter->lexicon('commerce.discount'),
            'input_class' => 'commerce-field-currency',
        ]);

        $fields[] = new NumberField($this->commerce, [
            'name' => 'discount_percentage',
            'label' => $this->adapter->lexicon('commerce.discount_percentage'),
            'description' => $this->adapter->lexicon('commerce.discount_percentage.desc'),
        ]);

        $fields[] = new SectionField($this->commerce, [
            'label' => $this->adapter->lexicon('commerce.availability'),
        ]);

        $fields[] = new CheckboxField($this->commerce, [
            'name' => 'active',
            'label' => $this->adapter->lexicon('commerce.active'),
            'default' => true,
        ]);

        $fields[] = new NumberField($this->commerce, [
            'name' => 'max_uses',
            'label' => $this->adapter->lexicon('commerce.max_uses'),
        ]);
        $fields[] = new SelectMultipleField($this->commerce, [
            'name' => 'products',
            'label' => $this->adapter->lexicon('commerce.discount_target.TaggerCoupon'),
            'options' => $this->getTaggerTags(),
            'validation' => [
                new Required(),
            ],
        ]);

        $fields[] = new SelectMultipleField($this->commerce, [
            'name' => 'properties[excluded_products]',
            'label' => $this->adapter->lexicon('commerce_taggercoupon.excluded_products'),
            'value' => $this->record->getProperty('excluded_products'),
            'optionsClass' => 'comProduct',
            'optionsCondition' => ['removed' => false],
        ]);

        $fields[] = new DateTimeField($this->commerce, [
            'name' => 'available_from',
            'label' => $this->adapter->lexicon('commerce.available_from'),
        ]);

        $fields[] = new DateTimeField($this->commerce, [
            'name' => 'available_until',
            'label' => $this->adapter->lexicon('commerce.available_until'),
        ]);

        $fields[] = new NumberField($this->commerce, [
            'name' => 'minimum_order_total',
            'label' => $this->adapter->lexicon('commerce.minimum_order_total'),
            'input_class' => 'commerce-field-currency',
        ]);

        $fields[] = new NumberField($this->commerce, [
            'name' => 'maximum_order_total',
            'label' => $this->adapter->lexicon('commerce.maximum_order_total'),
            'input_class' => 'commerce-field-currency',
        ]);

        $fields[] = new NumberField($this->commerce, [
            'name' => 'minimum_order_items',
            'label' => $this->adapter->lexicon('commerce.minimum_order_items'),
        ]);

        $fields[] = new NumberField($this->commerce, [
            'name' => 'maximum_order_items',
            'label' => $this->adapter->lexicon('commerce.maximum_order_items'),
        ]);

        return $fields;
    }

    public function getFormAction(array $options = array())
    {
        if ($this->record->get('id')) {
            return $this->adapter->makeAdminUrl('discounts/tagger_coupons/update', ['id' => $this->record->get('id')]);
        }
        return $this->adapter->makeAdminUrl('discounts/tagger_coupons/create');
    }

    private function getTaggerTags(): array
    {
        $tags = $this->adapter->getCollection('TaggerTag');

        $result = [];

        foreach ($tags as $tag) {
            $result[] = [
                'label' => $tag->get('label'),
                'value' => $tag->get('id'),
            ];
        }

        return $result;
    }
}