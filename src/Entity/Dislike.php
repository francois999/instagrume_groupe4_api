<?php

namespace App\Entity;

use App\Repository\DislikeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DislikeRepository::class)]
#[ORM\Table(name: '`dislike`')]
class Dislike
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'dislikes')]
    private ?user $user = null;

    #[ORM\ManyToOne(inversedBy: 'dislikes')]
    private ?Commentaire $commentaire = null;

    #[ORM\ManyToOne(inversedBy: 'dislikes')]
    private ?Post $post = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?user
    {
        return $this->user;
    }

    public function setUser(?user $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getCommentaire(): ?Commentaire
    {
        return $this->commentaire;
    }

    public function setCommentaire(?Commentaire $commentaire): static
    {
        $this->commentaire = $commentaire;

        return $this;
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
}
