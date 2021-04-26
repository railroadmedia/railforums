<?php

namespace Railroad\Railforums\Entities;

use Railroad\Doctrine\Contracts\UserEntityInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="users")
 */
class User
{
    /**
     * @ORM\Id @ORM\GeneratedValue @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $displayName;

    /**
     * @var string
     */
    private $profilePictureUrl;


    private $createdAt;

    /**
     * User constructor.
     *
     * @param int $id
     * @param $displayName
     * @param $profilePictureUrl
     * @param $createdAt
     */
    public function __construct(
        int $id,
        $displayName,
        $profilePictureUrl,
        $createdAt
    ) {
        $this->id = $id;
        $this->displayName = $displayName;
        $this->profilePictureUrl = $profilePictureUrl;
        $this->createdAt = $createdAt;
    }

    /**
     * @return int
     */
    public function getId()
    : int
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    : void {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getDisplayName()
    : string
    {
        return $this->displayName;
    }

    /**
     * @param $displayName
     */
    public function setDisplayName($displayName)
    : void {
        $this->displayName = $displayName;
    }

    /**
     * @return string
     */
    public function getProfilePictureUrl()
    {
        return $this->profilePictureUrl;
    }

    /**
     * @param $profilePictureUrl
     */
    public function setProfilePictureUrl($profilePictureUrl)
    : void {
        $this->profilePictureUrl = $profilePictureUrl;
    }

    /**
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param $createdAt
     */
    public function setCreatedAt($createdAt)
    : void {
        $this->createdAt = $createdAt;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        /*
        method needed by UnitOfWork
        https://www.doctrine-project.org/projects/doctrine-orm/en/2.6/cookbook/custom-mapping-types.html
        */
        return (string)$this->getId();
    }
}