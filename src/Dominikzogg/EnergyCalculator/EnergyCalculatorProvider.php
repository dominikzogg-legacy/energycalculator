<?php

namespace Dominikzogg\EnergyCalculator;

use Dominikzogg\EnergyCalculator\Command\UserCreateCommand;
use Dominikzogg\EnergyCalculator\Entity\User;
use Dominikzogg\EnergyCalculator\Form\AjaxChoiceType;
use Dominikzogg\EnergyCalculator\Form\AjaxEntityType;
use Dominikzogg\EnergyCalculator\Form\EntityType;
use Dominikzogg\EnergyCalculator\Form\FormTypeExtension;
use Dominikzogg\EnergyCalculator\Form\SimpleDateType;
use Dominikzogg\EnergyCalculator\Provider\MenuProvider;
use Dominikzogg\EnergyCalculator\Twig\FormHelperExtension;
use Saxulum\BundleProvider\Provider\AbstractBundleProvider;
use Saxulum\UserProvider\Model\AbstractUser;
use Saxulum\UserProvider\Silex\Provider\SaxulumUserProvider;
use Silex\Application;

class EnergyCalculatorProvider extends AbstractBundleProvider
{
    public function register(Application $app)
    {
        $app->register(new MenuProvider());
        
        $app->register(new SaxulumUserProvider(), array(
            'saxulum.userprovider.userclass' => get_class(new User())
        ));
        
        $app['twig'] = $app->share($app->extend('twig', function (\Twig_Environment $twig) {
            $twig->addExtension(new FormHelperExtension());

            return $twig;
        }));

        $app['form.types'] = $app->share($app->extend('form.types', function ($types) use ($app) {
            $types[] = new SimpleDateType();
            $types[] = new EntityType($app['doctrine']);
            $types[] = new AjaxEntityType($app['doctrine']);

            return $types;
        }));

        $app['security.role_hierarchy'] = $app->share($app->extend('security.role_hierarchy', function ($roleHierarchy) use ($app) {

            // comestible
            $roleHierarchy['ROLE_COMESTIBLE_LIST'] = array();
            $roleHierarchy['ROLE_COMESTIBLE_CREATE'] = array('ROLE_COMESTIBLE_LIST');
            $roleHierarchy['ROLE_COMESTIBLE_EDIT'] = array('ROLE_COMESTIBLE_LIST');
            $roleHierarchy['ROLE_COMESTIBLE_VIEW'] = array('ROLE_COMESTIBLE_LIST');
            $roleHierarchy['ROLE_COMESTIBLE_DELETE'] = array('ROLE_COMESTIBLE_LIST');
            
            // day
            $roleHierarchy['ROLE_DAY_LIST'] = array();
            $roleHierarchy['ROLE_DAY_CREATE'] = array('ROLE_DAY_LIST');
            $roleHierarchy['ROLE_DAY_EDIT'] = array('ROLE_DAY_LIST');
            $roleHierarchy['ROLE_DAY_VIEW'] = array('ROLE_DAY_LIST');
            $roleHierarchy['ROLE_DAY_DELETE'] = array('ROLE_DAY_LIST');

            // user
            $roleHierarchy['ROLE_USER_LIST'] = array();
            $roleHierarchy['ROLE_USER_CREATE'] = array('ROLE_USER_LIST');
            $roleHierarchy['ROLE_USER_EDIT'] = array('ROLE_USER_LIST');
            $roleHierarchy['ROLE_USER_VIEW'] = array('ROLE_USER_LIST');
            $roleHierarchy['ROLE_USER_DELETE'] = array('ROLE_USER_LIST');

            $roleHierarchy[AbstractUser::ROLE_USER][] = 'ROLE_COMESTIBLE_CREATE';
            $roleHierarchy[AbstractUser::ROLE_USER][] = 'ROLE_COMESTIBLE_EDIT';
            $roleHierarchy[AbstractUser::ROLE_USER][] = 'ROLE_COMESTIBLE_VIEW';
            $roleHierarchy[AbstractUser::ROLE_USER][] = 'ROLE_COMESTIBLE_DELETE';

            $roleHierarchy[AbstractUser::ROLE_USER][] = 'ROLE_DAY_CREATE';
            $roleHierarchy[AbstractUser::ROLE_USER][] = 'ROLE_DAY_EDIT';
            $roleHierarchy[AbstractUser::ROLE_USER][] = 'ROLE_DAY_VIEW';
            $roleHierarchy[AbstractUser::ROLE_USER][] = 'ROLE_DAY_DELETE';

            $roleHierarchy[AbstractUser::ROLE_ADMIN][] = 'ROLE_USER_CREATE';
            $roleHierarchy[AbstractUser::ROLE_ADMIN][] = 'ROLE_USER_EDIT';
            $roleHierarchy[AbstractUser::ROLE_ADMIN][] = 'ROLE_USER_VIEW';
            $roleHierarchy[AbstractUser::ROLE_ADMIN][] = 'ROLE_USER_DELETE';

            return $roleHierarchy;
        }));

        $app['security.access_rules'] = $app->share($app->extend('security.access_rules', function ($rules) use ($app) {
            $rules[] = array('^/_profiler*', 'IS_AUTHENTICATED_ANONYMOUSLY');
            $rules[] = array('^/[^/]+/login*', 'IS_AUTHENTICATED_ANONYMOUSLY');
            $rules[] = array('^/[^/]+/logout*', 'IS_AUTHENTICATED_ANONYMOUSLY');
            $rules[] = array('^/[^/]+/admin', 'ROLE_ADMIN');
            $rules[] = array('^/[^/]+', 'ROLE_USER');

            return $rules;
        }));

        $app['console.commands'] = $app->share(
            $app->extend('console.commands', function (array $commands) use ($app) {
                $commands[] = new UserCreateCommand(null, $app['saxulum.userprovider.userclass']);

                return $commands;
            })
        );

        $this->addControllers($app);
        $this->addDoctrineOrmMappings($app);
        $this->addTranslatorRessources($app);
        $this->addTwigLoaderFilesystemPath($app);
    }

    public function boot(Application $app)
    {
    }
}
