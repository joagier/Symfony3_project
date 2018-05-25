<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

class PurchaseController extends Controller
{

    /**
     * Set the buyer for an immediate purchase.
     *
     * @Route("/purchase/{id}", name="immediate_purchase")
     * @Method({"GET"})
     */
    public function immediateBuy(Request $request)
    {
        $productId = $request->attributes->get('id');
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository('AppBundle:Product')->find($productId);
        $product->setBuyer($this->getUser());
        $product->setPrice($product->getImmediatePrice());
        $product->setEndDate(null);
        $em->persist($product);
        $em->flush();
        return $this->redirectToRoute('user_purchase');
    }


    /**
     * Lists users' purchases.
     *
     * @Route("/purchase", name="user_purchase")
     * @Method("GET")
     */
    public function userPurchases()
    {
        $id = $this->getUser()->getId();

        $em = $this->getDoctrine()->getManager();

        $products = $em->getRepository('AppBundle:Product')->findBy(
            array('buyer' => $id));

        return $this->render('purchase/my_purchases.html.twig', array(
            'products' => $products,
        ));
    }
}
