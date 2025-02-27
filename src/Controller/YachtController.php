<?php

namespace App\Controller;

use App\Entity\Yacht;
use App\Repository\YachtRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('api/yacht')]
class YachtController extends AbstractController
{

    #[Route('/create', name: 'yacht_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return new JsonResponse(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $yacht = new Yacht();
            $yacht->setName($data['name']);
            $yacht->setModel($data['model']);
            $yacht->setImage($data['image']);
            $yacht->setDescription($data['description']);
            $yacht->setCapacity((int)$data['capacity']);

            $entityManager->persist($yacht);
            $entityManager->flush();

            return new JsonResponse($serializer->serialize($yacht, 'json', ['groups' => ['yacht:read']]), Response::HTTP_CREATED, [], true);
        } catch (Exception $e) {
            return new JsonResponse(['error' => 'Internal server error', 'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/all', name: 'yacht_all', methods: ['GET'])]
    public function getAll(YachtRepository $yachtRepository, SerializerInterface $serializer): JsonResponse
    {
        $yachts = $yachtRepository->findAll();
        $yachts = $serializer->serialize($yachts, 'json', ['groups' => ['yacht:read']]);

        return new JsonResponse($yachts, 200, [], true);
    }

    #[Route('/update/{id}', name: 'yacht_update', methods: ['PUT'])]
    public function update($id, Request $request, EntityManagerInterface $entityManager, YachtRepository $yachtRepository, SerializerInterface $serializer): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return new JsonResponse(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }

        $yacht = $yachtRepository->find($id);

        if (!$yacht) {
            return new JsonResponse(['error' => 'Yacht not found'], Response::HTTP_NOT_FOUND);
        }

        try {
            $yacht->setName($data['name']);
            $yacht->setModel($data['model']);
            $yacht->setImage($data['image']);
            $yacht->setDescription($data['description']);
            $yacht->setCapacity((int)$data['capacity']);

            $entityManager->persist($yacht);
            $entityManager->flush();

            return new JsonResponse($serializer->serialize($yacht, 'json', ['groups' => ['yacht:read']]), Response::HTTP_OK, [], true);
        } catch (Exception $e) {
            return new JsonResponse(['error' => 'Internal server error', 'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/delete/{id}', name: 'yacht_delete', methods: ['DELETE'])]
    public function delete($id, EntityManagerInterface $entityManager, YachtRepository $yachtRepository): JsonResponse
    {
        $yacht = $yachtRepository->find($id);

        if (!$yacht) {
            return new JsonResponse(['error' => 'Yacht not found'], Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($yacht);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Yacht deleted'], Response::HTTP_OK);
    }



}