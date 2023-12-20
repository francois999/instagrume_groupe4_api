<?php

namespace App\Controller;

use PhpParser\Node\Expr\PostDec;
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
use App\Entity\Like;
use App\Entity\Dislike;

class PostController extends AbstractController
{

    private $jsonConverter;

    public function __construct(JsonConverter $jsonConverter)
    {
        $this->jsonConverter = $jsonConverter;
    }

    #[Route('/api/filtre/{username}', methods: ['GET'])]
    #[OA\Get(description: 'Retourne un post par son name')]
    #[OA\Response(
        response: 200,
        description: 'Le post correspondant à l\'identifiant',
        content: new OA\JsonContent(
            type: 'object',
            ref: new Model(type: Post::class)
        )
    )]
    public function getPostByPostname(ManagerRegistry $doctrine, $username)
    {
        $entityManager = $doctrine->getManager();

        $user = $entityManager->getRepository(User::class)->findOneBy(['username' => $username]);
        $post = $entityManager->getRepository(Post::class)->findOneBy(['user' => $user]);

        if (!$post) {
            return new Response('Post non trouvé', 404);
        }

        return new Response($this->jsonConverter->encodeToJson($post));
    }

    #[Route('/api/posts/{id}', methods: ['GET'])]
    #[OA\Get(description: 'Retourne un post par son identifiant')]
    #[OA\Response(
        response: 200,
        description: 'Le post correspondant à l\'identifiant',
        content: new OA\JsonContent(
            type: 'object',
            ref: new Model(type: Post::class)
        )
    )]
    #[OA\Tag(name: 'posts')]
    public function getPostById(ManagerRegistry $doctrine, $id)
    {
        $entityManager = $doctrine->getManager();

        $post = $entityManager->getRepository(Post::class)->find($id);

        if (!$post) {
            return new Response('Post non trouvé', 404);
        }

        return new Response($this->jsonConverter->encodeToJson($post));
    }


    #[Route('/api/posts', methods: ['POST'])]
    #[OA\Post(description: 'Crée un nouveau post')]
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
                new OA\Property(property: 'photo', type: 'string'),
                new OA\Property(property: 'description', type: 'string', default: 'test'),
            ]
        )
    )]
    #[OA\Tag(name: 'posts')]
    public function createPost(Request $request, ManagerRegistry $doctrine): Response
    {
        $data = json_decode($request->getContent(), true);

        $post = new Post();
        $post->setDescription($data['description']);

        $photoData = base64_decode($data['photo']);
        $photoFileName = md5(uniqid()) . '.png';

        // Chemin vers le dossier des photos
        $photosDirectory = $this->getParameter('kernel.project_dir') . '/public/photos/';
        $photoFilePath = $photosDirectory . $photoFileName;
        file_put_contents($photoFilePath, $photoData);
        $post->setPhoto($photoFileName);

        $user = $this->getUser();
        $post->setUser($user);

        $entityManager = $doctrine->getManager();
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
    public function deletePost(ManagerRegistry $doctrine, $id)
    {
        $entityManager = $doctrine->getManager();

        $post = $entityManager->getRepository(Post::class)->find($id);

        if (!$post) {
            throw $this->createNotFoundException(
                'Pas de commentaire avec id ' . $id
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
            items: new OA\Items(ref: new Model(type: Post::class))
        )
    )]
    #[OA\Tag(name: 'posts')]
    public function getAllPosts(ManagerRegistry $doctrine)
    {

        $entityManager = $doctrine->getManager();

        $posts = $entityManager->getRepository(Post::class)->findAll();
        return new Response($this->jsonConverter->encodeToJson($posts));
    }

    #[Route('/api/posts/{postid}/like', methods: ['GET'])]
    #[OA\Get(description: 'Retourne le nombre de likes pour un post spécifique')]
    #[OA\Response(
        response: 200,
        description: 'nombre de likes',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Post::class))
        )
    )]
    #[OA\Tag(name: 'posts')]
    public function getLikeCount(ManagerRegistry $doctrine, int $postid)
    {
        $entityManager = $doctrine->getManager();

        $post = $entityManager->getRepository(Post::class)->find($postid);

        if (!$post) {
            throw $this->createNotFoundException('Pas de post avec id ' . $postid);
        }

        $likeCount = count($post->getLikes());

        return new Response($this->jsonConverter->encodeToJson(['like_count' => $likeCount]));
    }

    #[Route('/api/posts/like/{postId}', methods: ['POST'])]
    #[OA\Tag(name: 'posts')]
    public function addLike(int $postId, Request $request, ManagerRegistry $doctrine)
    {
        $entityManager = $doctrine->getManager();

        $post = $entityManager->getRepository(Post::class)->find($postId);

        if (!$post) {
            throw $this->createNotFoundException('Pas de post avec id ' . $postId);
        }

        $user = $this->getUser();

        if (!$user)
            return new Response($this->jsonConverter->encodeToJson("Connexion requise"));

        $dislike = $entityManager->getRepository(Dislike::class)->findOneBy(['user' => $user, 'post' => $post]);

        if ($dislike) {
            $entityManager->remove($dislike);
            $entityManager->flush();
        }

        $like = $entityManager->getRepository(Like::class)->findOneBy(['user' => $user, 'post' => $post]);
        if ($like) {
            $entityManager->remove($like);
            $entityManager->flush();
            return new Response($this->jsonConverter->encodeToJson("Like retiré"));
        }
        $like = new Like();
        $like->setUser($user);
        $like->setPost($post);

        $entityManager->persist($like);
        $entityManager->flush();
        return new Response($this->jsonConverter->encodeToJson("Post liké"));

    }

    #[Route('/api/posts/dislike/{postId}', methods: ['POST'])]
    #[OA\Tag(name: 'posts')]
    public function addDislike(int $postId, Request $request, ManagerRegistry $doctrine)
    {
        $entityManager = $doctrine->getManager();

        $post = $entityManager->getRepository(Post::class)->find($postId);

        if (!$post) {
            throw $this->createNotFoundException('Pas de post avec id ' . $postId);
        }

        $user = $this->getUser();

        $like = $entityManager->getRepository(Like::class)->findOneBy(['user' => $user, 'post' => $post]);
        if ($like) {
            $entityManager->remove($like);
            $entityManager->flush();
        }

        $dislike = $entityManager->getRepository(Dislike::class)->findOneBy(['user' => $user, 'post' => $post]);

        if ($dislike) {
            $entityManager->remove($dislike);
            $entityManager->flush();
            return new Response($this->jsonConverter->encodeToJson("Disliké retiré"));
        }

        $dislike = new Dislike();
        $dislike->setUser($user);
        $dislike->setPost($post);

        $entityManager->persist($dislike);
        $entityManager->flush();
        return new Response($this->jsonConverter->encodeToJson("Post disliké"));
    }

}