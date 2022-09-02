<?php

declare(strict_types=1);

namespace PoconoSewVac\TaggerCoupon\Modules;

use modmore\Commerce\Admin\Configuration\About\ComposerPackages;
use modmore\Commerce\Admin\Sections\SimpleSection;
use modmore\Commerce\Events\Admin\GeneratorEvent;
use modmore\Commerce\Events\Admin\PageEvent;
use modmore\Commerce\Events\Admin\TopNavMenu;
use modmore\Commerce\Modules\BaseModule;
use Symfony\Component\EventDispatcher\EventDispatcher;

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

class TaggerCoupon extends BaseModule {

    public function getName()
    {
        $this->adapter->loadLexicon('commerce_taggercoupon:default');
        return $this->adapter->lexicon('commerce_taggercoupon');
    }

    public function getAuthor()
    {
        return 'Tony Klapatch - Pocono Sew & Vac';
    }

    public function getDescription()
    {
        return $this->adapter->lexicon('commerce_taggercoupon.description');
    }

    public function initialize(EventDispatcher $dispatcher)
    {
        // Load our lexicon
        $this->adapter->loadLexicon('commerce_taggercoupon:default');

        // Workaround for Exception: Class "comCoupon" not found in admin?
        $this->adapter->loadClass('comCoupon');

        // Add the xPDO package, so Commerce can detect the derivative classes
        $root = dirname(__DIR__, 2);
        $path = $root . '/model/';
        $this->adapter->loadPackage('commerce_taggercoupon', $path);

        // Add template path to twig
//        $root = dirname(__DIR__, 2);
//        $this->commerce->view()->addTemplatesPath($root . '/templates/');

        // Add composer libraries to the about section (v0.12+)
        $dispatcher->addListener(\Commerce::EVENT_DASHBOARD_LOAD_ABOUT, [$this, 'addLibrariesToAbout']);

        if ($this->adapter->hasPermission('commerce_discounts')) {
            $dispatcher->addListener(\Commerce::EVENT_DASHBOARD_INIT_GENERATOR, [$this, 'initGenerator']);
            $dispatcher->addListener(\Commerce::EVENT_DASHBOARD_GET_MENU, [$this, 'loadMenuItem']);
        }
    }

    public function initGenerator(GeneratorEvent $event)
    {
        $generator = $event->getGenerator();
        $generator->addPage('discounts/tagger_coupons', '\PoconoSewVac\TaggerCoupon\Admin\Active');
        $generator->addPage('discounts/tagger_coupons/create', '\PoconoSewVac\TaggerCoupon\Admin\Create');
        $generator->addPage('discounts/tagger_coupons/update', '\PoconoSewVac\TaggerCoupon\Admin\Update');
        $generator->addPage('discounts/tagger_coupons/delete', '\PoconoSewVac\TaggerCoupon\Admin\Delete');
    }

    public function loadMenuItem(TopNavMenu $event)
    {
        $items = $event->getItems();

        $items['discounts']['submenu'][] = [
            'name' => $this->adapter->lexicon('commerce_taggercoupon.tagger_coupons'),
            'key' => 'discounts/tagger_coupons',
            'link' => $this->adapter->makeAdminUrl('discounts/tagger_coupons'),
            'icon' => 'icon-tags',
        ];

        $event->setItems($items);
    }

    public function getModuleConfiguration(\comModule $module)
    {
        $fields = [];

        // A more detailed description to be shown in the module configuration. Note that the module description
        // ({@see self:getDescription}) is automatically shown as well.
//        $fields[] = new DescriptionField($this->commerce, [
//            'description' => $this->adapter->lexicon('commerce_taggercoupon.module_description'),
//        ]);

        return $fields;
    }

    public function addLibrariesToAbout(PageEvent $event)
    {
        $lockFile = dirname(__DIR__, 2) . '/composer.lock';
        if (file_exists($lockFile)) {
            $section = new SimpleSection($this->commerce);
            $section->addWidget(new ComposerPackages($this->commerce, [
                'lockFile' => $lockFile,
                'heading' => $this->adapter->lexicon('commerce.about.open_source_libraries') . ' - ' . $this->adapter->lexicon('commerce_taggercoupon'),
                'introduction' => '', // Could add information about how libraries are used, if you'd like
            ]));

            $about = $event->getPage();
            $about->addSection($section);
        }
    }
}
