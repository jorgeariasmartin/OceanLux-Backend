<?php

namespace App\Controller;

use App\Entity\Trip;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use App\Repository\YachtRepository;

#[Route('/trip')]
final class TripController extends AbstractController
{
    #[Route('/list', name: 'trip_list', methods: ['GET'])]
    public function list(YachtRepository $yachtRepository): JsonResponse
    {
        $trips = $yachtRepository->findAll();

        return $this->json($trips, Response::HTTP_OK, [], [
            'groups' => ['trip:read'],
        ]);
    }

    #[Route('/create', name: 'trip_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager, LoggerInterface $logger, YachtRepository $yachtRepository, SerializerInterface $serializer): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return new JsonResponse(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $yacht = $yachtRepository->find($data['yacht']['id']); // Cambié `yacht_id` por `yacht.id`
            if (!$yacht) {
                return new JsonResponse(['error' => 'Yacht not found'], Response::HTTP_BAD_REQUEST);
            }

            $trip = new Trip();
            $trip->setName($data['name']);
            $trip->setPrice((float)$data['price']);
            $trip->setDurationHours((int)$data['duration_hours']);
            $trip->setDescription($data['description']);
            $trip->setStartdate(new \DateTime($data['startdate']));
            $trip->setEnddate(new \DateTime($data['enddate']));
            $trip->setYacht($yacht);

            $entityManager->persist($trip);
            $entityManager->flush();

            return $this->json($trip, Response::HTTP_CREATED, [], [
                'groups' => ['trip:read'],
            ]);
        } catch (\Exception $e) {
            $logger->error('Internal server error', ['exception' => $e]);
            return new JsonResponse(['error' => 'Internal server error', 'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    }