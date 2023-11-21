<?php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Nelmio\ApiDocBundle\Annotation\Model;

use OpenApi\Attributes as OA;

use App\Service\JsonConverter;
use App\Entity\Incident;

class IncidentController extends AbstractController {

    private $jsonConverter;

    public  function __construct(JsonConverter $jsonConverter) {
        $this->jsonConverter = $jsonConverter;
    }

    #[Route('/api/incidents', methods: ['GET'])]
    #[OA\Get(description: 'Retourne la liste de tous les incidents')]
    #[OA\Response(
		response: 200,
		description: 'La liste de tous les incidents',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Incident::class))
        )
	)]
	#[OA\Tag(name: 'incidents')]
    public function getAllIncidents(ManagerRegistry $doctrine) {
       
        $entityManager = $doctrine->getManager();

        $incidents = $entityManager->getRepository(Incident::class)->findAll();
        return new Response($this->jsonConverter->encodeToJson($incidents));
    }
}