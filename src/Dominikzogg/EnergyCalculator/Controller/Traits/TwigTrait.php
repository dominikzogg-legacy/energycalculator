<?php

namespace Dominikzogg\EnergyCalculator\Controller\Traits;

use Symfony\Component\HttpFoundation\Response;

trait TwigTrait
{
    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @param $view
     * @param  array  $parameters
     * @return Response
     */
    protected function render($view, array $parameters = array())
    {
        return new Response($this->twig->render($view, $parameters));
    }
}