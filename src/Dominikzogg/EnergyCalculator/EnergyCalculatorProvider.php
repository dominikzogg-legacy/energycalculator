<?php

namespace Dominikzogg\EnergyCalculator;

use Dominikzogg\EnergyCalculator\Command\UserCreateCommand;
use Dominikzogg\EnergyCalculator\Entity\User;
use Dominikzogg\EnergyCalculator\Form\SimpleDateTypeExtension;
use Dominikzogg\EnergyCalculator\Provider\MenuProvider;
use Dominikzogg\EnergyCalculator\Twig\FormHelperExtension;
use Saxulum\BundleProvider\Provider\AbstractBundleProvider;
use Saxulum\PaginationProvider\Silex\Provider\SaxulumPaginationProvider;
use Saxulum\SaxulumBootstrapProvider\Silex\Provider\SaxulumBootstrapProvider;
use Saxulum\UserProvider\Silex\Provider\SaxulumUserProvider;
use Silex\Application;

class EnergyCalculatorProvider extends AbstractBundleProvider
{
    public function register(Application $app)
    {
        $app->register(new SaxulumUserProvider());
        $app->register(new SaxulumBootstrapProvider());
        $app->register(new MenuProvider());
        $app->register(new SaxulumPaginationProvider());

        $app['twig'] = $app->share($app->extend('twig', function(\Twig_Environment $twig) {
            $twig->addExtension(new FormHelperExtension());

            return $twig;
        }));

        $app['form.extensions'] = $app->share($app->extend('form.extensions', function ($extensions) use ($app) {
            $extensions[] = new SimpleDateTypeExtension();

            return $extensions;
        }));

        $app['saxulum.userprovider.userclass'] = get_class(new User());

        $rules = $app['security.access_rules'];
        $rules[] = array('^/_profiler*', 'IS_AUTHENTICATED_ANONYMOUSLY');
        $rules[] = array('^/[^/]*/admin', 'ROLE_ADMIN');
        $rules[] = array('^/[^/]*', 'ROLE_USER');
        $app['security.access_rules'] = $rules;

        $app['console.commands'] = $app->share(
            $app->extend('console.commands', function (array $commands) use ($app) {
                $commands[] = new UserCreateCommand(null, $app['saxulum.userprovider.userclass']);

                return $commands;
            })
        );

        //$this->addCommands($app);
        $this->addControllers($app);
        $this->addDoctrineOrmMappings($app);
        $this->addTranslatorRessources($app);
        $this->addTwigLoaderFilesystemPath($app);
    }

    public function boot(Application $app) {}
}
