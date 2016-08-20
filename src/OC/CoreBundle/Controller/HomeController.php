<?php

// src/OC/PlatformBundle/Controller/HomeController.php

namespace OC\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request; 
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class HomeController extends Controller
{
    public function indexAction()
    {
        return $this->render('OCCoreBundle:Home:index.html.twig');
    }
	
	public function contactAction(Request $request)
	{
		$request->getSession()->getFlashBag()->add('notice', "La page de contact n'est pas encore disponible, merci de revenir plus tard.");
		return $this->redirectToRoute('oc_core_homepage');
	}
}
