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
	public function updateCommentaire(ManagerRegistry $doctrine) {
		$entityManager = $doctrine->getManager();
        $request = Request::createFromGlobals();
        $data = json_decode($request->getContent(), true);

        $commentaire = $doctrine->getRepository(Commentaire::class)->find($data['id']);

        if (!$commentaire) {
            throw $this->createNotFoundException(
                'Pas d\'commentaire'
            );
        }

        $commentaire->setValeur($data['valeur']);

        $like = $doctrine->getRepository(Like::class)->find($data['like']);

        $entityManager->persist($commentaire);
        $entityManager->flush();

        return new Response($this->jsonConverter->encodeToJson($commentaire));
    }

    #[Route('/api/delete/{id}', methods: ['DELETE'])]
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

        $abeille = $entityManager->getRepository(Commentaire::class)->find($id);

        if (!$abeille) {
            throw $this->createNotFoundException(
                'Pas de commentaire avec id '.$id
            );
        }

        $entityManager->remove($abeille);
        $entityManager->flush();

        return new Response($this->jsonConverter->encodeToJson($abeille));
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