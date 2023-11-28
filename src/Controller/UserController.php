<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Nelmio\ApiDocBundle\Annotation\Model;

use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\JsonResponse;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use OpenApi\Attributes as OA;

use App\Service\JsonConverter;
use App\Entity\User;


class UserController extends AbstractController
{

    private $jsonConverter;
    private $passwordHasher;

    public function __construct(JsonConverter $jsonConverter, UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
        $this->jsonConverter = $jsonConverter;
    }

    #[Route('/api/login', methods: ['POST'])]
    #[Security(name: null)]
    #[OA\Post(description: 'Connexion à l\'API')]
    #[OA\Response(
        response: 200,
        description: 'Un token'
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'username', type: 'string', default: 'admin'),
                new OA\Property(property: 'password', type: 'string', default: 'password')
            ]
        )
    )]
    #[OA\Tag(name: 'utilisateurs')]
    public function logUser(ManagerRegistry $doctrine, JWTTokenManagerInterface $JWTManager)
    {
        $request = Request::createFromGlobals();
        $data = json_decode($request->getContent(), true);

        if (!is_array($data) || $data == null || empty($data['username']) || empty($data['password'])) {
            return new Response('Identifiants invalides', 401);
        }

        $entityManager = $doctrine->getManager();
        $user = $entityManager->getRepository(User::class)->findOneBy(['username' => $data['username']]);

        if (!$user) {
            throw $this->createNotFoundException();
        }
        if (!$this->passwordHasher->isPasswordValid($user, $data['password'])) {
            return new Response('Identifiants invalides', 401);
        }

        $token = $JWTManager->create($user);
        return new JsonResponse(['token' => $token]);
    }


    #[Route('/api/inscription', methods: ['POST'])]
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
	#[OA\Tag(name: 'utilisateurs')]
	public function createUser(ManagerRegistry $doctrine) {
		$entityManager = $doctrine->getManager();
        $request = Request::createFromGlobals();
        $data = json_decode($request->getContent(), true);
       
        if ($data['password'] == $data['passwordConfirm']){
            $user = new user();
            $user->setUsername($data['username']);
            $hashedPassword = $this->passwordHasher->hashPassword($user, $data['password']);
            $user->setPassword($hashedPassword);
            $user->setRoles(["ROLE_USER"]);

            $entityManager->persist($user);
            $entityManager->flush();

            return new Response($this->jsonConverter->encodeToJson($user));
        }
        
        return new Response('Mots de passe ne correspondent pas', 401);
    }

    #[Route('/api/myself', methods: ['GET'])]
    #[OA\Get(description: 'Retourne l\'utilisateur authentifié')]
    #[OA\Response(
        response: 200,
        description: 'L\'utilisateur correspondant au token passé dans le header',
        content: new OA\JsonContent(ref: new Model(type: User::class))
    )]
    #[OA\Tag(name: 'utilisateurs')]
    public function getUtilisateur(JWTEncoderInterface $jwtEncoder, Request $request)
    {
        $tokenString = str_replace('Bearer ', '', $request->headers->get('Authorization'));

        $user = $jwtEncoder->decode($tokenString);

        return new Response($this->jsonConverter->encodeToJson($user));
    }

    #[Route('/api/users', methods: ['GET'])]
    #[OA\Get(description: 'Retourne la liste de tous les utilisateurs')]
    #[OA\Response(
        response: 200,
        description: 'La liste de tous les utilisateurs',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: User::class))
        )
    )]
    #[OA\Tag(name: 'utilisateurs')]
    public function getAllUsers(ManagerRegistry $doctrine)
    {

        $entityManager = $doctrine->getManager();

        $users = $entityManager->getRepository(User::class)->findAll();
        return new Response($this->jsonConverter->encodeToJson($users));
    }

    #[Route('/api/users/{username}', methods: ['GET'])]
    #[OA\Get(description: 'Retourne un utilisateur en fonction de son pseudo')]
    #[OA\Response(
        response: 200,
        description: 'L\'utilisateur correspondant au pseudo',
        content: new OA\JsonContent(
            type: 'object',
            ref: new Model(type: User::class)
        )
    )]
    #[OA\Tag(name: 'utilisateurs')]
    public function getUserByUsername(ManagerRegistry $doctrine, string $username)
    {
        $entityManager = $doctrine->getManager();

        $user = $entityManager->getRepository(User::class)->findOneBy(['username' => $username]);

        if (!$user) {
            // Gérer le cas où l'utilisateur n'est pas trouvé (par exemple, retourner une erreur 404)
            return new Response('Utilisateur non trouvé', 404);
        }

        return new Response($this->jsonConverter->encodeToJson($user));
    }
}
