<?php

namespace Dominikzogg\EnergyCalculator\Voter;

use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class RelatedObjectVoter implements VoterInterface
{
    /**
     * @var RoleHierarchyInterface
     */
    protected $roleHierarchy;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param RoleHierarchyInterface $roleHierarchy
     * @param LoggerInterface        $logger
     */
    public function __construct(RoleHierarchyInterface $roleHierarchy, LoggerInterface $logger)
    {
        $this->roleHierarchy = $roleHierarchy;
        $this->logger = $logger;
    }

    /**
     * Checks if the voter supports the given attribute.
     *
     * @param string $attribute An attribute
     *
     * @return Boolean true if this Voter supports the attribute, false otherwise
     */
    public function supportsAttribute($attribute)
    {
        return true;
    }

    /**
     * @param string $class
     * @return bool
     */
    public function supportsClass($class)
    {
        try {
            $reflection = new \ReflectionClass($class);
        } catch (\Exception $e) {
            return false;
        }

        if($reflection->implementsInterface('Dominikzogg\EnergyCalculator\Voter\RelatedObjectInterface')) {
            return true;
        }

        return false;
    }

    /**
     * @param \Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token
     * @param null|object $object
     * @param array $attributes
     * @return int
     */
    public function vote(TokenInterface $token, $object, array $attributes)
    {
        $voterName = $this->getName();

        if (!is_object($object)) {
            $this->logger->debug(sprintf('RelatedObjectVoter %s not received an object. Voting to abstain.', $voterName));

            return self::ACCESS_ABSTAIN;
        }

        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            $this->logger->debug(sprintf('RelatedObjectVoter %s not received an valid user object. Voting to abstain.', $voterName));

            return self::ACCESS_ABSTAIN;
        }

        if (!$this->supportsClass($object)) {
            $objectString = is_object($object) ? get_class($object) : gettype($object);
            $this->logger->debug(sprintf('RelatedObjectVoter %s does not support class %s. Voting to abstain.', $voterName, $objectString));

            return self::ACCESS_ABSTAIN;
        }

        $userRoles = $this->getUserRoles($token);
        foreach ($this->getNeededRoles($attributes, $object) as $neededRole) {
            if (!in_array($neededRole, $userRoles)) {
                $this->logger->debug(sprintf('Needed Role "%s" not found on user. Voting to abstain.', $neededRole));

                return self::ACCESS_ABSTAIN;
            }
        }

        if (true === $this->isRelatedObject($user, $object)) {
            $this->logger->debug(sprintf('Object is RelatedObject (%s). Voting to grant access.', $voterName));

            return self::ACCESS_GRANTED;
        }

        $this->logger->debug(sprintf('Object is not RelatedObject (%s). Voting to abstain.', $voterName));

        return self::ACCESS_ABSTAIN;
    }

    /**
     * @param  TokenInterface $token
     * @return array
     */
    protected function getUserRoles(TokenInterface $token)
    {
        $roles = array();

        foreach ($this->roleHierarchy->getReachableRoles($token->getRoles()) as $role) {
            $roles[] = $role->getRole();
        }

        return array_unique($roles);
    }

    /**
     * @param array $attributes
     * @param RelatedObjectInterface $object
     * @return array
     */
    protected function getNeededRoles(array $attributes, RelatedObjectInterface $object)
    {
        $roles = array();
        $prefix = $this->getNeededRolesPrefix($object);
        foreach ($attributes as $attribute) {
            $roles[] = $prefix . $attribute;
        }

        return $roles;
    }

    /**
     * @param RelatedObjectInterface $object
     * @return string
     */
    protected function getNeededRolesPrefix(RelatedObjectInterface $object)
    {
        return 'RELATED_' . strtoupper($object->getRoleNamePart()) . '_';
    }

    /**
     * @param RelatedObjectInterface $user
     * @param RelatedObjectInterface $object
     * @return bool
     */
    protected function isRelatedObject(RelatedObjectInterface $user, RelatedObjectInterface $object)
    {
        foreach($user->getSecurityRelatedObjects() as $usro) {
            foreach($object->getSecurityRelatedObjects() as $osro) {
                if($usro === $osro) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @return string
     */
    protected function getName()
    {
        $explode = explode("\\", get_class($this));
        return substr(end($explode), 0, -5);
    }
}
