<?php

namespace App\Entity;

use App\Repository\CommentaireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentaireRepository::class)]
class Commentaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $valeur = null;

    #[ORM\ManyToOne(inversedBy: 'commentaires', cascade: ['persist'])]  
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'commentaire', targetEntity: Like::class)]
    private Collection $likes;

    #[ORM\OneToMany(mappedBy: 'commentaire', targetEntity: Dislike::class)]
    private Collection $dislikes;

    #[ORM\ManyToOne(inversedBy: 'commentaires')]
    #[ORM\JoinColumn(onDelete: "CASCADE")]
    private ?Post $post = null;

    #[ORM\ManyToOne(inversedBy: 'reponses', targetEntity: self::class)]
    #[ORM\JoinColumn(onDelete: "CASCADE")]
    private ?Commentaire $parent = null;

    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class)]
    #[ORM\JoinColumn(onDelete: "CASCADE")]
    private Collection $responses;


    public function __construct()
    {
        $this->likes = new ArrayCollection();
        $this->responses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): static
    {
        $this->post = $post;

        return $this;
    }

    public function getValeur(): ?string
    {
        return $this->valeur;
    }

    public function setValeur(string $valeur): static
    {
        $this->valeur = $valeur;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getParent(): ?Commentaire
    {
        return $this->parent;
    }

    public function setParent(?Commentaire $parent): static
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection<int, Like>
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(Like $like): static
    {
        if (!$this->likes->contains($like)) {
            $this->likes->add($like);
            $like->setCommentaire($this);
        }

        return $this;
    }

    public function removeLike(Like $like): static
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getCommentaire() === $this) {
                $like->setCommentaire(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Dislike>
     */
    public function getDislikes(): Collection
    {
        return $this->dislikes;
    }

    public function addDislike(Dislike $dislike): static
    {
        if (!$this->dislikes->contains($dislike)) {
            $this->dislikes->add($dislike);
            $dislike->setCommentaire($this);
        }

        return $this;
    }

    public function removeDislike(Dislike $dislike): static
    {
        if ($this->dislikes->removeElement($dislike)) {
            // set the owning side to null (unless already changed)
            if ($dislike->getCommentaire() === $this) {
                $dislike->setCommentaire(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Commentaire>
     */
    public function getResponses(): Collection
    {
        return $this->responses;
    }

    public function addResponse(Commentaire $response): static
    {
        if (!$this->responses->contains($response)) {
            $this->responses->add($response);
            $response->setParent($this);
        }

        return $this;
    }

    public function removeResponse(Commentaire $response): static
    {
        if ($this->responses->removeElement($response)) {
            // set the owning side to null (unless already changed)
            if ($response->getParent() === $this) {
                $response->setParent(null);
            }
        }

        return $this;
    }

}
