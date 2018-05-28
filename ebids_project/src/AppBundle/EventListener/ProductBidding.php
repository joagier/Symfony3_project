<?php
/**
 * Created by PhpStorm.
 * User: jordane
 * Date: 28/05/18
 * Time: 10:57
 */

namespace AppBundle\EventListener;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use AppBundle\Entity\Product;
use Doctrine\ORM\Event\PreUpdateEventArgs;

use Symfony\Component\HttpFoundation\RedirectResponse;


class ProductBidding implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return array(
            'preUpdate',
        );
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $array = [];
        $entity = $args->getObject();

        if (!$entity instanceof Product) {
            return;
        }

        if ($entity->getOldBidding() < $entity->getBiddingPrice()) {

            $em = $args->getObjectManager();

            $bids = $em->getRepository('AppBundle:Bid')->findby(
                array('product' => $entity)
            );

            if ($bids != null) {
                foreach ($bids as $bid) {
                    if ($bid->getMaxBid() > $bid->getProduct()->getPrice())
                        $array[] = $bid->getMaxBid();
                }

                if (sizeof($array) > 1) {
                    rsort($array);
                    $entity->setPrice($array[1] +1);
                    $entity->setBiddingPrice($entity->getPrice());
                    $findUser = $em->getRepository('AppBundle:Bid')->findby(
                        array('maxBid' => $array[1])
                    );
                    $entity->setBuyer($findUser[0]->getUser());
                } elseif (sizeof($array) == 1) {
                    $entity->setPrice($entity->getPrice() +1);
                    $entity->setBiddingPrice($entity->getPrice());
                    $findUser = $em->getRepository('AppBundle:Bid')->findby(
                        array('maxBid' => $array[0])
                    );
                    $entity->setBuyer($findUser[0]->getUser());
                }

                $md = $em->getClassMetadata(Product::class);
                $em->getUnitOfWork()->recomputeSingleEntityChangeSet($md, $entity);
            }

        }


    }
}