<?php
/**
 * Created by PhpStorm.
 * User: jordane
 * Date: 24/05/18
 * Time: 19:00
 */

namespace AppBundle\Controller;


use Doctrine\Common\Util\Debug;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class UserManagementController extends Controller
{
    /**
     * Lists all user entities.
     *
     * @Route("/admin/user", name="user_index")
     * @Method("GET")
     */
    public function indexAction()
    {

        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository('AppBundle:User')->findAll();

        return $this->render('admin/index.html.twig', array(
            'users' => $users,
        ));
    }


    /**
     * Finds and displays a user entity.
     *
     * @Route("/admin/user/{id}", name="user_show")
     * @Method("GET")
     */
    public function showAction(Request $request)
    {

        $userId = $request->attributes->get('id');

        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('AppBundle:User')->findBy(
            array('id' => $userId)
        );

        return $this->render('admin/show.html.twig', array(
            'user' => $user[0])
        );
    }

    /**
     * Displays a form to edit an existing user entity.
     *
     * @Route("/admin/useredit/{id}", name="user_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request)
    {
        $userId = $request->attributes->get('id');

        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('AppBundle:User')->findBy(
            array('id' => $userId)
        );

        $editForm = $this->createFormBuilder($user[0])
            ->add('username')->add('email')->add('address')
            ->add(
                'roles', ChoiceType::class, [
                    'choices' => ['ROLE_ADMIN' => 'ROLE_ADMIN', 'ROLE_USER' => 'ROLE_USER'],
                    'expanded' => true,
                    'multiple' => true,
                ]
            )
            ->getForm();

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_edit', array('id' => $user[0]->getId()));
        }

        return $this->render('admin/edit.html.twig', array(
            'user' => $user[0],
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Deletes a user entity.
     *
     * @Route("/admin/deleteuser/{id}", name="user_delete_admin")
     * @Method("GET")
     */
    public function deleteUserAdmin(Request $request)
    {
        $userId = $request->attributes->get('id');

        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('AppBundle:User')->findBy(
            array('id' => $userId)
        );
        $em->remove($user[0]);
        $em->flush();

        $this->addFlash(
            'notice',
            'Le compte a bien été supprimé ainsi que l\'ensemble des produits liés'
        );

        return $this->redirectToRoute('user_index');
    }



    /**
     * Deletes a user entity.
     *
     * @Route("/deleteuser", name="user_delete")
     * @Method("GET")
     */
    public function deleteUser(Request $request)
    {
        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();

        $this->addFlash(
            'notice',
            'Votre compte a bien été supprimé ainsi que l\'ensemble de vos produits'
        );

        return $this->redirectToRoute('homepage');
    }
}