<?php

namespace Dominikzogg\EnergyCalculator\Controller;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Dominikzogg\EnergyCalculator\Entity\User;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

abstract class AbstractController
{
    /**
     * @var ManagerRegistry
     */
    protected $doctrine;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var AuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @return User
     * @throws \Exception
     */
    protected function getUser()
    {
        if (null === $token = $this->tokenStorage->getToken()) {
            throw new \Exception("No user token!");
        }

        $user = $token->getUser();

        if(!$user instanceof User) {
            throw new \Exception("Invalid user token!");
        }

        $user = $this->doctrine->getManager()->getRepository(get_class($user))->find($user->getId());

        if(null === $user) {
            throw new \Exception("User not found in database!");
        }

        return $user;
    }

    /**
     * @return ManagerRegistry
     */
    protected function getDoctrine()
    {
        return $this->doctrine;
    }

    /**
     * @param  string             $class
     * @return ObjectManager|null
     */
    protected function getManagerForClass($class)
    {
        return $this->doctrine->getManagerForClass($class);
    }

    /**
     * @param  string           $class
     * @return ObjectRepository
     */
    protected function getRepositoryForClass($class)
    {
        return $this->getManagerForClass($class)->getRepository($class);
    }

    /**
     * @param $view
     * @param  array    $parameters
     * @return Response
     */
    protected function render($view, array $parameters = array())
    {
        return new Response($this->twig->render($view, $parameters));
    }

    /**
     * @param  string $type
     * @param  null   $data
     * @param  array  $options
     * @return FormInterface
     */
    protected function createForm($type = 'form', $data = null, array $options = array())
    {
        return $this->formFactory->create($type, $data, $options);
    }
}