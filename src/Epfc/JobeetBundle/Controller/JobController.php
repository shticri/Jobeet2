<?php

namespace Epfc\JobeetBundle\Controller;

use Epfc\JobeetBundle\Entity\Job;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Job controller.
 *
 * @Route("/job")
 */
class JobController extends Controller
{
    /**
     * Lists all job entities.
     *
     * @Route("/", name="job_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $categories = $em->getRepository('JobeetBundle:Category')->getWithJobs();
 
        foreach($categories as $category)
        {
          $category->setActiveJobs($em->getRepository('JobeetBundle:Job')->getActiveJobs($category->getId(), $this->container->getParameter('max_jobs_on_homepage')));
          $category->setMoreJobs($em->getRepository('JobeetBundle:Job')->countActiveJobs($category->getId()), $this->container->getParameter('max_jobs_on_homepage'));
        }
        
        return $this->render('job/index.html.twig', array(
            'categories' => $categories,
        ));
 
    }

    /**
     * Displays a form to create a new Job entity
     *
     * @Route("/new", name="job_new")
     * @Method({"GET"})
     */
    public function newAction(Request $request)
    {
        $job = new Job();
        $form = $this->createForm('Epfc\JobeetBundle\Form\JobType', $job);
        
        return $this->render('job/new.html.twig', array(
            'job' => $job,
            'form' => $form->createView(),
        ));
    }

    /**
     * Creates a new job entity.
     *
     * @Route("/new", name="job_create")
     * @Method({"POST"})
     */
    public function createAction(Request $request)
    {
        $job  = new Job();
        $form = $this->createForm('Epfc\JobeetBundle\Form\JobType', $job);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            
            $em->persist($job);
            $em->flush();

            return $this->redirectToRoute('job_preview', array(
                'company' => $job->getCompanySlug(),
                'location' => $job->getLocationSlug(),
                'token' => $job->getToken(),
                'position' => $job->getPositionSlug()
            ));
        } 

        return $this->render('job/new.html.twig', array(
            'job' => $job,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a job entity.
     *
     * @Route("/{company}/{location}/{id}/{position}", name="job_show", requirements={"id": "\d+"})
     * @Method("GET")
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $job = $em->getRepository('JobeetBundle:Job')->getActiveJob($id);
        //dump($job);die;
         if (!$job) {
            throw $this->createNotFoundException('Unable to find Job entity.');
        }
        
        $deleteForm = $this->createDeleteForm($id);
        
        return $this->render('job/show.html.twig', array(
            'job' => $job,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a preview of a job entity.
     *
     * @Route("/{company}/{location}/{token}/{position}", name="job_preview", requirements={"token": "\w+"})
     * @Method("GET")
     */
    public function previewAction($token)
    {
        $em = $this->getDoctrine()->getManager();
        $job = $em->getRepository('JobeetBundle:Job')->findOneByToken($token);
        
        if (!$job) {
            throw $this->createNotFoundException('Unable to find Job entity.');
        }
  
        $deleteForm = $this->createDeleteForm($job->getId());
        $publishForm = $this->createPublishForm($job->getToken());
        $extendForm = $this->createExtendForm($job->getToken());
        
        return $this->render('job/show.html.twig', array(
            'job' => $job,
            'delete_form' => $deleteForm->createView(),
            'publish_form' => $publishForm->createView(),
            'extend_form' => $extendForm->createView()
        ));
    }
    
    /**
     * Displays a form to edit an existing Job entity
     *
     * @Route("/{token}/edit", name="job_edit", requirements={"token": "\w+"})
     * @Method("GET")
     */
    public function editAction($token)
    {
      $em = $this->getDoctrine()->getEntityManager();

      $job = $em->getRepository('JobeetBundle:Job')->findOneByToken($token);

      if (!$job) {
        throw $this->createNotFoundException('Unable to find Job entity.');
      }
      
      if ($job->getIsActivated()) {
        throw $this->createNotFoundException('Job is activated and cannot be edited.');
      }

      $editForm = $this->createForm('Epfc\JobeetBundle\Form\JobType', $job);
      $deleteForm = $this->createDeleteForm($token);

      return $this->render('job/edit.html.twig', array(
        'job'      => $job,
        'edit_form'   => $editForm->createView(),
        'delete_form' => $deleteForm->createView(),
      ));
    }
    
    /**
     * Edits an existing job entity.
     *
     * @Route("/{token}/update", name="job_update", requirements={"token": "\w+"})
     * @Method({"POST"})
     */
    public function updateAction(Request $request, $token) {
        $em = $this->getDoctrine()->getEntityManager();
        $job = $em->getRepository('JobeetBundle:Job')->findOneByToken($token);

        if (!$job) {
          throw $this->createNotFoundException('Unable to find Job entity.');
        }
        
        $deleteForm = $this->createDeleteForm($token);
        $editForm = $this->createForm('Epfc\JobeetBundle\Form\JobType', $job);     
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('job_preview', array(
                'company' => $job->getCompanySlug(),
                'location' => $job->getLocationSlug(),
                'token' => $job->getToken(),
                'position' => $job->getPositionSlug()
            ));
        }

        return $this->render('job/update.html.twig', array(
            'job' => $job,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a job entity.
     *
     * @Route("/{token}", name="job_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request, $token)
    {    
        $form = $this->createDeleteForm($token);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
 
            $job = $em->getRepository('JobeetBundle:Job')->findOneByToken($token);

            if (!$job) {
              throw $this->createNotFoundException('Unable to find Job entity.');
            }
            
            $em->remove($job);
            $em->flush();
        }

        return $this->redirectToRoute('job_index');
    }

    /**
     * Creates a form to delete a job entity.
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($token)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('job_delete', array('token' => $token)))
            ->getForm()
        ;
    }
    
    /**
     * Publishes a Job entity
     *
     * @Route("/{token}/publish", name="job_publish" )
     * @Method("POST")
     */
    public function publishAction(Request $request, $token)
    {
        $form = $this->createPublishForm($token);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
          $em = $this->getDoctrine()->getEntityManager();

          $job = $em->getRepository('JobeetBundle:Job')->findOneByToken($token);

          if (!$job) {
            throw $this->createNotFoundException('Unable to find Job entity.');
          }

          $job->publish();
          $em->persist($job);
          $em->flush();

          $this->get('session')->getFlashBag()->add('notice', 'Your job is now online for 30 days.');
        }

        return $this->redirectToRoute('job_preview', array(
            'company' => $job->getCompanySlug(),
            'location' => $job->getLocationSlug(),
            'token' => $job->getToken(),
            'position' => $job->getPositionSlug()
        ));
    }

    /**
     * Creates a form to publish a job entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createPublishForm($token)
    {
      return $this->createFormBuilder()
            ->setAction($this->generateUrl('job_publish', array('token' => $token)))
            ->getForm()
        ;
    }
    
    /**
     * Extends a Job entity
     *
     * @Route("/{token}/extend", name="job_extend" )
     * @Method("POST")
     */
    public function extendAction($token, Request $request) {
      $form = $this->createExtendForm($token);
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
        $em = $this->getDoctrine()->getEntityManager();

        $job = $em->getRepository('JobeetBundle:Job')->findOneByToken($token);

        if (!$job) {
          throw $this->createNotFoundException('Unable to find Job entity.');
        }

        if (!$job->extend()) {
          throw $this->createNotFoundException('Unable to find extend the Job.');
        }

        $em->persist($job);
        $em->flush();

        $this->get('session')->getFlashBag()->add('notice', sprintf('Your job validity has been extended until %s.', $job->getExpiresAt()->format('m/d/Y')));
      }

      return $this->redirectToRoute('job_preview', array(
        'company' => $job->getCompanySlug(),
        'location' => $job->getLocationSlug(),
        'token' => $job->getToken(),
        'position' => $job->getPositionSlug()
      ));
    }

    /**
     * Creates a form to extend a Job entity
     * 
     * @return \Symfony\Component\Form\Form The form
     */
    private function createExtendForm($token)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('job_extend', array('token' => $token)))
            ->getForm()
        ;
    }
 
}
