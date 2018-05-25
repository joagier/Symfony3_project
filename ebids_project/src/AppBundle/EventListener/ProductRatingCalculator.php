<?php
/**
 * Created by PhpStorm.
 * User: jordane
 * Date: 23/05/18
 * Time: 10:57
 */

namespace AppBundle\EventListener;

use AppBundle\Entity\Product;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use AppBundle\Entity\ProductRating;
use Doctrine\Common\Util\Debug;
use Doctrine\ORM\Event\PreUpdateEventArgs;


class ProductRatingCalculator implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return array(
            'prePersist',
            'preUpdate',
            'preRemove'
        );
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {

        $entity = $args->getObject();
        $em = $args->getEntityManager();


        if (!$entity instanceof ProductRating) {
            return;
        }
        $oldValue = $entity->getOldRate();
        $newValue = $entity->getRate();
        $product = $entity->getProduct();

        $product->setAverageRate((($product->getAverageRate()*$product->getNbRate()) - $oldValue + $newValue) / ($product->getNbRate()));

        $md = $em->getClassMetadata(Product::class);
        $em->getUnitOfWork()->recomputeSingleEntityChangeSet($md, $product);
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if (!$entity instanceof ProductRating) {
            return;
        }
        $product = $entity->getProduct();
        $product->setAverageRate((($product->getAverageRate()*$product->getNbRate()) + $entity->getRate()) / ($product->getNbRate()+1));
        $product->setNbRate($product->getNbRate()+1);
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if (!$entity instanceof ProductRating) {
            return;
        }
        $product = $entity->getProduct();
        if ($product->getNbRate() > 1) {
            $product->setAverageRate((($product->getAverageRate()*$product->getNbRate()) - $entity->getRate()) / ($product->getNbRate()-1));
        } else {
            $product->setAverageRate(null);
        }
        $product->setNbRate($product->getNbRate()-1);
    }

    /*public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        // only act on some "Product" entity
        if (!$entity instanceof ProductRating) {
            return;
        }
        $entityManager = $args->getEntityManager();
        $product = $entity->getProduct();
        $product->setAverageRate((($product->getAverageRate()*$product->getNbRate()) + $entity->getRate()) / ($product->getNbRate()+1));
        $product->setNbRate($product->getNbRate()+1);

        // ... do something with the Product
    }*/
}