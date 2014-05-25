<?php

namespace Dominikzogg\EnergyCalculator\Controller;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityRepository;
use Dominikzogg\EnergyCalculator\Controller\Traits\FormTrait;
use Dominikzogg\EnergyCalculator\Controller\Traits\DoctrineTrait;
use Dominikzogg\EnergyCalculator\Controller\Traits\SecurityTrait;
use Dominikzogg\EnergyCalculator\Controller\Traits\TwigTrait;
use Dominikzogg\EnergyCalculator\Entity\UserReferenceInterface;
use Knp\Component\Pager\Paginator;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Translation\Translator;

abstract class AbstractCRUDController
{
    use DoctrineTrait;
    use FormTrait;
    use SecurityTrait;
    use TwigTrait;

    protected $entityClass;
    protected $formTypeClass;
    protected $listRoute;
    protected $editRoute;
    protected $showRoute;
    protected $deleteRoute;
    protected $listTemplate;
    protected $editTemplate;
    protected $showTemplate;
    protected $transPrefix;

    /**
     * @var Paginator
     */
    protected $paginator;

    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @var UrlGenerator
     */
    protected $urlGenerator;

    /**
     * @param ManagerRegistry $doctrine
     * @param FormFactory $formFactory
     * @param Paginator $paginator
     * @param SecurityContext $security
     * @param Translator $translator
     * @param \Twig_Environment $twig
     * @param UrlGenerator $urlGenerator
     */
    public function __construct(
        ManagerRegistry $doctrine,
        FormFactory $formFactory,
        Paginator $paginator,
        SecurityContext $security,
        Translator $translator,
        \Twig_Environment $twig,
        UrlGenerator $urlGenerator
    ) {
        $this->doctrine = $doctrine;
        $this->formFactory = $formFactory;
        $this->paginator = $paginator;
        $this->security = $security;
        $this->translator = $translator;
        $this->twig = $twig;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param Request $request
     * @param array $criteria
     * @param array $orderBy
     * @param int $perPage
     * @return Response
     */
    protected function listEntities(Request $request, array $criteria = array(), array $orderBy = array(), $perPage = 10)
    {
        $entity = new $this->entityClass;

        if($entity instanceof UserReferenceInterface && !array_key_exists('user', $criteria)) {
            $criteria['user'] = $this->getUser()->getId();
        }

        /** @var EntityRepository $repo */
        $repo = $this->getRepositoryForClass($this->entityClass);

        $qb = $repo->createQueryBuilder('e');
        foreach($criteria as $field => $value) {
            $qb->andWhere("e.{$field} = :{$field}");
            $qb->setParameter($field, $value);
        }
        foreach($orderBy as $field => $direction) {
            $qb->addOrderBy("e.{$field}", $direction);
        }

        $entities = $this->paginator->paginate($qb, $request->query->get('page', 1), $perPage);

        return $this->render($this->listTemplate, array(
            'entities' => $entities,
            'listroute' => $this->listRoute,
            'editroute' => $this->editRoute,
            'showroute' => $this->showRoute,
            'deleteroute' => $this->deleteRoute,
            'transprefix' => $this->transPrefix,
        ));
    }

    /**
     * @param Request $request
     * @param $id
     * @return Response|RedirectResponse
     * @throws NotFoundHttpException
     */
    protected function editEntity(Request $request, $id)
    {
        if (!is_null($id)) {
            $entity = $this->getRepositoryForClass($this->entityClass)->find($id);
            if (is_null($entity)) {
                throw new NotFoundHttpException("entity with id {$id} not found!");
            }
            if(!$this->security->isGranted('ROLE_ADMIN') &&
                $entity->getUser()->getId() !== $this->getUser()->getId()) {
                throw new AccessDeniedException("permission denied to edit entity with {$id}");
            }
        } else {
            $entity = new $this->entityClass;
            if($entity instanceof UserReferenceInterface) {
                $entity->setUser($this->getUser());
            }
        }

        $formType = new $this->formTypeClass;
        if(method_exists($formType, 'setUser')) {
            $formType->setUser($this->getUser());
        }
        if(method_exists($formType, 'setTranslator')) {
            $formType->setTranslator($this->translator);
        }

        $form = $this->createForm($formType, $entity);

        if ('POST' == $request->getMethod()) {
            $form->submit($request);
            if ($form->isValid()) {
                $em = $this->getManagerForClass($this->entityClass);

                $em->persist($entity);
                $em->flush();

                if($request->request->get('saveandclose', false)) {
                    return new RedirectResponse($this->urlGenerator->generate($this->listRoute, array(), true), 302);
                }

                if($request->request->get('saveandnew', false)) {
                    return new RedirectResponse($this->urlGenerator->generate($this->editRoute, array(), true), 302);
                }

                return new RedirectResponse($this->urlGenerator->generate($this->editRoute, array('id' => $entity->getId()), true), 302);
            }
        }

        return $this->render($this->editTemplate, array(
            'entity' => $entity,
            'form' => $form->createView(),
            'listroute' => $this->listRoute,
            'editroute' => $this->editRoute,
            'showroute' => $this->showRoute,
            'deleteroute' => $this->deleteRoute,
            'transprefix' => $this->transPrefix,
        ));
    }

    /**
     * @param $id
     * @return Response
     */
    protected function showEntity($id)
    {
        $entity = $this->getRepositoryForClass($this->entityClass)->find($id);

        if (is_null($entity)) {
            throw new NotFoundHttpException("entity with id {$id} not found!");
        }

        if(!$this->security->isGranted('ROLE_ADMIN') &&
            $entity->getUser()->getId() !== $this->getUser()->getId()) {
            throw new AccessDeniedException("permission denied to show entity with {$id}");
        }

        return $this->render($this->showTemplate, array(
            'entity' => $entity,
            'listroute' => $this->listRoute,
            'editroute' => $this->editRoute,
            'showroute' => $this->showRoute,
            'deleteroute' => $this->deleteRoute,
            'transprefix' => $this->transPrefix,
        ));
    }

    /**
     * @param $id
     * @return RedirectResponse
     * @throws NotFoundHttpException
     */
    protected function deleteEntity($id)
    {
        $entity = $this->getRepositoryForClass($this->entityClass)->find($id);

        if (is_null($entity)) {
            throw new NotFoundHttpException("entity with id {$id} not found!");
        }

        if(!$this->security->isGranted('ROLE_ADMIN') &&
            $entity->getUser()->getId() !== $this->getUser()->getId()) {
            throw new AccessDeniedException("permission denied to delete entity with {$id}");
        }

        $em = $this->getManagerForClass($this->entityClass);

        $em->remove($entity);
        $em->flush();

        return new RedirectResponse($this->urlGenerator->generate($this->listRoute), 302);
    }
}
