<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use AppBundle\Entity\User;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class HomeController extends Controller
{
    /**
     * Lists all product entities.
     *
     * @Route("/", name="homepage")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        if ($this->isGranted('ROLE_USER') && $this->getUser() instanceof User) {

            $products = $em->getRepository('AppBundle:Product')->findBy(
                array('user' => $this->getUser()));

        } else {

            $products = $em->getRepository('AppBundle:Product')->findAll();


        }

        return $this->render('home/home.html.twig', array(
            'products' => $products,
        ));
    }
}
