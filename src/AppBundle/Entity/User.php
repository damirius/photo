<?php
// src/AppBundle/Entity/User.php

namespace AppBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 * @Gedmo\Uploadable(
 * path="../app/Resources/uploads/avatar",
 * allowOverwrite=false,
 * appendNumber=true,
 * allowedTypes="image/jpeg,image/pjpeg,image/png,image/x-png,image/gif",
 * filenameGenerator="ALPHANUMERIC")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(nullable=true)
     * @Gedmo\UploadableFilePath
     */
    private $avatar;

    /**
     * @ORM\Column(nullable=true)
     * @Gedmo\UploadableFileName
     */
    private $avatarName;

    /**
     * @var Comment[]
     *
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="user");
     */
    private $comments;

    public function __construct()
    {
        parent::__construct();

        $this->comments = new ArrayCollection();
    }

    /**
     * Set avatar
     *
     * @param string $avatar
     *
     * @return User
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * Get avatar
     *
     * @return string
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * Add comment
     *
     * @param \AppBundle\Entity\Comment $comment
     *
     * @return User
     */
    public function addComment(\AppBundle\Entity\Comment $comment)
    {
        $this->comments[] = $comment;

        return $this;
    }

    /**
     * Remove comment
     *
     * @param \AppBundle\Entity\Comment $comment
     */
    public function removeComment(\AppBundle\Entity\Comment $comment)
    {
        $this->comments->removeElement($comment);
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Set avatarName
     *
     * @param string $avatarName
     *
     * @return User
     */
    public function setAvatarName($avatarName)
    {
        $this->avatarName = $avatarName;

        return $this;
    }

    /**
     * Get avatarName
     *
     * @return string
     */
    public function getAvatarName()
    {
        return $this->avatarName;
    }
}
