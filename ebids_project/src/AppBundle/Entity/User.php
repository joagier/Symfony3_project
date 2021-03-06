<?php
/**
 * Created by PhpStorm.
 * User: jordane
 * Date: 17/05/18
 * Time: 15:25
 */

namespace AppBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
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
     *
     * @var string
     *
     * @ORM\Column(name = "address", type="text", nullable=true)
     */
    protected $address;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Product", mappedBy="user", cascade={"remove"})
     */
    private $products;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Product", mappedBy="buyer")
     * @ORM\JoinColumn(nullable=true)
     */
    private $productsBought;

    /**
     * @var int
     *
     * @ORM\Column(name="nb_visit", type="integer", nullable=true)
     */
    private $nbVisit;


    public function __construct()
    {
        parent::__construct();

    }

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\UserRating", mappedBy="user", cascade={"remove"})
     */
    private $userRatings;

    /**
     * @var float
     * @ORM\Column(name="average_rate", type="float", nullable=true)
     */
    private $averageRate;

    /**
     * @var integer
     * @ORM\Column(name="nb_rate", type="integer", nullable=true)
     */
    private $nbRate;

    /**
     * Add product
     *
     * @param \AppBundle\Entity\Product $product
     *
     * @return User
     */
    public function addProduct(\AppBundle\Entity\Product $product)
    {
        $product->setUser($this);
        $this->products[] = $product;

        return $this;
    }

    /**
     * Remove product
     *
     * @param \AppBundle\Entity\Product $product
     */
    public function removeProduct(\AppBundle\Entity\Product $product)
    {
        $product->setUser(null);
        $this->products->removeElement($product);
    }

    /**
     * Get products
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProducts()
    {
        return $this->products;
    }

    public function __toString()
    {
        return $this->getUsername();
    }


    /**
     * Add productsBought
     *
     * @param \AppBundle\Entity\Product $productsBought
     *
     * @return User
     */
    public function addProductsBought(\AppBundle\Entity\Product $productsBought)
    {
        $productsBought->setBuyer($this);
        $this->productsBought[] = $productsBought;

        return $this;
    }

    /**
     * Remove productsBought
     *
     * @param \AppBundle\Entity\Product $productsBought
     */
    public function removeProductsBought(\AppBundle\Entity\Product $productsBought)
    {
        $productsBought->setBuyer(null);
        $this->productsBought->removeElement($productsBought);
    }

    /**
     * Get productsBought
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProductsBought()
    {
        return $this->productsBought;
    }

    /**
     * Set averageRate
     *
     * @param float $averageRate
     *
     * @return User
     */
    public function setAverageRate($averageRate)
    {
        $this->averageRate = $averageRate;

        return $this;
    }

    /**
     * Get averageRate
     *
     * @return float
     */
    public function getAverageRate()
    {
        return $this->averageRate;
    }

    /**
     * Set nbRate
     *
     * @param integer $nbRate
     *
     * @return User
     */
    public function setNbRate($nbRate)
    {
        $this->nbRate = $nbRate;

        return $this;
    }

    /**
     * Get nbRate
     *
     * @return integer
     */
    public function getNbRate()
    {
        return $this->nbRate;
    }

    /**
     * Add userRating
     *
     * @param \AppBundle\Entity\UserRating $userRating
     *
     * @return User
     */
    public function addUserRating(\AppBundle\Entity\UserRating $userRating)
    {
        $this->userRatings[] = $userRating;

        return $this;
    }

    /**
     * Remove userRating
     *
     * @param \AppBundle\Entity\UserRating $userRating
     */
    public function removeUserRating(\AppBundle\Entity\UserRating $userRating)
    {
        $this->userRatings->removeElement($userRating);
    }

    /**
     * Get userRatings
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUserRatings()
    {
        return $this->userRatings;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return User
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set nbVisit
     *
     * @param integer $nbVisit
     *
     * @return User
     */
    public function setNbVisit($nbVisit)
    {
        $this->nbVisit = $nbVisit;

        return $this;
    }

    /**
     * Get nbVisit
     *
     * @return integer
     */
    public function getNbVisit()
    {
        return $this->nbVisit;
    }
}
