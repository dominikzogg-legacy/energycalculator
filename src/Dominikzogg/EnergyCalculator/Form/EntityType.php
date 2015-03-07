<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dominikzogg\EnergyCalculator\Form;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\ChoiceList\ORMQueryBuilderLoader;
use Symfony\Bridge\Doctrine\Form\Type\DoctrineType;

class EntityType extends DoctrineType
{
    /**
     * @var ORMQueryBuilderLoader[]
     */
    private $loaderCache;

    /**
     * Return the default loader object.
     *
     * @param ObjectManager $manager
     * @param mixed         $queryBuilder
     * @param string        $class
     *
     * @return ORMQueryBuilderLoader
     */
    public function getLoader(ObjectManager $manager, $queryBuilder, $class)
    {
        $queryBuilderHash = null;

        if($queryBuilder instanceof QueryBuilder) {
            $queryBuilderHash = $this->getQueryBuilderHash($queryBuilder);
        }

        if(null === $queryBuilderHash) {
            return new ORMQueryBuilderLoader(
                $queryBuilder,
                $manager,
                $class
            );
        }

        $loaderHash = $this->getLoaderHash($manager, $queryBuilderHash, $class);

        if(!isset($this->loaderCache[$loaderHash])) {
            $this->loaderCache[$loaderHash] = new ORMQueryBuilderLoader(
                $queryBuilder,
                $manager,
                $class
            );
        }

        return $this->loaderCache[$loaderHash];
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @return string
     */
    protected function getQueryBuilderHash(QueryBuilder $queryBuilder)
    {
        return hash('sha256', json_encode(array(
             'sql' => $queryBuilder->getQuery()->getSQL(),
             'parameters' => $queryBuilder->getParameters(),
        )));
    }

    /**
     * @param ObjectManager $manager
     * @param string $queryBuilderHash
     * @param string $class
     * @return string
     */
    protected function getLoaderHash(ObjectManager $manager, $queryBuilderHash, $class)
    {
        return hash('sha256', json_encode(array(
            'manager' => spl_object_hash($manager),
            'queryBuilder' => $queryBuilderHash,
            'class' => $class,
        )));
    }

    public function getName()
    {
        return 'entity';
    }
}
