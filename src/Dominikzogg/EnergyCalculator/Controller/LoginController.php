<?php

namespace Dominikzogg\EnergyCalculator\Controller;

use Saxulum\RouteController\Annotation\DI;
use Saxulum\RouteController\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/{_locale}")
 * @DI(serviceIds={
 *      "security.last_error",
 *      "twig"
 * })
 */
class LoginController
{
    /**
     * @var callback
     */
    protected $securityLastError;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    public function __construct($securityLastError, \Twig_Environment $twig)
    {
        $this->securityLastError = $securityLastError;
        $this->twig = $twig;
    }

    /**
     * @Route("/login", bind="login", method="GET")
     */
    public function loginAction(Request $request)
    {
        $securityLastError = $this->securityLastError;

        // return the rendered template
        return $this->twig->render('@DominikzoggEnergyCalculator/Login/login.html.twig', array(
            'error'         => $securityLastError($request),
            'last_username' => $request->getSession()->get('_security.last_username'),
        ));
    }

    /**
     * @Route("/logout", bind="logout", method="GET")
     */
    public function logoutAction() {}

    /**
     * @Route("/login_check", bind="login_check", method="POST")
     */
    public function logincheckAction() {}
}