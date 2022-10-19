<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Trait\UpdatedByTrait;
use App\Repository\CharacteristicRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;

/**
 * @author <andy.rotsaert@live.be>
 */
#[ORM\Entity(repositoryClass: CharacteristicRepository::class)]
class Characteristic implements TranslatableInterface, TimestampableInterface
{
    use TranslatableTrait;
    use TimestampableTrait;
    use UpdatedByTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $minAge = null;

    #[ORM\ManyToMany(targetEntity: Child::class, mappedBy: 'characteristics')]
    private Collection $children;

    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMinAge(): ?int
    {
        return $this->minAge;
    }

    public function setMinAge(?int $minAge): self
    {
        $this->minAge = $minAge;

        return $this;
    }

    /**
     * @return Collection<int, Child>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(Child $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children->add($child);
            $child->addCharacteristic($this);
        }

        return $this;
    }

    public function removeChild(Child $child): self
    {
        if ($this->children->removeElement($child)) {
            $child->removeCharacteristic($this);
        }

        return $this;
    }
}
