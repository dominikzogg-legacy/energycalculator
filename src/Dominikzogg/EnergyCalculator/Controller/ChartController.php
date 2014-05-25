<?php

namespace Dominikzogg\EnergyCalculator\Controller;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Dominikzogg\EnergyCalculator\Entity\Day;
use Dominikzogg\EnergyCalculator\Entity\User;
use Dominikzogg\EnergyCalculator\Form\DateRangeType;
use Dominikzogg\EnergyCalculator\Repository\DayRepository;
use Saxulum\RouteController\Annotation\DI;
use Saxulum\RouteController\Annotation\Route;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;
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
    /**
     * @var ManagerRegistry
     */
    protected $doctrine;

    /**
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @var SecurityContext
     */
    protected $security;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

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
        $repo = $this->getRepository(get_class(new Day()));

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

    /**
     * @param  string               $type
     * @param  null                 $data
     * @param  array                $options
     * @param  FormBuilderInterface $parent
     * @return Form
     */
    protected function createForm($type = 'form', $data = null, array $options = array(), FormBuilderInterface $parent = null)
    {
        return $this->formFactory->createBuilder($type, $data, $options, $parent)->getForm();
    }

    /**
     * @return User|Null|string
     */
    protected function getUser()
    {
        if (is_null($this->security->getToken())) {
            return null;
        }

        $user = $this->security->getToken()->getUser();

        if ($user instanceof User) {
            $user = $this->doctrine->getManager()->getRepository(get_class($user))->find($user->getId());
        }

        return $user;
    }
}