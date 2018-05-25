<?php
/**
 * Created by PhpStorm.
 * User: jordane
 * Date: 23/05/18
 * Time: 10:57
 */

namespace AppBundle\EventListener;

use AppBundle\Entity\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use AppBundle\Entity\UserRating;
use Doctrine\ORM\Event\PreUpdateEventArgs;


class UserRatingCalculator implements EventSubscriber
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

        if (!$entity instanceof UserRating) {
            return;
        }
        $oldValue = $entity->getOldRate();
        $newValue = $entity->getRate();
        $user = $entity->getUser();

        $user->setAverageRate((($user->getAverageRate()*$user->getNbRate()) - $oldValue + $newValue) / ($user->getNbRate()));
        $md = $em->getClassMetadata(User::class);
        $em->getUnitOfWork()->recomputeSingleEntityChangeSet($md, $user);
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof UserRating) {
            return;
        }
        $user = $entity->getUser();
        if ($user->getNbRate() > 0) {
            $user->setAverageRate((($user->getAverageRate()*$user->getNbRate()) + $entity->getRate()) / ($user->getNbRate()+1));
        } else {
            $user->setAverageRate($entity->getRate());

        }
        $user->setNbRate($user->getNbRate()+1);
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof UserRating) {
            return;
        }
        $user = $entity->getUser();
        if ($user->getNbRate() > 1) {
            $user->setAverageRate((($user->getAverageRate()*$user->getNbRate()) - $entity->getRate()) / ($user->getNbRate()-1));
        } else {
            $user->setAverageRate(null);
        }
        $user->setNbRate($user->getNbRate()-1);
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