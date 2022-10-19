<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Trait\BaseEntityTrait;
use App\Entity\Trait\UpdatedByTrait;
use App\Repository\FamilyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;

/**
 * @author <andy.rotsaert@live.be>
 */
#[ORM\Entity(repositoryClass: FamilyRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Family implements TimestampableInterface
{
    use TimestampableTrait;
    use UpdatedByTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'family', targetEntity: Child::class, orphanRemoval: true)]
    private Collection $children;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'families')]
    private Collection $users;

    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->users = new ArrayCollection();
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
     * @return Collection<int, Child>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    /**
     * @param Child $child
     * @return $this
     */
    public function addChild(Child $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children->add($child);
            $child->setFamily($this);
        }

        return $this;
    }

    /**
     * @param Child $child
     * @return $this
     */
    public function removeChild(Child $child): self
    {
        if ($this->children->removeElement($child)) {
            // set the owning side to null (unless already changed)
            if ($child->getFamily() === $this) {
                $child->setFamily(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    /**
     * @param User $user
     * @return $this
     */
    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addFamily($this);
        }

        return $this;
    }

    /**
     * @param User $user
     * @return $this
     */
    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            $user->removeFamily($this);
        }

        return $this;
    }
}
