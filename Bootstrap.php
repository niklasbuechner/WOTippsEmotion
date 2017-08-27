<?php

define('WOTIPPS_LICENSE_CHECK', false);

use Doctrine\Common\Collections\ArrayCollection;
use Shopware\Components\Theme\LessDefinition;

class Shopware_Plugins_Frontend_WOTippsEmotion_Bootstrap extends Shopware_Components_Plugin_Bootstrap
{

    public function getVersion()
    {
        return "1.0.2";
    }

    public function getLabel()
    {
        return 'WOTipps Emotion Integrator';
    }
    
    public function getInfo()
    {
        return array(
            'version'     => $this->getVersion(),
            'autor'       => 'Niklas B&uuml;chner | Webseiten-Optimierungs-Tipps.de',
            'copyright'   => '© 2017 ',
            'label'       => $this->getLabel(),
            'license'     => 'n.buechner@wotipps.de',
            'support'     => 'n.buechner@wotipps.de',
            'link'        => 'http://www.webseiten-optimierungs-tipps.de'
        );
    }

    public function install()
    {
        if (!$this->checkLicenseWrapper())
        {
            return array(
                'success' => false, 'message' => 'Keine Lizenz vorhanden.');
        }
        
        $this->subscribeEvent(
            'Theme_Compiler_Collect_Plugin_Javascript',
            'addJavascriptFiles'
        );
        
        $this->subscribeEvent(
            'Theme_Compiler_Collect_Plugin_Less',
            'addCSSFiles'
        );
        
        $this->subscribeEvent(
            'Enlight_Controller_Action_PreDispatch_Frontend',
            'beforeFrontedDispatch'
        );
        
        $this->subscribeEvent(
            'Enlight_Controller_Dispatcher_ControllerPath_Backend_WOTippsEmotionCache',
            'onCacheController'
        );

        $this->subscribeEvent(
            'Enlight_Controller_Action_PostDispatch_Frontend',
            'onFrontDispatch'
        );
        
        $menuOptions = array(
            'label' => 'Einkaufswelten Cache leeren',
            'controller' => 'WOTippsEmotionCache',
            'class' => 'sprite-minus-octagon',
            'action' => 'Index',
            'active' => 1,
            'parent' => $this->Menu()->findOneBy(['controller' => 'Performance'])
        );
        
        $this->createMenuItem($menuOptions);
        
        return array
        (
            'success' => true,
        );
    }
    
    public function enable()
    {
        return array
        (
            'success' => true,
            'invalidateCache' => array('template', 'theme')
        );
    }
    
    public function uninstall()
    {
        return array
        (
            'success' => true,
            'invalidateCache' => array('template', 'theme')
        );
    }
    
    public function checkLicenseWrapper($throwException = true)
    {
        if (WOTIPPS_LICENSE_CHECK)
        {
            return $this->checkLicense($throwException);
        }
        else
        {
            return true;
        }
    }
    public function checkLicense($throwException = true)
    {
        
    }
    
    public function addJavascriptFiles(Enlight_Event_EventArgs $args)
    {
        if (1 == version_compare(Shopware()->Config()->Version, "5.2.99")) {
            $js = __DIR__ . '/js/load53.js';
        } else {
            $js = __DIR__ . '/js/load.js';
        }

        return new ArrayCollection(array($js));

    }

    public function onFrontDispatch(Enlight_Event_EventArgs $args)
    {
        $view = $args->getSubject()->View();

        //$view->extendsTemplate($this->Path() . 'Views/frontend/index/index.tpl');
    }
    
    public function addCSSFiles(Enlight_Event_EventArgs $args)
    {
        $less = new LessDefinition(
                array(),
                array(
                    __DIR__ . '/js/load.less'
                    )
                );

        return new ArrayCollection(array($less));

    }

    public function beforeFrontedDispatch(Enlight_Event_EventArgs $args)
    {
        
        // set the template dir
        Shopware()->Template()->addPluginsDir(__DIR__ . '/js/');
        
        Shopware()->Template()->loadFilter('output', 'h20170415');
    }
    
    public function onCacheController(Enlight_Event_EventArgs $args)
    {
        $this->Application()->Template()->addTemplateDir(
                $this->Path() . 'Views/'
                );
                
        return $this->Path() . '/Controllers/Backend/WOTippsEmotionCache.php';
    }
}