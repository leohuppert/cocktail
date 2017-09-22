<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Aliment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\DataFixtures\Donnees;

class Fixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // Ajout des entités aliment sans relations sous/super catégorie
        foreach (Donnees::$Hierarchie as $key => $value) {

            if (empty($manager->getRepository('AppBundle:Aliment')->findBy(array('name' => $key)))) {
                $al = new Aliment();
                $al->setName($key);
                $manager->persist($al);
            }
        }

        $manager->flush();

        foreach (Donnees::$Hierarchie as $key => $value) {

            // Voir si sous catégorie
            // si oui => trouver chaque aliment et l'ajouter comme sous-aliment
            // Pareil pour les super-catégorie

            $currentAliment = $manager->getRepository('AppBundle:Aliment')
                ->findBy(array('name' => $key))[0];

            // S'il y a des super catégories
            if (isset($value['super-categorie']) && count($value['super-categorie']) > 0) {
                foreach ($value['super-categorie'] as $super) {

                    $alimentSuper = $manager->getRepository('AppBundle:Aliment')->findBy(array('name' => $super));

                    if (!empty($alimentSuper)) {
                        $currentAliment->addSuperAliment($alimentSuper[0]);
                    }
                }
            }

            // S'il y a des sous catégories
            if (isset($value['sous-categorie']) && count($value['sous-categorie']) > 0) {
                foreach ($value['sous-categorie'] as $sous) {

                    $alimentSous = $manager->getRepository('AppBundle:Aliment')->findBy(array('name' => $sous));

                    if (!empty($alimentSous)) {
                        $currentAliment->addSubAliment($alimentSous[0]);
                    }
                }
            }
        }

        $manager->flush();
    }
}
