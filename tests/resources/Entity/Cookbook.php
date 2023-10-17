<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Tests\resources\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="DemosEurope\DocumentBakery\Tests\resources\Repository\CookbookRepository")
 */
class Cookbook
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length="64")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length="64")
     */
    private $flavour;

    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return Cookbook
     */
    public function setId(string $id): Cookbook
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return Cookbook
     */
    public function setName(string $name): Cookbook
    {
        $this->name = $name;

        return $this;
    }

    public function getFlavour(): string
    {
        return $this->flavour;
    }

    /**
     * @param string $flavour
     *
     * @return Cookbook
     */
    public function setFlavour(string $flavour): Cookbook
    {
        $this->flavour = $flavour;

        return $this;
    }
}
