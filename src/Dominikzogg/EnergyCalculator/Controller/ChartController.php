<?php

namespace Dominikzogg\EnergyCalculator\Controller;

use Doctrine\Common\Persistence\ManagerRegistry;
use Dominikzogg\EnergyCalculator\Controller\Traits\FormTrait;
use Dominikzogg\EnergyCalculator\Controller\Traits\DoctrineTrait;
use Dominikzogg\EnergyCalculator\Controller\Traits\SecurityTrait;
use Dominikzogg\EnergyCalculator\Controller\Traits\TwigTrait;
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
class ChartController
{
    use DoctrineTrait;
    use SecurityTrait;
    use TwigTrait;
    use FormTrait;

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
     * @param Request $request
     * @return Response
     */
    public function weightAction(Request $request)
    {
        $dateRangeType = new DateRangeType();

        $dateRangeForm = $this->createForm($dateRangeType, array(
            'from' => $this->getDefaultFrom('-1 week'),
            'to' => $this->getDefaultTo()
        ));

        $dateRangeForm->handleRequest($request);
        $dateRangeFormData = $dateRangeForm->getData();

        $from = $dateRangeFormData['from'];
        $to = $dateRangeFormData['to'];

        /** @var DayRepository $repo */
        $repo = $this->getRepositoryForClass(get_class(new Day()));

        $days = $repo->getInRange($from, $to, $this->getUser());

        foreach($days as $day) {
            if($day->getWeight() === null) {
                var_dump($day->getDate());
            }

        }

        $allDays = $this->getDaysOrNull($days, $from, $to);

        $minWeight = $this->getMinWeight($days);
        $maxWeight = $this->getMaxWeight($days);

        return $this->render('@DominikzoggEnergyCalculator/Chart/weight.html.twig', array(
            'daterangeform' => $dateRangeForm->createView(),
            'alldays' => $allDays,
            'minweight' => $minWeight,
            'maxweight' => $maxWeight
        ));
    }

    /**
     * @param null $modifier
     * @return \DateTime
     */
    protected function getDefaultFrom($modifier = null)
    {
        $from = new \DateTime();
        if(null !== $modifier) {
            $from->modify($modifier);
        }
        $from->setTime(0,0,0);

        return $from;
    }

    /**
     * @param null $modifier
     * @return \DateTime
     */
    protected function getDefaultTo($modifier = null)
    {
        $to = new \DateTime();
        if(null !== $modifier) {
            $to->modify($modifier);
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
}