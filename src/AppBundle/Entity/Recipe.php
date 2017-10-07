<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Recipe
 *
 * @ORM\Table(name="recipe")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RecipeRepository")
 */
class Recipe
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="ingredients", type="string", length=255)
     */
    private $ingredients;

    /**
     * @var string
     *
     * @ORM\Column(name="preparation", type="text")
     */
    private $preparation;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Aliment")
     * @ORM\JoinTable(name="recipes_aliments",
     *     joinColumns={@ORM\JoinColumn(name="recipe_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="aliment_id", referencedColumnName="id")})
     */
    private $aliments;


    /**
     * Recipe constructor.
     */
    public function __construct()
    {
        $this->aliments = new ArrayCollection();
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Recipe
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set ingredients
     *
     * @param string $ingredients
     *
     * @return Recipe
     */
    public function setIngredients($ingredients)
    {
        $this->ingredients = $ingredients;

        return $this;
    }

    /**
     * Get ingredients
     *
     * @return string
     */
    public function getIngredients()
    {
        return $this->ingredients;
    }

    /**
     * Set preparation
     *
     * @param string $preparation
     *
     * @return Recipe
     */
    public function setPreparation($preparation)
    {
        $this->preparation = $preparation;

        return $this;
    }

    /**
     * Get preparation
     *
     * @return string
     */
    public function getPreparation()
    {
        return $this->preparation;
    }

    /**
     * @return ArrayCollection
     */
    public function getAliments()
    {
        return $this->aliments;
    }

    /**
     * @param ArrayCollection $aliments
     */
    public function setAliments(ArrayCollection $aliments)
    {
        $this->aliments = $aliments;
    }

    /**
     * @param Aliment $aliment
     */
    public function addAliment(Aliment $aliment)
    {
        if (!$this->aliments->contains($aliment)) {
            $this->aliments->add($aliment);
        }
    }

    /**
     * @param Aliment $aliment
     */
    public function removeAliment(Aliment $aliment)
    {
        if ($this->aliments->contains($aliment)) {
            $this->aliments->removeElement($aliment);
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }

}

