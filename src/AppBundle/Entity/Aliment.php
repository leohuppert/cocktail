<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Aliment
 *
 * @ORM\Table(name="aliment")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AlimentRepository")
 */
class Aliment
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
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Aliment")
     * @ORM\JoinTable(name="aliments_sub_aliments",
     *     joinColumns={@ORM\JoinColumn(name="aliment_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="sub_aliment_id", referencedColumnName="id")})
     */
    private $subAliments;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Aliment")
     * @ORM\JoinTable(name="aliments_super_aliments",
     *     joinColumns={@ORM\JoinColumn(name="aliment_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="super_aliment_id", referencedColumnName="id")})
     */
    private $superAliments;

    /**
     * Les recettes qui utilisent cet aliment
     *
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Recipe")
     * @ORM\JoinTable(name="aliments_recipes",
     *     joinColumns={@ORM\JoinColumn(name="aliment_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="recipe_id", referencedColumnName="id")})
     */
    private $recipes;

    /**
     * Aliment constructor.
     */
    public function __construct()
    {
        $this->subAliments = new ArrayCollection();
        $this->superAliments = new ArrayCollection();
        $this->recipes = new ArrayCollection();
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
     * @return Aliment
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
     * @return ArrayCollection
     */
    public function getSubAliments()
    {
        return $this->subAliments;
    }

    /**
     * @param ArrayCollection $subAliments
     */
    public function setSubAliments(ArrayCollection $subAliments)
    {
        $this->subAliments = $subAliments;
    }

    /**
     * @param Aliment $aliment
     */
    public function addSubAliment(Aliment $aliment)
    {
        if (!$this->subAliments->contains($aliment)) {
            $this->subAliments->add($aliment);
        }
    }

    /**
     * @param Aliment $aliment
     */
    public function removeSubAliment(Aliment $aliment)
    {
        if ($this->subAliments->contains($aliment)) {
            $this->subAliments->removeElement($aliment);
        }
    }

    /**
     * @return ArrayCollection
     */
    public function getSuperAliments()
    {
        return $this->superAliments;
    }

    /**
     * @param ArrayCollection $superAliments
     */
    public function setSuperAliments(ArrayCollection $superAliments)
    {
        $this->superAliments = $superAliments;
    }

    /**
     * @param Aliment $aliment
     */
    public function addSuperAliment(Aliment $aliment)
    {
        if (!$this->superAliments->contains($aliment)) {
            $this->superAliments->add($aliment);
        }
    }

    /**
     * @param Aliment $aliment
     */
    public function removeSuperAliment(Aliment $aliment)
    {
        if ($this->superAliments->contains($aliment)) {
            $this->superAliments->removeElement($aliment);
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }

    /**
     * @return ArrayCollection
     */
    public function getRecipes()
    {
        return $this->recipes;
    }

    /**
     * @param Recipe $recipe
     */
    public function addRecipe(Recipe $recipe)
    {
        if (!$this->recipes->contains($recipe)) {
            $this->recipes->add($recipe);
        }
    }

    /**
     * @param ArrayCollection $recipes
     */
    public function setRecipes(ArrayCollection $recipes)
    {
        $this->recipes = $recipes;
    }

}
