<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Nelmio\ApiDocBundle\Annotation\Model;
use Doctrine\ORM\EntityManagerInterface;

use OpenApi\Attributes as OA;

use App\Service\JsonConverter;
use App\Entity\Commentaire;
use App\Entity\Post;
use App\Entity\User;

class PostController extends AbstractController
{

    private $jsonConverter;

    public function __construct(JsonConverter $jsonConverter) {
        $this->jsonConverter = $jsonConverter;
    }

    #[Route('/api/posts/{username}', methods: ['GET'])]
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


    #[Route('/api/posts', methods: ['POST'])]
    #[OA\Post(description: 'Crée un nouveau compte')]
    #[OA\Response(
        response: 200,
        description: 'Le compte a été crée',
        content: new OA\JsonContent(ref: new Model(type: User::class))
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'username', type: 'string', default: 'test'),
                new OA\Property(property: 'password', type: 'string', default: 'test'),
                new OA\Property(property: 'passwordConfirm', type: 'string', default: 'test'),
            ]
        )
    )]
    #[OA\Tag(name: 'posts')]
    public function createPost(Request $request): Response {
        $data = json_decode($request->getContent(), true);

        $post = new Post();
        $post->setDescription($data['description']);

        // Décodage et traitement de l'image base64
        $photoData = base64_decode($data['photo']);
        $photoFileName = md5(uniqid()) . '.png';
        $photoFilePath = $this->getParameter('photos_directory') . '/' . $photoFileName;

        // Enregistrement de l'image sur le serveur
        file_put_contents($photoFilePath, $photoData);

        // Associer le nom du fichier à l'entité Post
        $post->setPhoto($photoFileName);

        $user = $this->getUser();
        $post->setUser($user);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($post);
        $entityManager->flush();

        return $this->json(['message' => 'Post créé avec succès']);
    }


    #[Route('/api/posts/{id}', methods: ['DELETE'])]
    #[OA\Delete(description: 'Supprime un post correspondant à un identifiant')]
    #[OA\Response(
        response: 200,
        description: 'Le post a été supprimé',
        content: new OA\JsonContent(ref: new Model(type: Post::class))
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        schema: new OA\Schema(type: 'integer'),
        required: true,
        description: 'L\'identifiant d\'un post'
    )]
    #[OA\Tag(name: 'posts')]
    public function deletePost(ManagerRegistry $doctrine, $id) {
        $entityManager = $doctrine->getManager();

        $post = $entityManager->getRepository(Post::class)->find($id);

        if (!$post) {
            throw $this->createNotFoundException(
                'Pas de commentaire avec id '.$id
            );
        }

        $entityManager->remove($post);
        $entityManager->flush();

        return new Response($this->jsonConverter->encodeToJson($post));
    }


    #[Route('/api/posts', methods: ['GET'])]
    #[OA\Get(description: 'Retourne la liste de tous les posts')]
    #[OA\Response(
        response: 200,
        description: 'La liste de tous les posts',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: User::class))
        )
    )]
    #[OA\Tag(name: 'posts')]
    public function getAllPosts(ManagerRegistry $doctrine) {

        $entityManager = $doctrine->getManager();

        $posts = $entityManager->getRepository(Post::class)->findAll();
        return new Response($this->jsonConverter->encodeToJson($posts));
    }

}