<?php

namespace Dominikzogg\EnergyCalculator\Controller;

use Doctrine\Common\Persistence\ManagerRegistry;
use Dominikzogg\EnergyCalculator\Entity\Day;
use Dominikzogg\EnergyCalculator\Form\DateRangeType;
use Dominikzogg\EnergyCalculator\Repository\DayRepository;
use Saxulum\RouteController\Annotation\DI;
use Saxulum\RouteController\Annotation\Route;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * @Route("/{_locale}/chart")
 * @DI(serviceIds={
 *      "doctrine",
 *      "form.factory",
 *      "security",
 *      "twig"
 * })
 */
class ChartController extends AbstractController
{
    public function __construct(
        ManagerRegistry $doctrine,
        FormFactory $formFactory,
        SecurityContext $security,
        \Twig_Environment $twig
    ) {
        $this->doctrine = $doctrine;
        $this->formFactory = $formFactory;
        $this->security = $security;
        $this->twig = $twig;
    }

    /**
     * @Route("/weight", bind="chart_weight", method="GET")
     * @param  Request  $request
     * @return Response
     */
    public function weightAction(Request $request)
    {
        $dateRangeType = new DateRangeType();

        $dateRangeForm = $this->createForm($dateRangeType, array(
            'from' => $this->getDefaultFrom('-1 week'),
            'to' => $this->getDefaultTo(),
        ));

        $dateRangeForm->handleRequest($request);
        $dateRangeFormData = $dateRangeForm->getData();

        $from = $dateRangeFormData['from'];
        $to = $dateRangeFormData['to'];

        /** @var DayRepository $repo */
        $repo = $this->getRepositoryForClass(Day::class);

        $days = $repo->getInRange($from, $to, $this->getUser());
        $allDays = $this->getDaysOrNull($days, $from, $to);

        $minWeight = $this->getMinWeight($days);
        $maxWeight = $this->getMaxWeight($days);

        return $this->render('@DominikzoggEnergyCalculator/Chart/weight.html.twig', array(
            'daterangeform' => $dateRangeForm->createView(),
            'alldays' => $allDays,
            'minweight' => $minWeight,
            'maxweight' => $maxWeight,
        ));
    }

    /**
     * @Route("/calorie", bind="chart_calorie", method="GET")
     * @param  Request  $request
     * @return Response
     */
    public function caloriesAction(Request $request)
    {
        $dateRangeType = new DateRangeType();

        $dateRangeForm = $this->createForm($dateRangeType, array(
            'from' => $this->getDefaultFrom('-1 week'),
            'to' => $this->getDefaultTo(),
        ));

        $dateRangeForm->handleRequest($request);
        $dateRangeFormData = $dateRangeForm->getData();

        $from = $dateRangeFormData['from'];
        $to = $dateRangeFormData['to'];

        /** @var DayRepository $repo */
        $repo = $this->getRepositoryForClass(Day::class);

        $days = $repo->getInRange($from, $to, $this->getUser());
        $allDays = $this->getDaysOrNull($days, $from, $to);

        $minCalorie = $this->getMinCalorie($days);
        $maxCalorie = $this->getMaxCalorie($days);

        return $this->render('@DominikzoggEnergyCalculator/Chart/calorie.html.twig', array(
            'daterangeform' => $dateRangeForm->createView(),
            'alldays' => $allDays,
            'mincalorie' => $minCalorie,
            'maxcalorie' => $maxCalorie,
        ));
    }

    /**
     * @Route("/energymix", bind="chart_energymix", method="GET")
     * @param  Request  $request
     * @return Response
     */
    public function energymixAction(Request $request)
    {
        $dateRangeType = new DateRangeType();

        $dateRangeForm = $this->createForm($dateRangeType, array(
            'from' => $this->getDefaultFrom('-1 week'),
            'to' => $this->getDefaultTo(),
        ));

        $dateRangeForm->handleRequest($request);
        $dateRangeFormData = $dateRangeForm->getData();

        $from = $dateRangeFormData['from'];
        $to = $dateRangeFormData['to'];

        /** @var DayRepository $repo */
        $repo = $this->getRepositoryForClass(Day::class);

        $days = $repo->getInRange($from, $to, $this->getUser());
        $allDays = $this->getDaysOrNull($days, $from, $to);

        $minEnergyMix = $this->getMinEnergyMix($days);
        $maxEnergyMix = $this->getMaxEnergyMix($days);

        return $this->render('@DominikzoggEnergyCalculator/Chart/energymix.html.twig', array(
            'daterangeform' => $dateRangeForm->createView(),
            'alldays' => $allDays,
            'minenergymix' => $minEnergyMix,
            'maxenergymix' => $maxEnergyMix,
        ));
    }

    /**
     * @param  string|null $modifier
     * @return \DateTime
     */
    protected function getDefaultFrom($modifier = null)
    {
        $from = new \DateTime();
        if (null !== $modifier) {
            $from->modify($modifier);
        }
        $from->setTime(0, 0, 0);

        return $from;
    }

    /**
     * @param  string|null $modifier
     * @return \DateTime
     */
    protected function getDefaultTo($modifier = null)
    {
        $to = new \DateTime();
        if (null !== $modifier) {
            $to->modify($modifier);
        }
        $to->setTime(23, 59, 59);

        return $to;
    }

    /**
     * @param  Day[]     $days
     * @param  \DateTime $from
     * @param  \DateTime $to
     * @return Day[]
     */
    protected function getDaysOrNull(array $days, \DateTime $from, \DateTime $to)
    {
        $from = clone $from;

        $daysPerDate = array();
        foreach ($days as $day) {
            $daysPerDate[$day->getDate()->format('d.m.Y')] = $day;
        }

        $return = array();

        while ($from->format('Ymd') <= $to->format('Ymd')) {
            $fromAsString = $from->format('d.m.Y');
            $return[$fromAsString] = isset($daysPerDate[$fromAsString]) ? $daysPerDate[$fromAsString] : null;
            $from->modify('+1day');
        }

        return $return;
    }

    /**
     * @param  Day[] $days
     * @return float
     */
    protected function getMinWeight(array $days)
    {
        $minWeight = null;
        foreach ($days as $day) {
            if (null === $minWeight || $day->getWeight() < $minWeight) {
                $minWeight = $day->getWeight();
            }
        }

        return null !== $minWeight ? $minWeight : 0;
    }

    /**
     * @param  Day[] $days
     * @return float
     */
    protected function getMaxWeight(array $days)
    {
        $maxWeight = null;
        foreach ($days as $day) {
            if (null === $maxWeight || $day->getWeight() > $maxWeight) {
                $maxWeight = $day->getWeight();
            }
        }

        return null !== $maxWeight ? $maxWeight : 500;
    }

    /**
     * @param  Day[] $days
     * @return float
     */
    protected function getMinCalorie(array $days)
    {
        $minCalorie = null;
        foreach ($days as $day) {
            if (null === $minCalorie || $day->getCalorie() < $minCalorie) {
                $minCalorie = $day->getCalorie();
            }
        }

        return null !== $minCalorie ? $minCalorie : 0;
    }

    /**
     * @param  Day[] $days
     * @return float
     */
    protected function getMaxCalorie(array $days)
    {
        $maxCalorie = null;
        foreach ($days as $day) {
            if (null === $maxCalorie || $day->getCalorie() > $maxCalorie) {
                $maxCalorie = $day->getCalorie();
            }
        }

        return null !== $maxCalorie ? $maxCalorie : 10000;
    }

    /**
     * @param  Day[] $days
     * @return float
     */
    protected function getMinEnergyMix(array $days)
    {
        $minEnergyMix = null;
        foreach ($days as $day) {
            if (null === $minEnergyMix || $day->getProtein() < $minEnergyMix) {
                $minEnergyMix = $day->getProtein();
            }
            if (null === $minEnergyMix || $day->getCarbohydrate() < $minEnergyMix) {
                $minEnergyMix = $day->getCarbohydrate();
            }
            if (null === $minEnergyMix || $day->getFat() < $minEnergyMix) {
                $minEnergyMix = $day->getFat();
            }
        }

        return null !== $minEnergyMix ? $minEnergyMix : 0;
    }

    /**
     * @param  Day[] $days
     * @return float
     */
    protected function getMaxEnergyMix(array $days)
    {
        $maxEnergyMix = null;
        foreach ($days as $day) {
            if (null === $maxEnergyMix || $day->getProtein() > $maxEnergyMix) {
                $maxEnergyMix = $day->getProtein();
            }
            if (null === $maxEnergyMix || $day->getCarbohydrate() > $maxEnergyMix) {
                $maxEnergyMix = $day->getCarbohydrate();
            }
            if (null === $maxEnergyMix || $day->getFat() > $maxEnergyMix) {
                $maxEnergyMix = $day->getFat();
            }
        }

        return null !== $maxEnergyMix ? $maxEnergyMix : 1000;
    }
}
