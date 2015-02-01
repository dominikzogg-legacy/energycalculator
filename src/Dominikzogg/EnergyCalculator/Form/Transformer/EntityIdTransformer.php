<?php

namespace Dominikzogg\EnergyCalculator\Form\Transformer;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;
use Symfony\Component\PropertyAccess\PropertyAccess;

class EntityIdTransformer implements DataTransformerInterface
{
    protected $objectManager;
    protected $class;
    protected $identifier;
    protected $property;
    protected $multiple;
    protected $propertyAccessor;

    /**
     * @param ObjectManager $objectManager
     * @param string $class
     * @param string $property
     * @param bool $multiple
     */
    public function __construct(ObjectManager $objectManager, $class, $property, $multiple)
    {
        $this->objectManager = $objectManager;
        $this->class = $class;
        $this->identifier = $objectManager->getClassMetadata($class)->getIdentifier()[0];
        $this->property = $property;
        $this->multiple = $multiple;
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    /**
     * @param array|null|object $entities
     * @return string
     */
    public function transform($entities)
    {
        if (null === $entities) {
            return '';
        }

        if(!$this->multiple) {
            return $this->transformOne($entities);
        }

        if (!is_array($entities)) {
            $entities = iterator_to_array($entities);
        }

        $entities = array_map(
            function ($entity) {
                return $this->transformOne($entity);
            }, $entities
        );

        return implode(",", $entities);
    }

    /**
     * @param object $entity
     * @return string
     */
    protected function transformOne($entity)
    {
        return $this->propertyAccessor->getValue($entity, $this->identifier) . ':' . $this->propertyAccessor->getValue($entity, $this->property);
    }

    /**
     * @param string $text
     * @return array|null|object
     */
    public function reverseTransform($text)
    {

        if (!$text || !is_scalar($text)) {
            return null;
        }

        if(!$this->multiple) {
            return $this->reverseTransformOne($text);
        }

        $entities = array();
        foreach (explode(',', $text) as $part) {
            $entities[] = $this->reverseTransformOne($part);
        }

        return $entities;
    }

    /**
     * @param string $text
     * @return null|object
     * @throws TransformationFailedException
     */
    protected function reverseTransformOne($text)
    {
        $data = explode(":", $text);
        if (isset($data[1])) {
            throw new TransformationFailedException();
        }
        $id = $data[0];

        return $this->objectManager->find($this->class, $id);
    }
}