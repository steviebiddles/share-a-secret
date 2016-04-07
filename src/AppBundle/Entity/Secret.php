<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use VMelnik\DoctrineEncryptBundle\Configuration\Encrypted;

/**
 * Secret
 *
 * @ORM\Table(name="secret")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SecretRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Secret
{
    /**
     * @ORM\Column(name="id", type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(min = 1)
     *
     * @Encrypted
     *
     * @ORM\Column(name="secret", type="string", length=255)
     */
    private $secret;

    /**
     * @var int
     *
     * @Assert\NotBlank()
     * @Assert\Range(min = 1, max = 5)
     *
     * @ORM\Column(name="views", type="integer")
     */
    private $views;

    /**
     * @var \DateTime
     *
     * @Assert\NotBlank()
     *
     * @ORM\Column(name="expires", type="datetime")
     */
    private $expires;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated", type="datetime")
     */
    private $updated;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return strtolower($this->id);
    }

    /**
     * Set secret
     *
     * @param string $secret
     *
     * @return Secret
     */
    public function setSecret($secret)
    {
        $this->secret = $secret;

        return $this;
    }

    /**
     * Get secret
     *
     * @return string
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * Set views
     *
     * @param integer $views
     *
     * @return Secret
     */
    public function setViews($views)
    {
        $this->views = $views;

        return $this;
    }

    /**
     * Get views
     *
     * @return integer
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * Set expires
     *
     * @param \DateTime $expires
     *
     * @return Secret
     */
    public function setExpires($expires)
    {
        $this->expires = $expires;

        return $this;
    }

    /**
     * Get expires
     *
     * @return \DateTime
     */
    public function getExpires()
    {
        return $this->expires;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Secret
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param \DateTime $updated
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }

    /**
     * Set created and updated at date time.
     *
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->setCreated(new \DateTime());
        $this->setUpdated(new \DateTime());
    }

    /**
     * Set updated at date time.
     *
     * @ORM\PreUpdate()
     */
    public function preUpdate()
    {
        $this->setUpdated(new \DateTime());
    }
}
