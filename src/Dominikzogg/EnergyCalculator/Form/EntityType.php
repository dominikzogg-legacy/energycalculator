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
use Symfony\Component\Form\Exception\UnexpectedTypeException;

class EntityType extends DoctrineType
{
    /**
     * @var array
     */
    private $loaderCache = array();

    /**
     * Return the default loader object.
     *
     * @param ObjectManager $manager
     * @param mixed         $queryBuilder
     * @param string        $class
     *
     * @return ORMQueryBuilderLoader
     *
     * @throws UnexpectedTypeException
     */
    public function getLoader(ObjectManager $manager, $queryBuilder, $class)
    {
        if (!($queryBuilder instanceof QueryBuilder || $queryBuilder instanceof \Closure)) {
            throw new UnexpectedTypeException($queryBuilder, 'Doctrine\ORM\QueryBuilder or \Closure');
        }

        if ($queryBuilder instanceof \Closure) {
            $reflection = new \ReflectionFunction($queryBuilder);
            $queryBuilderHashParts = array(
                'export' => (string) $reflection,
                'this' => spl_object_hash($reflection->getClosureThis()),
            );
            $queryBuilderHash = hash('sha256', json_encode($queryBuilderHashParts));
        } else {
            $queryBuilderHash = spl_object_hash($queryBuilder);
        }

        $loaderHash = hash('sha256', json_encode(array(
            'manager' => spl_object_hash($manager),
            'queryBuilder' => $queryBuilderHash,
            'class' => $class,
        )));

        if (!isset($this->loaderCache[$loaderHash])) {
            $this->loaderCache[$loaderHash] = new ORMQueryBuilderLoader(
                $queryBuilder,
                $manager,
                $class
            );
        }

        return $this->loaderCache[$loaderHash];
    }

    public function getName()
    {
        return 'entity';
    }
}
