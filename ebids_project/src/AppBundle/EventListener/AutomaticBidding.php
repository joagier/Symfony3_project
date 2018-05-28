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
use AppBundle\Entity\Bid;

use Symfony\Component\HttpFoundation\RedirectResponse;


class AutomaticBidding implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return array(
            'prePersist',
        );
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $array = [];
        $entity = $args->getObject();

        if (!$entity instanceof Bid) {
            return;
        }

        $product = $entity->getProduct();

        $em = $args->getObjectManager();

        $bids = $em->getRepository('AppBundle:Bid')->findby(
            array('product' => $entity->getProduct())
        );


        if ($bids != null) {
            foreach ($bids as $bid) {
                if ($bid->getMaxBid() > $bid->getProduct()->getPrice())
                $array[] = $bid->getMaxBid();
            }
            $array[] = $entity->getMaxBid();

            if (sizeof($array) > 1) {
                rsort($array);
                $product->setPrice($array[1]);
            }
        }

        $product->setPrice($product->getPrice() + 1);
        $product->setBuyer($entity->getUser());
        $product->setBiddingPrice($product->getPrice());
    }
}