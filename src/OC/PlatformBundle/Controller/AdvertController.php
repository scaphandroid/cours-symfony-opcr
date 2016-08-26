<?php

// src/OC/PlatformBundle/Controller/AdvertController.php

namespace OC\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request; 
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
// use Symfony\Component\HttpFoundation\RedirectResponse; //pour faire des redirections

//les entités :
use OC\PlatformBundle\Entity\Advert;
use OC\PlatformBundle\Entity\Image;
use OC\PlatformBundle\Entity\Application;
use OC\PlatformBundle\Entity\AdvertSkill;

class AdvertController extends Controller
{

	public function indexAction($page){
		
		//on ne peut pas avoir d'index de page négatif
		if ($page < 1 ){
			
			//cette exception affiche une page 404 que l'on pourra personnaliser par la suite
			throw new NotFoundHttpException('Page "'.$page.'" inexistante !');		
		}
		
		//TODO provisoire
		$listTest = array(
			array(
				'title'   => 'Recherche développpeur Symfony',
				'id'      => 1,
				'author'  => 'Alexandre',
				'content' => 'Nous recherchons un développeur Symfony débutant sur Lyon. Blabla…',
				'date'    => new \Datetime()),
			array(
				'title'   => 'Mission de webmaster',
				'id'      => 2,
				'author'  => 'Hugo',
				'content' => 'Nous recherchons un webmaster capable de maintenir notre site internet. Blabla…',
				'date'    => new \Datetime()),
			array(
				'title'   => 'Offre de stage webdesigner',
				'id'      => 3,
				'author'  => 'Mathieu',
				'content' => 'Nous proposons un poste pour webdesigner. Blabla…',
				'date'    => new \Datetime())
		);
		
		return $this->render('OCPlatformBundle:Advert:index.html.twig', array(
			'listAdverts' => $listTest
		));
	}
	
	public function viewAction($id){
		
		//premier test de récupération d'entité
		
		$em = $this->getDoctrine()->getManager();
		
		//récupérer le repository
		$repository = $this->getDoctrine()
			->getManager()
			->getRepository('OCPlatformBundle:Advert')
		;
		
		$advert = $repository->find($id);
		
		if ($advert == null){
			throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas !");
		}
		
		//on récupère les candidatures liées à cette annonce
		$repositoryApplication = $em->getRepository('OCPlatformBundle:Application');
		
		$listApplications = $repositoryApplication->findBy(array('advert' => $advert));
		
		//on récupère les advertskills de cette annonce
		$listAdvertSkills = $em
			->getRepository('OCPlatformBundle:AdvertSkill')
			->findBy(array('advert' => $advert))
		;
		
		
		return $this->render('OCPlatformBundle:Advert:view.html.twig', array(
			'advert' => $advert,
			'listApplications' => $listApplications,
			'listAdvertSkills' => $listAdvertSkills
		));
				
	}
	
	public function addAction( Request $request){
		
		//on récupère l'EntityManager
		$em = $this->getDoctrine()->getManager();
		
		//création de l'entité Advert
		$advert = new Advert();
		$advert->setTitle('Recherche développeur Symfony');
		$advert->setAuthor('Jambon');
		$advert->setContent("Nous recherchons un développeur Symfony débutant sur Lyon. Blabla..");
		//pour l'image de l'advert
		$image = new Image();
		$image->setUrl("http://sdz-upload.s3.amazonaws.com/prod/upload/job-de-reve.jpg");
		$image->setAlt("job de rêve");
		//on lie l'image à l'advert
		$advert->setImage($image);
		
		//ajout de toutes les compétences au niveau expert
		$listSkills = $em->getRepository('OCPlatformBundle:Skill')->findAll();
		
		foreach ($listSkills as $skill){
			$advertSkill = new AdvertSkill();
			$advertSkill -> setAdvert($advert);
			$advertSkill -> setSkill ($skill);
			$advertSkill -> setLevel ('Expert');
			$em -> persist($advertSkill);
		}
		
		//creation d'une première annonce
		$application1 = new Application();
		$application1->setAuthor('Marine');
		$application1->setContent("J'ai toutes les qualités requises.");
		
		//creation d'une seconde candidature
		$application2 = new Application();
		$application2->setAuthor('Jean');
		$application2->setContent("Je suis très motivé.");
		
		//on lie les candidatures à l'annonce
		$application1->setAdvert($advert);
		$application2->setAdvert($advert);
		
		$em->persist($advert);
		$em->persist($application1);
		$em->persist($application2);
				
		$em->flush();
		
		//si on envoie un formulaire la requête est en POST :
		
		//TODO provisoire
		if ($request->isMethod('POST')) {
			
			//création et gestion du formulaire à faire
			
			$request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée');
			
			//retour vers la page de visualisation de l'annonce créée
			return $this->redirectToRoute('oc_platform_view', array(
				'id' => 5
			));
		}
		
		//si la requête n'est pas en POST on affiche le formulaire
		return $this->render('OCPlatformBundle:Advert:add.html.twig');
	}
	
	public function editAction($id, Request $request){
		
		//même logique que pour le add
		
		$em = $this->getDoctrine()->getManager();
		
		$advert = $em->getRepository("OCPlatformBundle:Advert")->find($id);
		
		if ($advert === null){
			throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas !");
		}
		
		$listCategories = $em -> getRepository('OCPlatformBundle:Category')->findAll();
		
		foreach($listCategories as $category){
			$advert->addCategory($category);
		}
		
		$em->flush();
		
		if ($request->isMethod('POST')) {
			
			$request->getSession()->getFlashBag()->add('notice', 'Annonce bien modifiée');
			
			//retour vers la page de visualisation de l'annonce modifiée
			return $this->redirectToRoute('oc_platform_view', array(
				'id' => 5
			));
		}
		
		
		
		return $this->render('OCPlatformBundle:Advert:edit.html.twig', array(
			'advert' => $advert
		));
	}
	
	public function deleteAction($id) {
		
		//ici on fera la suppression de l'annonce id
		//pour le moment on supprime toutes les catégories d'une annonce
		
		$em = $this->getDoctrine()->getManager();
		
		$advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);
		
		if ($advert === null){
			throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas !");
		}
		
		foreach ($advert->getCategories() as $category){
			$advert->removeCategory($category);
		}
		
		$em->flush();
		
		return $this->render('OCPlatformBundle:Advert:delete.html.twig');
	}
	
	public function menuAction($limit){
		
		//TODO provisoire
		$listTest = array(
			array(
				'title'   => 'Recherche développpeur Symfony',
				'id'      => 1,
				'author'  => 'Alexandre',
				'content' => 'Nous recherchons un développeur Symfony débutant sur Lyon. Blabla…',
				'date'    => new \Datetime()),
			array(
				'title'   => 'Mission de webmaster',
				'id'      => 2,
				'author'  => 'Hugo',
				'content' => 'Nous recherchons un webmaster capable de maintenir notre site internet. Blabla…',
				'date'    => new \Datetime()),
			array(
				'title'   => 'Offre de stage webdesigner',
				'id'      => 3,
				'author'  => 'Mathieu',
				'content' => 'Nous proposons un poste pour webdesigner. Blabla…',
				'date'    => new \Datetime())
		);
		
		return $this->render('OCPlatformBundle:Advert:menu.html.twig', array(
			'listAdverts' => $listTest
		));
	}
}