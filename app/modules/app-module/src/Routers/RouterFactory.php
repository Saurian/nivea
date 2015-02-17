<?php

/**
 *
 * This file is part of the 2015_02_Q10Plus
 *
 * Copyright (c) 2015
 *
 * @file RouterFactory.php
 * @author  Pavel Paulík <pavel.paulik1@gmail.com>
 */

namespace AppModule\Routers;

use Kdyby\Translation\Translator;
use Nette,
    Nette\Application\Routers\RouteList,
    Nette\Application\Routers\Route;
use Tracy\Debugger;


/**
 * Router factory.
 */
class RouterFactory
{
    /** @var Nette\DI\Container */
    private $container;

    /** @var Nette\Caching\IStorage */
    private $cache;

    /** @var string */
    private $defaultLang;

    /** @var mixed[] */
    public $defaults = array(
        'website' => array(
            'name' => 'Presentation',
            'title' => '%n %s %t',
            'titleSeparator' => '|',
            'keywords' => '',
            'description' => '',
            'author' => '',
            'robots' => 'index, follow',
            'routePrefix' => '',
            'oneWayRoutePrefix' => '',
            'languages' =>  array('cs', 'en'),
            'defaultLanguage' => 'cs',
            'defaultPresenter' => 'Homepage',
            'errorPresenter' => 'Cms:Error',
            'layout' => '@cms/bootstrap',
            'cacheMode' => '',
            'cacheValue' => '10',
            'theme' => '',
        ),
    );


    function __construct($defaultLang, \Nette\DI\Container $container, \Nette\Caching\IStorage $cache)
    {
        $this->cache       = $cache;
        $this->container   = $container;
        $this->defaultLang = $defaultLang;
    }


    /**
     * @return \Nette\Application\IRouter
     */
    public function createRouter()
    {
        $router   = new RouteList();
        $router[] = new Route('index.php', 'App:Registration:default', Route::ONE_WAY);

        $router[] = $adminRouter = new RouteList('Cms');
        $adminRouter[] = new Route('admin/[<locale=cs cs|en>/]<presenter>/<action>[/<id>]', array(
            'presenter' => 'Dashboard',
            'action'    => 'default'
        ));

        $router[] = $frontRouter = new RouteList('App');

        $frontRouter[] = new Route('sitemap.xml', array(
            'presenter' => 'Sitemap',
            'action'    => 'sitemap',
        ));

        // detect prefix
        $prefix = $this->defaults['website']['routePrefix'];
        $languages = $this->defaults['website']['languages'];
        $mask = sprintf("[<locale=%s %s>/]<slug .+>[/<presenter>/<action>[/<id>]]", 'cs', 'cs|en');

        $frontRouter[] = new Route("[<locale={$this->defaultLang} sk|hu|cs>/]<presenter>/<action>[/<id>]", array(
                'presenter' => array(
                    Route::VALUE        => 'Homepage',
                    Route::FILTER_TABLE => array(),
                ),
                'action'    => array(
                    Route::VALUE        => 'default',
                    Route::FILTER_TABLE => array(
                        'odeslana'   => 'send',
                    ),
                ),
            )
        );

        return $router;
    }

}
