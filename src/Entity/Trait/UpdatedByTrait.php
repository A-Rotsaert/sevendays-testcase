<?php

declare(strict_types=1);

namespace App\Entity\Trait;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @author <andy.rotsaert@live.be>
 */
trait UpdatedByTrait
{
    #[ORM\OneToOne(cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $updatedBy = null;

    /**
     * @return User|null
     */
    public function getUpdatedBy(): ?User
    {
        return $this->updatedBy;
    }

    /**
     * @param User|null $updatedBy
     */
    public function setUpdatedBy(?User $updatedBy): void
    {
        $this->updatedBy = $updatedBy;
    }
}
