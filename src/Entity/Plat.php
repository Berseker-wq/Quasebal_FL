<?php

namespace App\Entity;

use App\Repository\PlatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PlatRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['plat:read']],
    denormalizationContext: ['groups' => ['plat:write']],
    operations: [
        new GetCollection(),
        new Post(),
        new Get(),
        new Put(),
        new Patch(),
        new Delete()
    ]
)]
#[Vich\Uploadable]
class Plat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['plat:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Groups(['plat:read', 'plat:write'])]
    private ?string $libelle = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['plat:read', 'plat:write'])]
    private ?string $description = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 6, scale: 2)]
    #[Groups(['plat:read', 'plat:write'])]
    private ?string $prix = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(['plat:read', 'plat:write'])]
    private ?string $image = null;

    // Fichier temporaire (non stocké en base)
    #[Vich\UploadableField(mapping: 'plat_image', fileNameProperty: 'image')]
    #[Groups(['plat:write'])]
    private ?File $imageFile = null;

    #[ORM\Column]
    #[Groups(['plat:read', 'plat:write'])]
    private ?bool $active = null;

    /**
     * @var Collection<int, Detail>
     */
    #[ORM\OneToMany(targetEntity: Detail::class, mappedBy: 'plat', orphanRemoval: true)]
    private Collection $details;

    #[ORM\ManyToOne(inversedBy: 'plats')]
    #[Assert\NotNull(message: 'La catégorie est obligatoire')]
    #[Groups(['plat:read', 'plat:write'])]
    private ?Categorie $categorie = null;

    public function __construct()
{
    $this->details = new ArrayCollection();
    $this->active = true; // ✅ ou false selon ta logique métier
}


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPrix(): ?string
    {
        return $this->prix;
    }

    public function setPrix(string $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }
    public function getImageFile(): ?File
{
    return $this->imageFile;
}
public function setImageFile(?File $imageFile = null): void
{
    $this->imageFile = $imageFile;
}







    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return Collection<int, Detail>
     */
    public function getDetails(): Collection
    {
        return $this->details;
    }

    public function addDetail(Detail $detail): static
    {
        if (!$this->details->contains($detail)) {
            $this->details->add($detail);
            $detail->setPlat($this);
        }

        return $this;
    }

    public function removeDetail(Detail $detail): static
    {
        if ($this->details->removeElement($detail)) {
            // set the owning side to null (unless already changed)
            if ($detail->getPlat() === $this) {
                $detail->setPlat(null);
            }
        }

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): static
    {
        $this->categorie = $categorie;

        return $this;
    }
   }

