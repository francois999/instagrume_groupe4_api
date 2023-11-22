<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Bundle\MakerBundle\MakerBundle;


use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\JsonResponse;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use OpenApi\Attributes as OA;

use App\Service\JsonConverter;
use App\Entity\Commentaire;
use App\Entity\Post;

class CommentaireController extends AbstractController
{

    private $jsonConverter;

    public function __construct(JsonConverter $jsonConverter) {
        $this->jsonConverter = $jsonConverter;
    }


    #[Route('/api/commentaires/{post}', methods: ['GET'])]
    #[OA\Get(description: 'Retourne les commentaires du post')]
    #[OA\Response(
        response: 200,
        description: 'L\'utilisateur correspondant au pseudo',
        content: new OA\JsonContent(
            type: 'object',
            ref: new Model(type: Commentaire::class)
        )
    )]
    #[OA\Tag(name: 'utilisateurs')]

    public function getCommentsForPost(ManagerRegistry $doctrine, Post $post)
    {
        $entityManager = $doctrine->getManager();
    
        $comments = $entityManager->getRepository(Commentaire::class)->findBy(['post' => $post]);
    
        return new Response($this->jsonConverter->encodeToJson($comments));
    }


    #[Route('/api/commentaires', methods: ['GET'])]
    #[OA\Get(description: 'Retourne les commentaires')]
    #[OA\Response(
        response: 200,
        description: 'L\'utilisateur correspondant au pseudo',
        content: new OA\JsonContent(
            type: 'object',
            ref: new Model(type: Commentaire::class)
        )
    )]
    #[OA\Tag(name: 'commentaires')]

    public function getComments(ManagerRegistry $doctrine)
    {
    
        $entityManager = $doctrine->getManager();

        $commentaires = $entityManager->getRepository(Commentaire::class)->findAll();
        return new Response($this->jsonConverter->encodeToJson($commentaires));
    }

}