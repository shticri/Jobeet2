<?php

namespace Epfc\JobeetBundle\Controller;
 
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Epfc\JobeetBundle\Entity\Affiliate;
use Epfc\JobeetBundle\Entity\Job;
use Epfc\JobeetBundle\Repository\AffiliateRepository;
 
/**
 * Api controller.
 *
 * @Route("/api")
 */
class ApiController extends Controller
{
/**
     * 
     *
     * @Route("/chien")
     */ 
    public function chien(){
        echo 'ouaf';
        die();
    }

    /**
     * Lists all affiliate jobs.
     *
     * @Route("/{token}/jobs.{_format}", name="api_list", requirements={"_format": "xml|json|yaml"})
     */
    public function listAction(Request $request, $token)
    {
        $em = $this->getDoctrine()->getManager();
 
        $jobs = array();
 
        $rep = $em->getRepository('JobeetBundle:Affiliate');
        $affiliate = $rep->getForToken($token);
 
        if(!$affiliate) { 
            throw $this->createNotFoundException('This affiliate account does not exist!');
        }
 
        $rep = $em->getRepository('JobeetBundle:Job');
        $active_jobs = $rep->getActiveJobs(null, null, null, $affiliate->getId());
        
        foreach ($active_jobs as $job) {
            $jobs[$this->get('router')->generate('job_show', array('company' => $job->getCompanySlug(), 'location' => $job->getLocationSlug(), 'id' => $job->getId(), 'position' => $job->getPositionSlug()), true)] = $job->asArray($request->getHost());
        }
 
        $format = $request->getRequestFormat();
        $jsonData = json_encode($jobs);
 
        if ($format == "json") {
            $headers = array('Content-Type' => 'application/json'); 
            $response = new Response($jsonData, 200, $headers);
 
            return $response;
        }
 
        return $this->render('api/jobs.' . $format . '.twig', array('jobs' => $jobs));  
    }
}
