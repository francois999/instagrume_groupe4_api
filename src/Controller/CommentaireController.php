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
use App\Entity\Like;

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
    #[OA\Tag(name: 'commentaires')]

    public function getCommentsForPost(ManagerRegistry $doctrine, Post $post)
    {
        $entityManager = $doctrine->getManager();
   
        $comments = $entityManager->getRepository(Commentaire::class)->findBy(['post' => $post]);
   
        return new Response($this->jsonConverter->encodeToJson($comments));
    }

    #[Route('/api/commentaires/{id}', methods: ['PUT'])]
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
            ]
        )
    )]
    #[OA\Tag(name: 'commentaires')]
    public function updateCommentaire(int $id, ManagerRegistry $doctrine) {
        $entityManager = $doctrine->getManager();
        $request = Request::createFromGlobals();
        $data = json_decode($request->getContent(), true);
        $commentaire = $doctrine->getRepository(Commentaire::class)->find($id);
    
        if (!$commentaire) {
            throw $this->createNotFoundException('Pas d\'commentaire trouvé avec l\'ID ' . $id);
        }
    
        $commentaire->setValeur($data['valeur']);
    
        // Vérifiez si le paramètre 'like' est défini dans la requête
        if (isset($data['like'])) {
            // Vous devez implémenter la logique appropriée ici pour gérer le Like
            $like = $doctrine->getRepository(Like::class)->find($data['like']);
            if ($like) {
                // Implémentez la logique de gestion du Like ici
            }
        }
    
        $entityManager->persist($commentaire);
        $entityManager->flush();
    
        // Utilisez JsonResponse pour simplifier la création de réponses JSON
        return new JsonResponse($this->jsonConverter->encodeToJson($commentaire), 200);
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
    public function deleteCommentaire(ManagerRegistry $doctrine, $id) {
        $entityManager = $doctrine->getManager();

        $commentaire = $entityManager->getRepository(Commentaire::class)->find($id);

        if (!$commentaire) {
            throw $this->createNotFoundException(
                'Pas de commentaire avec id '.$id
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

      #[Route('/api/commentaires', methods: ['POST'])]
      #[Security(name: null)]
      #[OA\Post(description: 'Poster un commentaire')]
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
                  new OA\Property(property: 'user_id', type: 'int', default: 1),
                  new OA\Property(property: 'parent', type: 'int', default: 1)
              ]
          )
      )]
      #[OA\Tag(name: 'commentaires')]
      public function createCommentaire(ManagerRegistry $doctrine)
      {
          $request = Request::createFromGlobals();
          $data = json_decode($request->getContent(), true);
  
          $entityManager = $doctrine->getManager();
          $comm = new Commentaire();
  
          $authorizationHeader = $request->headers->get('Authorization');

          // Vérifier si le header Authorization est présent et commence par "Bearer "
          if ($authorizationHeader && strpos($authorizationHeader, 'Bearer ') === 0) {
              // Extraire le token en supprimant "Bearer " du début
              $token = substr($authorizationHeader, 7);
      
              // Vous pouvez maintenant utiliser $token comme nécessaire
          }
          
          $user = $tokenStorage->getToken()->getUser();
          $comm->setUser($user);
  
          $comm->setValeur($data['valeur']);
  
          // Assurez-vous que la clé 'parent' existe dans les données avant de l'utiliser
          $comm->setParent(isset($data['parent']) ? $data['parent'] : null);
  
          $entityManager->persist($comm);
          $entityManager->flush();
  
          return new JsonResponse($this->jsonConverter->encodeToJson($comm));
      }

}