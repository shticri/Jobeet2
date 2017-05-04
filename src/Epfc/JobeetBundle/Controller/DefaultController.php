<?php

namespace Epfc\JobeetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Security\Core\Validator\SecurityContext;

class DefaultController extends Controller
{
    /**
     * @Route("chffg")
     */
    public function indexAction()
    {
        return $this->render('JobeetBundle:Default:index.html.twig');
    }

    /**
     * Login form
     *
     * @Route("/login", name="login")
     */
    public function loginAction(Request $request)
    {
       /* $request = $this->getRequest();
        $session = $request->getSession();
 
        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }
 
        return $this->render('EnsJobeetBundle:Default:login.html.twig', array(
            // last username entered by the user
            'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'error'         => $error,
        )); */

        //added by me
        //$session = $request->getSession();

        $authenticationUtils = $this->get('security.authentication_utils');

	    // get the login error if there is one
	    $error = $authenticationUtils->getLastAuthenticationError();

	    // last username entered by the user
	    $lastUsername = $authenticationUtils->getLastUsername();

	    return $this->render('default/login.html.twig', array(
	        'last_username' => $lastUsername,
	        'error'         => $error,
	    ));
    }
}
