<?php

declare(strict_types=1);

namespace DemosEurope\DocumentBakery\Tests\resources\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Cookbook
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length="64")
     */
    private string $name;

    /**
     * @ORM\Column(type="string", length="64")
     */
    private string $flavour;

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return Cookbook
     */
    public function setId(int $id): Cookbook
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
