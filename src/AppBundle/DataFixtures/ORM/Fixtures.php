<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Aliment;
use AppBundle\Entity\Recipe;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\DataFixtures\Donnees;

/**
 * Class Fixtures
 * Permet de peupler la base de données
 * @package AppBundle\DataFixtures\ORM
 */
class Fixtures extends Fixture
{
    /* Source : https://stackoverflow.com/a/3373364 */
    const UNWANTED_ARRAY = array('Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
        'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
        'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
        'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
        'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y');

    /**
     * Fonction appelée lors de l'exécution de la commande
     * php bin/console doctrine:fixtures:load
     *
     * Peuple la base de données dont la config se trouve dans app/parameters.yml
     * en utilisant les données se trouvant dans src/AppBundle/DataFixtures/Donnees.php
     *
     * @param ObjectManager $manager
     */
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

                    $alimentSuper = $manager->getRepository('AppBundle:Aliment')
                        ->findBy(array('name' => $super));

                    if (!empty($alimentSuper)) {
                        $currentAliment->addSuperAliment($alimentSuper[0]);
                    }
                }
            }

            // S'il y a des sous catégories
            if (isset($value['sous-categorie']) && count($value['sous-categorie']) > 0) {
                foreach ($value['sous-categorie'] as $sous) {

                    $alimentSous = $manager->getRepository('AppBundle:Aliment')
                        ->findBy(array('name' => $sous));

                    if (!empty($alimentSous)) {
                        $currentAliment->addSubAliment($alimentSous[0]);
                    }
                }
            }
        }

        $manager->flush();

        // Les recettes
        foreach (Donnees::$Recettes as $key => $value) {
            $recipe = new Recipe();
            $recipe->setName($value['titre']);
            $recipe->setIngredients(str_replace('|', ", ", $value['ingredients']));
            $recipe->setPreparation($value['preparation']);

            // Photo
            $pictureName = strtr($value['titre'], self::UNWANTED_ARRAY);
            $pictureName = str_replace(' ', '_', $pictureName);
            $pictureName = ucfirst(strtolower($pictureName));
            $pictureName = preg_replace('/[^a-zA-Z0-9\/_|+ .-]/', '', $pictureName);
            $pictureName .= '.jpg';

            file_exists('web/assets/pictures/' . $pictureName) ? $recipe->setPicture($pictureName) : $recipe->setPicture('default.png');


            // On ajoute à la recette en cours les aliments qui la composent
            foreach ($value['index'] as $aliment) {
                $al = $manager->getRepository('AppBundle:Aliment')
                    ->findBy(array('name' => $aliment));

                if (!empty($al)) {
                    $recipe->addAliment($al[0]);
                }
            }

            $manager->persist($recipe);
        }

        $manager->flush();

        // Enregistrement des recettes => dans les entités Aliment
        $recipes = $manager->getRepository('AppBundle:Recipe')
            ->findAll();

        foreach ($recipes as $recipe) {
            foreach ($recipe->getAliments() as $aliment) {
                $aliment->addRecipe($recipe);
            }
        }

        $manager->flush();
    }
}
