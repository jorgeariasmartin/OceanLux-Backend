<?php

namespace App\Controller;

use App\Repository\YachtRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/yacht')]
final class YachtController extends AbstractController
{
    #[Route('/all', name: 'yacht_all', methods: ['GET'])]
    public function getAll(YachtRepository $yachtRepository, SerializerInterface $serializer): JsonResponse
    {
        $yachts = $yachtRepository->findAll();
        $yachts = $serializer->serialize($yachts, 'json');

        return new JsonResponse($yachts, 200, [], true);
    }

}
