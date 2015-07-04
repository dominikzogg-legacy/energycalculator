<?php

namespace Dominikzogg\EnergyCalculator\Controller;

use Saxulum\RouteController\Annotation\DI;
use Saxulum\RouteController\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGenerator;

/**
 * @DI(serviceIds={
 *      "url_generator"
 * })
 */
class IndexController
{
    /**
     * @var UrlGenerator
     */
    protected $urlGenerator;

    public function __construct(UrlGenerator $urlGenerator)
    {
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
     * @Route("/{_locale}", bind="index", method="GET", asserts={"_locale"="([a-z]{2}|[a-z]{2}_[A-Z]{2})"})
     */
    public function indexAction()
    {
        return new RedirectResponse($this->urlGenerator->generate('day_list'), 301);
    }
}
