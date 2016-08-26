<?php

// src/OC/PlatformBundle/DataFixtures/ORM/LoadFixtures.php

namespace OC\PlatformBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OC\PlatformBundle\Entity\Skill;

class LoadFixtures implements FixtureInterface{
	
	public function load(ObjectManager $manager){
		
		$names = array(
			'PHP',
			'Symfony',
			'C++',
			'Java',
			'Photoshop',
			'Blender',
			'Bloc-note'
		);
		
		foreach($names as $name){
			$skill = new Skill();
			$skill->setName($name);
			$manager->persist($skill);
		}
		
		$manager->flush();
	}
}
