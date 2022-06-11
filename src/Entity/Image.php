<?php
declare(strict_types=1);

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Image
 * @package App\Entity
 *
 * @ORM\Entity(repositoryClass=ImageRepository::class)
 */
class Image
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $created_at;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $path;

    /**
     * @ORM\Column(type="integer")
     */
    private $updated_at;

    /**
     * @ORM\OneToMany(targetEntity=Resize::class, mappedBy="image", orphanRemoval=true)
     */
    private $resizes;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?int
    {
        return $this->created_at;
    }

    public function setCreatedAt(int $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getUpdatedAt(): ?int
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(int $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * @return Collection<int, Resize>
     */
    public function getResizes(): Collection
    {
        return $this->resizes;
    }

    public function addResize(Resize $resize): self
    {
        if (!$this->resizes->contains($resize)) {
            $this->resizes[] = $resize;
            $resize->setImage($this);
        }

        return $this;
    }

    public function removeResize(Resize $resize): self
    {
        if ($this->resizes->removeElement($resize)) {
            // set the owning side to null (unless already changed)
            if ($resize->getImage() === $this) {
                $resize->setImage(null);
            }
        }

        return $this;
    }
}
