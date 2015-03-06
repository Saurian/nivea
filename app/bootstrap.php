<?php

require __DIR__ . '/../vendor/autoload.php';

$configurator = new Nette\Configurator;

$configurator->setDebugMode(array('37.221.241.103'));
$configurator->enableDebugger(__DIR__ . '/log');
$configurator->setTempDirectory(__DIR__ . '/temp');

$configurator->createRobotLoader()
	->addDirectory(__DIR__)
	->register();

$environment = (Nette\Configurator::detectDebugMode('127.0.0.1') or (PHP_SAPI == 'cli' && Nette\Utils\Strings::startsWith(getHostByName(getHostName()), "192.168.")))
    ? 'development'
    : 'production';

$configurator->addConfig(__DIR__ . '/config/config.neon');
$configurator->addConfig(__DIR__ . "/config/config.$environment.neon");


$container = $configurator->createContainer();
$container->httpResponse->addHeader('P3P', 'CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
$container->httpResponse->setHeader('X-Frame-Options', NULL);

\CmsModule\Doctrine\ToManyContainer::register();

$em = $container->createServiceDoctrine__default__entityManager();
$em->getEventManager()->addEventListener(
    array(\Doctrine\ORM\Events::loadClassMetadata),
    new \CmsModule\Doctrine\Listeners\TablePrefixListener("nivea_{$container->getParameters()['contest']['transaction']['name']}_")
);

return $container;
