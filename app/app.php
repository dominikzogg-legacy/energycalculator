<?php

use Dflydev\Silex\Provider\DoctrineOrm\DoctrineOrmServiceProvider;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Igorw\Silex\ConfigServiceProvider;
use Knp\Menu\Integration\Silex\KnpMenuServiceProvider;
use Silex\Application;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\HttpCacheServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\SwiftmailerServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\WebProfilerServiceProvider;
use Saxulum\Accessor\AccessorTrait;
use Saxulum\Accessor\Accessors\Get;
use Saxulum\Accessor\Accessors\Is;
use Saxulum\Accessor\Accessors\Set;
use Saxulum\AsseticTwig\Silex\Provider\AsseticTwigProvider;
use Saxulum\Console\Silex\Provider\ConsoleProvider;
use Saxulum\DoctrineOrmManagerRegistry\Silex\Provider\DoctrineOrmManagerRegistryProvider;
use Saxulum\MenuProvider\Silex\Provider\SaxulumMenuProvider;
use Saxulum\PaginationProvider\Silex\Provider\SaxulumPaginationProvider;
use Saxulum\RouteController\Provider\RouteControllerProvider;
use Saxulum\SaxulumBootstrapProvider\Silex\Provider\SaxulumBootstrapProvider;
use Saxulum\SaxulumWebProfiler\Provider\SaxulumWebProfilerProvider;
use Saxulum\Translation\Silex\Provider\TranslationProvider;
use Symfony\Component\Validator\Mapping\Factory\LazyLoadingMetadataFactory;
use Symfony\Component\Validator\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Validator\Mapping\Loader\LoaderChain;
use Symfony\Component\Validator\Mapping\Loader\StaticMethodLoader;

// annotation registry
AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

// accessor registry
AccessorTrait::registerAccessor(new Get());
AccessorTrait::registerAccessor(new Is());
AccessorTrait::registerAccessor(new Set());

// create new silex app
$app = new Application();

$app['root'] = dirname(__DIR__);
$app['debug'] = isset($debug) ? $debug : false;
$app['env'] = isset($env) ? $env : 'prod';

$app->register(new TranslationServiceProvider());
$app->register(new TwigServiceProvider());
$app->register(new UrlGeneratorServiceProvider());
$app->register(new SessionServiceProvider());
$app->register(new ValidatorServiceProvider());
$app->register(new FormServiceProvider());
$app->register(new HttpCacheServiceProvider());
$app->register(new SecurityServiceProvider());
$app->register(new SwiftmailerServiceProvider());
$app->register(new MonologServiceProvider());
$app->register(new DoctrineServiceProvider());
$app->register(new ServiceControllerServiceProvider());

$app->register(new ConsoleProvider());
$app->register(new DoctrineOrmServiceProvider());
$app->register(new DoctrineOrmManagerRegistryProvider());
$app->register(new KnpMenuServiceProvider());
$app->register(new SaxulumMenuProvider());
$app->register(new AsseticTwigProvider());
$app->register(new RouteControllerProvider());
$app->register(new SaxulumPaginationProvider());
$app->register(new TranslationProvider());
$app->register(new SaxulumBootstrapProvider());

$app['validator.mapping.class_metadata_factory'] = $app->share(function () {
    return new LazyLoadingMetadataFactory(
        new LoaderChain(array(
            new AnnotationLoader(new AnnotationReader),
            new StaticMethodLoader
        ))
    );
});

if ($app['debug']) {
    $app->register(new WebProfilerServiceProvider());
    $app->register(new SaxulumWebProfilerProvider());
}

// load all project providers
$app->register(new \Dominikzogg\EnergyCalculator\EnergyCalculatorProvider());

// config overrides
$app->register(new ConfigServiceProvider("{$app['root']}/app/config/config.yml", array('root_dir' => $app['root'], 'env' => $app['env'])));
$app->register(new ConfigServiceProvider("{$app['root']}/app/config/config_{$app['env']}.yml", array('root_dir' => $app['root'])));
$app->register(new ConfigServiceProvider("{$app['root']}/app/config/parameters.yml"));

// return the app
return $app;
