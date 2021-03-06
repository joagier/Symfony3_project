<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ProductRating
 *
 * @ORM\Table(name="product_rating")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProductRatingRepository")
 */
class ProductRating
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     * @Assert\Range(
     *     min = 0,
     *     max= 5
     * )
     *
     * @ORM\Column(name="rate", type="integer", nullable=false)

     */
    private $rate;

    private $oldRate;

    /**
     * @var Product
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Product", inversedBy="productRatings")
     */
    private $product;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="text", nullable=true)

     */
    private $comment;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", nullable=false)
     */
    private $username;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getOldRate()
    {
        return $this->oldRate;
    }

    /**
     * @param mixed $oldRate
     * @return ProductRating
     */
    public function setOldRate($oldRate)
    {
        $this->oldRate = $oldRate;
        return $this;
    }

    /**
     * Set rate
     *
     * @param integer $rate
     *
     * @return ProductRating
     */
    public function setRate($rate)
    {
        $this->oldRate = $this->rate;
        $this->rate = $rate;

        return $this;
    }

    /**
     * Get rate
     *
     * @return integer
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * Set product
     *
     * @param \AppBundle\Entity\Product $product
     *
     * @return ProductRating
     */
    public function setProduct(\AppBundle\Entity\Product $product = null)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return \AppBundle\Entity\Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return ProductRating
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return ProductRating
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }
}
