<?php

namespace App\Controller;

use App\Entity\Marker;
use App\Repository\MarkerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api', name: 'api_')]
final class MarkerController extends AbstractController
{
    public function __construct(
        private readonly MarkerRepository $markerRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly SerializerInterface $serializer // Le serializer de Symfony
    ) {}

    // Les endpoints seront ajoutés ici


    #[Route('/markers', name: 'markers_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $markers = $this->markerRepository->findAll();
        return $this->json(['data' => $markers], context: [
            'groups' => 'marker:read'
        ]);
    }

    #[Route('/markers/{id}', name: 'markers_show', methods: ['GET'])]
    public function show(Marker $marker): JsonResponse
    {
        return $this->json(['data' => $marker], context: ['groups' => 'marker:read']);
    }

    #[Route('/markers', name: 'markers_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        try {
            $marker = $this->serializer->deserialize($request->getContent(), Marker::class, 'json');
            $this->entityManager->persist($marker);
            $this->entityManager->flush();

            return $this->json([
                'message' => 'Marker créé avec succès',
                'data' => $marker
            ], 201, [], ['groups' => 'marker:read']);
        } catch (\Exception $e) {
            return $this->json([
                'message' => 'Données invalides',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    #[Route('/markers/{id}', name: 'markers_update', methods: ['PATCH'])]
    public function update(Marker $marker, Request $request): JsonResponse
    {
        try {
            $this->serializer->deserialize(
                $request->getContent(),
                Marker::class,
                'json',
                ['object_to_populate' => $marker] //permet de mettre à jour l'objet existant plutôt que d'en créer un nouveau
            );

            $this->entityManager->flush();

            return $this->json([
                'message' => 'Livre mis à jour avec succès',
                'data' => $marker
            ], context: ['groups' => 'marker:read']);
        } catch (\Exception $e) {
            return $this->json([
                'message' => 'Données invalides',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    #[Route('/markers/{id}', name: 'markers_delete', methods: ['DELETE'])]
    public function delete(Marker $marker): JsonResponse
    {
        try {
            $this->entityManager->remove($marker);
            $this->entityManager->flush();

            return $this->json(['message' => 'Marker supprimé avec succès']);
        } catch (\Exception $e) {
            return $this->json([
                'message' => 'Erreur lors de la suppression',
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
