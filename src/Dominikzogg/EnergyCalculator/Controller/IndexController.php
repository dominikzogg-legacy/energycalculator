<?php

namespace Dominikzogg\EnergyCalculator\Controller;

use Saxulum\RouteController\Annotation\DI;
use Saxulum\RouteController\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGenerator;

/**
 * @DI(serviceIds={
 *      "twig",
 *      "url_generator"
 * })
 */
class IndexController
{
    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var UrlGenerator
     */
    protected $urlGenerator;

    public function __construct(\Twig_Environment $twig, UrlGenerator $urlGenerator)
    {
        $this->twig = $twig;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @Route("/", bind="index_redirect", method="GET")
     */
    public function indexredirectAction()
    {
        return new RedirectResponse($this->urlGenerator->generate('index'), 301);
    }

    /**
     * @Route("/{_locale}", bind="index", method="GET")
     */
    public function indexAction()
    {
        return $this->twig->render('@DominikzoggEnergyCalculator/Index/index.html.twig');
    }
}