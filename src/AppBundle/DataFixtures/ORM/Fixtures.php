<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Aliment;
use AppBundle\Entity\Recipe;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class Fixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $recipe = new Recipe();
        $recipe->setName('Vodka Red Bull');
        $recipe->setIngredients('4cl Vodka, 20 cl Redbull');
        $recipe->setPreparation('Mélange les deux ingrédients, t\'es con ou quoi ?');

        $aliment = new Aliment();
        $aliment->setName('Red Bull');

        $aliment2 = new Aliment();
        $aliment2->setName('Vodka');

        $manager->persist($aliment);
        $manager->persist($aliment2);

        $recipe->addAliment($aliment);
        $recipe->addAliment($aliment2);
        $manager->persist($recipe);
        $manager->flush();
    }
}
