<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use AppBundle\Entity\Bid;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Product controller.
 *
 * @Route("product")
 */
class ProductController extends Controller
{

    /**
     * Lists all product entities.
     *
     * @Route("/", name="product_index")
     * @Method("GET")
     */
    public function indexAction()
    {

        $em = $this->getDoctrine()->getManager();

        $products = $em->getRepository('AppBundle:Product')->findAll();

        return $this->render('product/index.html.twig', array(
            'products' => $products,
        ));
    }

    /**
     * Lists users' all product entities.
     *
     * @Route("/user/{id}", name="user_products_index")
     * @Method("GET")
     */
    public function indexUserAction(Request $request)
    {
        $id = $request->attributes->get('id');

        $em = $this->getDoctrine()->getManager();

        $products = $em->getRepository('AppBundle:Product')->findBy(
            array('user' => $id));

        $user = $em->getRepository('AppBundle:User')->findBy(
            array('id' => $id));

        if ($this->getUser()->getId() != $id) {
            $user[0]->setNbVisit($user[0]->getNbVisit() +1);
            $em->flush();
        }

        return $this->render('product/index_user_products.html.twig', array(
            'products' => $products,
            'user' => $user[0],
        ));
    }

    /**
     * Creates a new product entity.
     *
     * @Route("/new", name="product_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $product = new Product();
        $form = $this->createForm('AppBundle\Form\ProductType', $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product->setUser($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('product_show', array('id' => $product->getId()));
        }

        return $this->render('product/new.html.twig', array(
            'product' => $product,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a product entity.
     *
     * @Route("/{id}", name="product_show")
     * @Method({"GET", "POST"})
     */
    public function showAction(Request $request, Product $product)
    {
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository('AppBundle:Product')->find($product->getId());

        if ($this->getUser() != $product->getUser()) {
            $product->setNbVisit($product->getNbVisit() +1);
            $em->flush();
        }

        $bid = new Bid();

        $deleteForm = $this->createDeleteForm($product);

        $bidForm = $this->createFormBuilder($product)
        ->add('biddingPrice')
            ->getForm();

        $autoBidForm = $this->createFormBuilder($bid)
            ->add('maxBid')
            ->getForm();

        $autoBidForm->handleRequest($request);


        $bidForm->handleRequest($request);

        if ($bidForm->isSubmitted() && $bidForm->isValid()) {
            $biddingPrice = $bidForm->getData()->getBiddingPrice();
            if ($biddingPrice < $product->getImmediatePrice() || $product->getImmediatePrice() == null) {
                $product->setBuyer($this->getUser());
                $product->setPrice($biddingPrice);
                $em->flush();
                return $this->redirectToRoute('product_show', array('id' => $product->getId()));
            } else {
                return $this->redirectToRoute('immediate_purchase', array('id' => $product->getId()));
            }
        }

        if ($autoBidForm->isSubmitted() && $autoBidForm->isValid()) {
            $autoBidPrice = $autoBidForm->getData()->getMaxBid();
            if ($autoBidPrice > $product->getPrice() && $autoBidPrice < $product->getImmediatePrice()) {
                $bid->setUser($this->getUser());
                $bid->setProduct($product);
                $em->persist($bid);
                $em->flush();
            } else {
                $this->addFlash(
                    'notice',
                    'L\'enchère auto doit être supérieure au prix du produit et inférieure au prix immédiat '
                );
            }
        }

            return $this->render('product/show.html.twig', array(
            'product' => $product,
            'delete_form' => $deleteForm->createView(),
            'bidForm' => $bidForm->createView(),
            'autoBidForm' => $autoBidForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing product entity.
     *
     * @Route("/{id}/edit", name="product_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Product $product)
    {
        $deleteForm = $this->createDeleteForm($product);
        $editForm = $this->createForm('AppBundle\Form\ProductType', $product);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('product_edit', array('id' => $product->getId()));
        }

        return $this->render('product/edit.html.twig', array(
            'product' => $product,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a product entity.
     *
     * @Route("/{id}", name="product_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Product $product)
    {
        $form = $this->createDeleteForm($product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($product);
            $em->flush();
        }

        return $this->redirectToRoute('product_index');
    }

    /**
     * Creates a form to delete a product entity.
     *
     * @param Product $product The product entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Product $product)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('product_delete', array('id' => $product->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }


}
