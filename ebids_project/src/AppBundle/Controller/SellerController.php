<?php
/**
 * Created by PhpStorm.
 * User: jordane
 * Date: 28/05/18
 * Time: 16:21
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class SellerController extends Controller

{
    /**
     * Lists all product entities.
     *
     * @Route("/sellerspace", name="seller_index")
     * @Method("GET")
     */
    public function indexAction()
    {

        $em = $this->getDoctrine()->getManager();

        $products = $em->getRepository('AppBundle:Product')->findBy(
            array('user' => $this->getUser())
        );

        return $this->render('seller/seller_space.html.twig', array(
            'products' => $products,
        ));
    }
}