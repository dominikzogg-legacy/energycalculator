<?php

namespace Dominikzogg\EnergyCalculator;

use Dominikzogg\EnergyCalculator\Entity\User;
use Dominikzogg\EnergyCalculator\Provider\MenuProvider;
use Dominikzogg\EnergyCalculator\Twig\FormHelperExtension;
use Saxulum\BundleProvider\Provider\AbstractBundleProvider;
use Saxulum\SaxulumBootstrapProvider\SaxulumBootstrapProvider;
use Saxulum\UserProvider\SaxulumUserProvider;
use Silex\Application;

class EnergyCalculatorProvider extends AbstractBundleProvider
{
    public function register(Application $app)
    {
        $this->addCommands($app);
        $this->addControllers($app);
        $this->addDoctrineOrmMappings($app);
        $this->addTranslatorRessources($app);
        $this->addTwigLoaderFilesystemPath($app);

        $app->register(new SaxulumUserProvider());
        $app->register(new SaxulumBootstrapProvider());
        $app->register(new MenuProvider());

        $app['twig'] = $app->share($app->extend('twig', function(\Twig_Environment $twig) {
            $twig->addExtension(new FormHelperExtension());

            return $twig;
        }));

        $app['saxulum.userprovider.userclass'] = get_class(new User());

        $rules = $app['security.access_rules'];
        $rules[] = array('^/_profiler*', 'IS_AUTHENTICATED_ANONYMOUSLY');
        $rules[] = array('^/[^/]*/admin', 'ROLE_ADMIN');
        $rules[] = array('^/[^/]*', 'ROLE_USER');
        $app['security.access_rules'] = $rules;
    }

    public function boot(Application $app) {}
}
