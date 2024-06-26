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
use App\Entity\User;
use App\Entity\Post;
use App\Entity\Like;
use App\Entity\Dislike;

class CommentaireController extends AbstractController
{

    private $jsonConverter;

    public function __construct(JsonConverter $jsonConverter)
    {
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
    #[OA\Tag(name: 'commentaires')]

    public function getCommentsForPost(ManagerRegistry $doctrine, Post $post)
    {
        $entityManager = $doctrine->getManager();

        $comments = $entityManager->getRepository(Commentaire::class)->findBy(['post' => $post]);

        return new Response($this->jsonConverter->encodeToJson($comments));
    }



    #[Route('/api/commentaires', methods: ['PUT'])]
    #[OA\Put(description: 'Modifie un commentaire et retourne ses informations')]
    #[OA\Response(
        response: 200,
        description: 'Le commentaire mis à jour',
        content: new OA\JsonContent(ref: new Model(type: Commentaire::class))
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'valeur', type: 'string'),
                new OA\Property(property: 'id', type: 'int'),
            ]
        )
    )]
    #[OA\Tag(name: 'commentaires')]
    public function updateCommentaire(ManagerRegistry $doctrine)
    {
        $entityManager = $doctrine->getManager();
        $request = Request::createFromGlobals();
        $data = json_decode($request->getContent(), true);
        $id = $data['id'];
        $commentaire = $doctrine->getRepository(Commentaire::class)->find($id);

        if (!$commentaire) {
            throw $this->createNotFoundException('Pas d\'commentaire trouvé avec l\'ID ' . $id);
        }

        $commentaire->setValeur($data['valeur']);

        $entityManager->persist($commentaire);
        $entityManager->flush();

        return new Response($this->jsonConverter->encodeToJson($commentaire), 200);
    }

    #[Route('/api/commentaires/{id}', methods: ['DELETE'])]
    #[OA\Delete(description: 'Supprime un commentaire correspondant à un identifiant')]
    #[OA\Response(
        response: 200,
        description: 'Le commentaire a été supprimé',
        content: new OA\JsonContent(ref: new Model(type: Commentaire::class))
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        schema: new OA\Schema(type: 'integer'),
        required: true,
        description: 'L\'identifiant d\'un commentaire'
    )]
    #[OA\Tag(name: 'commentaires')]
    public function deleteCommentaire(ManagerRegistry $doctrine, $id)
    {
        $entityManager = $doctrine->getManager();

        $commentaire = $entityManager->getRepository(Commentaire::class)->find($id);

        if (!$commentaire) {
            throw $this->createNotFoundException(
                'Pas de commentaire avec id ' . $id
            );
        }

        $entityManager->remove($commentaire);
        $entityManager->flush();

        return new Response($this->jsonConverter->encodeToJson($commentaire));
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

    #[Route('/api/commentaires/{post}', methods: ['POST'])]
    #[OA\Post(description: 'poster un commentaire')]
    #[OA\Response(
        response: 200,
        description: 'Un commentaire'
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'valeur', type: 'string', default: 'test'),
                new OA\Property(property: 'parent', type: 'int', default: '1')
            ]
        )
    )]
    #[OA\Tag(name: 'commentaires')]
    public function createCommentaire(JWTEncoderInterface $jwtEncoder, ManagerRegistry $doctrine, $post)
    {
        $request = Request::createFromGlobals();
        $data = json_decode($request->getContent(), true);

        $entityManager = $doctrine->getManager();

        $comm = new Commentaire();
        if (isset($data['valeur'])) {
            $comm->setValeur($data['valeur']);
        }

        $postEntity = $entityManager->getRepository(Post::class)->find($post);
        $comm->setPost($postEntity);

        if (isset($data['parent']) && is_numeric($data['parent'])) {
            $parent = $entityManager->getRepository(Commentaire::class)->find($data['parent']);
            $comm->setParent($parent);
        }

        $user = $this->getUser();
        $comm->setUser($user);


        $entityManager->persist($comm);
        $entityManager->flush();

        return new Response($this->jsonConverter->encodeToJson($user));
    }

    #[Route('/api/commentaire/like/{commentaireId}', methods: ['POST'])]
    #[OA\Tag(name: 'commentaires')]
    public function addLike(int $commentaireId, Request $request, ManagerRegistry $doctrine)
    {
        $entityManager = $doctrine->getManager();

        $commentaire = $entityManager->getRepository(Commentaire::class)->find($commentaireId);

        if (!$commentaire) {
            throw $this->createNotFoundException('Pas de commentaire avec id ' . $commentaireId);
        }

        $user = $this->getUser();

        if (!$user)
            return new Response($this->jsonConverter->encodeToJson("Connexion requise"));

        $dislike = $entityManager->getRepository(Dislike::class)->findOneBy(['user' => $user, 'commentaire' => $commentaire]);

        if ($dislike) {
            $entityManager->remove($dislike);
            $entityManager->flush();
        }

        $like = $entityManager->getRepository(Like::class)->findOneBy(['user' => $user, 'commentaire' => $commentaire]);
        if ($like) {
            $entityManager->remove($like);
            $entityManager->flush();
            return new Response($this->jsonConverter->encodeToJson("Like retiré"));
        }
        $like = new Like();
        $like->setUser($user);
        $like->setCommentaire($commentaire);

        $entityManager->persist($like);
        $entityManager->flush();
        return new Response($this->jsonConverter->encodeToJson("Commentaire liké"));


    }

    #[Route('/api/commentaire/dislike/{commentaireId}', methods: ['POST'])]
    #[OA\Tag(name: 'commentaires')]
    public function addDislike(int $commentaireId, Request $request, ManagerRegistry $doctrine)
    {
        $entityManager = $doctrine->getManager();

        $commentaire = $entityManager->getRepository(Commentaire::class)->find($commentaireId);

        if (!$commentaire) {
            throw $this->createNotFoundException('Pas de commentaire avec id ' . $commentaireId);
        }

        $user = $this->getUser();

        $like = $entityManager->getRepository(Like::class)->findOneBy(['user' => $user, 'commentaire' => $commentaire]);
        if ($like) {
            $entityManager->remove($like);
            $entityManager->flush();
        }

        if (!$user)
            return new Response($this->jsonConverter->encodeToJson("Connexion requise"));

        $dislike = $entityManager->getRepository(Dislike::class)->findOneBy(['user' => $user, 'commentaire' => $commentaire]);

        if ($dislike) {
            $entityManager->remove($dislike);
            $entityManager->flush();
            return new Response($this->jsonConverter->encodeToJson("Disliké retiré"));
        }

        $dislike = new Dislike();
        $dislike->setUser($user);
        $dislike->setCommentaire($commentaire);

        $entityManager->persist($dislike);
        $entityManager->flush();
        return new Response($this->jsonConverter->encodeToJson("Commentaire disliké"));
    }

    #[Route('/api/posts/id/{commentaireId}', methods: ['GET'])]
    #[OA\Get(description: 'Retourne un commentaire selon son id')]
    #[OA\Tag(name: 'commentaires')]
    public function getPostIdByCommentaireId(ManagerRegistry $doctrine, int $commentaireId)
    {
        $entityManager = $doctrine->getManager();

        $commentaire = $entityManager->getRepository(Commentaire::class)->find($commentaireId);

        if (!$commentaire) {
            throw $this->createNotFoundException('Pas de commentaire avec id ' . $commentaireId);
        }

        $postId = $commentaire->getPost()->getId();

        return new Response($this->jsonConverter->encodeToJson($postId));
    }


    #[Route('/api/reponse/{commentaireId}', methods: ['POST'])]
    #[OA\Post(description: 'poster une reponse')]
    #[OA\Response(
        response: 200,
        description: 'Un commentaire'
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'valeur', type: 'string', default: 'test'),
            ]
        )
    )]
    #[OA\Tag(name: 'commentaires')]
    public function createReponse(JWTEncoderInterface $jwtEncoder, ManagerRegistry $doctrine, $commentaireId)
    {
        $request = Request::createFromGlobals();
        $data = json_decode($request->getContent(), true);

        $entityManager = $doctrine->getManager();
        $commentaire = $entityManager->getRepository(Commentaire::class)->find($commentaireId);

        $comm = new Commentaire();
        if (isset($data['valeur'])) {
            $comm->setValeur($data['valeur']);
        }

        
        $comm->setParent($commentaire);

        $user = $this->getUser();
        $comm->setUser($user);


        $entityManager->persist($comm);
        $entityManager->flush();

        return new Response($this->jsonConverter->encodeToJson($user));
    }

}