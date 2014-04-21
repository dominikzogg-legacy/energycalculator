<?php

namespace Dominikzogg\EnergyCalculator;

use Dominikzogg\EnergyCalculator\Provider\MenuProvider;
use Dominikzogg\EnergyCalculator\Provider\UserProvider;
use Saxulum\BundleProvider\Provider\AbstractBundleProvider;
use Saxulum\SaxulumBootstrapProvider\SaxulumBootstrapProvider;
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

        $app->register(new SaxulumBootstrapProvider());
        $app->register(new MenuProvider());

        $app['security.firewalls'] = array(
            'default' => array(
                'pattern' => '/',
                'form' => array(
                    'login_path' => 'login',
                    'check_path' => 'login_check'
                ),
                'logout' => array(
                    'logout_path' => 'logout'
                ),
                'users' => $app->share(function () use ($app) {
                    return new UserProvider($app['orm.em']);
                }),
                'anonymous' => true,
            ),
        );

        $app['security.access_rules'] = array(
            array('^/[^/]*/admin', 'ROLE_ADMIN'),
            array('^/[^/]*/login', 'IS_AUTHENTICATED_ANONYMOUSLY'),
            array('^/[^/]*', 'ROLE_USER'),
        );

        $app['security.role_hierarchy'] = array(
            'ROLE_ADMIN' => array('ROLE_USER'),
        );
    }

    public function boot(Application $app) {}
}
