<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Trait\BaseEntityTrait;
use App\Entity\Trait\UpdatedByTrait;
use App\Repository\ChildRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;

/**
 * @author <andy.rotsaert@live.be>
 */
#[ORM\Entity(repositoryClass: ChildRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Child implements TimestampableInterface
{
    use TimestampableTrait;
    use UpdatedByTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?int $height = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $birthday = null;

    #[ORM\ManyToMany(targetEntity: Characteristic::class, inversedBy: 'children')]
    private Collection $characteristics;

    #[ORM\ManyToOne(inversedBy: 'children')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Family $family = null;

    #[ORM\Column(length: 255)]
    private ?string $gender = null;

    public function __construct()
    {
        $this->characteristics = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getHeight(): ?int
    {
        return $this->height;
    }

    /**
     * @param int|null $height
     * @return void
     */
    public function setHeight(?int $height): void
    {
        $this->height = $height;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    /**
     * @param \DateTimeInterface $birthday
     * @return $this
     */
    public function setBirthday(\DateTimeInterface $birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * @return Collection<int, Characteristic>
     */
    public function getCharacteristics(): Collection
    {
        return $this->characteristics;
    }

    /**
     * @param Characteristic $characteristic
     * @return $this
     */
    public function addCharacteristic(Characteristic $characteristic): self
    {
        if (!$this->characteristics->contains($characteristic)) {
            $this->characteristics->add($characteristic);
        }

        return $this;
    }

    /**
     * @param Characteristic $characteristic
     * @return $this
     */
    public function removeCharacteristic(Characteristic $characteristic): self
    {
        $this->characteristics->removeElement($characteristic);

        return $this;
    }

    /**
     * @return Family|null
     */
    public function getFamily(): ?Family
    {
        return $this->family;
    }

    /**
     * @param Family|null $family
     * @return $this
     */
    public function setFamily(?Family $family): self
    {
        $this->family = $family;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getGender(): ?string
    {
        return $this->gender;
    }

    /**
     * @param string $gender
     * @return $this
     */
    public function setGender(string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }
}
