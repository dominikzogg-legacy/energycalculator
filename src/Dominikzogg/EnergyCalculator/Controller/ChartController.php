<?php

namespace Dominikzogg\EnergyCalculator\Controller;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Dominikzogg\EnergyCalculator\Entity\Day;
use Dominikzogg\EnergyCalculator\Repository\DayRepository;
use Saxulum\RouteController\Annotation\DI;
use Saxulum\RouteController\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/{_locale}/chart")
 * @DI(serviceIds={
 *      "doctrine",
 *      "twig"
 * })
 */
class ChartController
{
    /**
     * @var ManagerRegistry
     */
    protected $doctrine;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    public function __construct(
        ManagerRegistry $doctrine,
        \Twig_Environment $twig
    ) {
        $this->doctrine = $doctrine;
        $this->twig = $twig;
    }

    /**
     * @Route("/weight", bind="chart_weight", method="GET")
     * @param Request $request
     * @return Response
     */
    public function weightAction(Request $request)
    {
        $from = $this->getFrom($request->query->get('from', null), '-1 month');
        $to = $this->getTo($request->query->get('to', null));

        /** @var DayRepository $repo */
        $repo = $this->getRepository(get_class(new Day()));

        $days = $repo->getInRange($from, $to);
        $allDays = $this->getDaysOrNull($days, $from, $to);

        $minWeight = $this->getMinWeight($days);
        $maxWeight = $this->getMaxWeight($days);

        return $this->render('@DominikzoggEnergyCalculator/Chart/weight.html.twig', array(
            'alldays' => $allDays,
            'minweight' => $minWeight,
            'maxweight' => $maxWeight
        ));
    }

    /**
     * @param null|string $from
     * @param null|string $defaultModifier
     * @return \DateTime
     */
    protected function getFrom($from, $defaultModifier = null)
    {
        if(null !== $from) {
            $from = new \DateTime($from);
        } else {
            $from = new \DateTime();
            if(null !== $defaultModifier) {
                $from->modify($defaultModifier);
            }
        }

        $from->setTime(0,0,0);

        return $from;
    }

    /**
     * @param null|string $to
     * @param null|string $defaultModifier
     * @return \DateTime
     */
    protected function getTo($to, $defaultModifier = null)
    {
        if(null !== $to) {
            $to = new \DateTime($to);
        } else {
            $to = new \DateTime();
            if(null !== $defaultModifier) {
                $to->modify($defaultModifier);
            }
        }

        $to->setTime(23,59,59);

        return $to;
    }

    /**
     * @param Day[] $days
     * @param \DateTime $from
     * @param \DateTime $to
     * @return Day[]
     */
    protected function getDaysOrNull(array $days, \DateTime $from, \DateTime $to)
    {
        $from = clone $from;

        $daysPerDate = array();
        foreach($days as $day) {
            $daysPerDate[$day->getDate()->format('d.m.Y')] = $day;
        }

        $return = array();

        while($from->format('Ymd') <= $to->format('Ymd')) {
            $fromAsString = $from->format('d.m.Y');
            $return[$fromAsString] = isset($daysPerDate[$fromAsString]) ? $daysPerDate[$fromAsString] : null;
            $from->modify('+1day');
        }

        return $return;
    }

    /**
     * @param Day[] $days
     * @return float
     */
    protected function getMinWeight(array $days)
    {
        $minWeight = null;
        foreach($days as $day) {
            if(null === $minWeight || $day->getWeight() < $minWeight) {
                $minWeight = $day->getWeight();
            }
        }

        return null !== $minWeight ? $minWeight : 0;
    }

    /**
     * @param Day[] $days
     * @return float
     */
    protected function getMaxWeight(array $days)
    {
        $maxWeight = null;
        foreach($days as $day) {
            if(null === $maxWeight || $day->getWeight() > $maxWeight) {
                $maxWeight = $day->getWeight();
            }
        }

        return null !== $maxWeight ? $maxWeight : 500;
    }

    /**
     * @param string $view
     * @param  array  $parameters
     * @return string
     */
    protected function render($view, array $parameters = array())
    {
        return new Response($this->twig->render($view, $parameters));
    }

    /**
     * @param string $class
     * @return EntityManager|null
     */
    protected function getManager($class)
    {
        return $this->doctrine->getManagerForClass($class);
    }

    /**
     * @param string $class
     * @return EntityRepository
     */
    protected function getRepository($class)
    {
        return $this->getManager($class)->getRepository($class);
    }
}