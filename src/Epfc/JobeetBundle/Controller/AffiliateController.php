<?php

namespace Epfc\JobeetBundle\Controller;

use Epfc\JobeetBundle\Entity\Affiliate;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Affiliate controller.
 *
 * @Route("affiliate")
 */
class AffiliateController extends Controller
{
    /**
     * Lists all affiliate entities.
     *
     * @Route("/", name="affiliate_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $affiliates = $em->getRepository('JobeetBundle:Affiliate')->findAll();

        return $this->render('affiliate/index.html.twig', array(
            'affiliates' => $affiliates,
        ));
    }

    /**
     * Creates a new affiliate entity.
     *
     * @Route("/new", name="affiliate_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $affiliate = new Affiliate();
        $form = $this->createForm('Epfc\JobeetBundle\Form\AffiliateType', $affiliate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($affiliate);
            $em->flush($affiliate);

            return $this->redirectToRoute('affiliate_show', array('id' => $affiliate->getId()));
        }

        return $this->render('affiliate/new.html.twig', array(
            'affiliate' => $affiliate,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a affiliate entity.
     *
     * @Route("/{id}", name="affiliate_show")
     * @Method("GET")
     */
    public function showAction(Affiliate $affiliate)
    {
        $deleteForm = $this->createDeleteForm($affiliate);

        return $this->render('affiliate/show.html.twig', array(
            'affiliate' => $affiliate,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing affiliate entity.
     *
     * @Route("/{id}/edit", name="affiliate_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Affiliate $affiliate)
    {
        $deleteForm = $this->createDeleteForm($affiliate);
        $editForm = $this->createForm('Epfc\JobeetBundle\Form\AffiliateType', $affiliate);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('affiliate_edit', array('id' => $affiliate->getId()));
        }

        return $this->render('affiliate/edit.html.twig', array(
            'affiliate' => $affiliate,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a affiliate entity.
     *
     * @Route("/{id}", name="affiliate_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Affiliate $affiliate)
    {
        $form = $this->createDeleteForm($affiliate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($affiliate);
            $em->flush();
        }

        return $this->redirectToRoute('affiliate_index');
    }

    /**
     * Creates a form to delete a affiliate entity.
     *
     * @param Affiliate $affiliate The affiliate entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Affiliate $affiliate)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('affiliate_delete', array('id' => $affiliate->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
