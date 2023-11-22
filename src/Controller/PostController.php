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

class PostController extends AbstractController
{

    private $jsonConverter;

    public function __construct(JsonConverter $jsonConverter) {
        $this->jsonConverter = $jsonConverter;
    }

    #[Route('/api/post/{username}', methods: ['GET'])]
    #[OA\Get(description: 'Retourne les post d/un utilisateur')]
    #[OA\Response(
        response: 200,
        description: 'L\'utilisateur correspondant au pseudo',
        content: new OA\JsonContent(
            type: 'object',
            ref: new Model(type: Post::class)
        )
    )]
    #[OA\Tag(name: 'posts')]
    public function getPostByPostname(ManagerRegistry $doctrine, string $username)
    {
        $entityManager = $doctrine->getManager();

        $user = $entityManager->getRepository(Post::class)->find(['username' => $username]);

        if (!$user) {
            return new Response('Utilisateur non trouvé', 404);
        }

        return new Response($this->jsonConverter->encodeToJson($user));
    }

}