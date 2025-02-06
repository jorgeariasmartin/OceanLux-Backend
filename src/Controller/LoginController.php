<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Repository\UserAccountRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class LoginController extends AbstractController
{
    private UserAccountRepository $userAccountRepository;
    private UserPasswordHasherInterface $passwordHasher;
    private JWTTokenManagerInterface $jwtManager;
    private LoggerInterface $logger;

    public function __construct(UserAccountRepository $userAccountRepository, UserPasswordHasherInterface $passwordHasher, JWTTokenManagerInterface $jwtManager, LoggerInterface $logger)
    {
        $this->userAccountRepository = $userAccountRepository;
        $this->passwordHasher = $passwordHasher;
        $this->jwtManager = $jwtManager;
        $this->logger = $logger;
    }

    #[Route('/api/login/check', name: 'api_login_check', methods: ['POST'])]
    public function checkLogin(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        dump($data); // 👀 Verifica qué datos recibe el backend
        $this->logger->info('Datos recibidos:', $data); // Log en Symfony

        if (!isset($data['username']) || !isset($data['password'])) {
            return new JsonResponse(['code' => 400, 'message' => 'Faltan credenciales.'], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->userAccountRepository->findOneBy(['username' => $data['username']]);

        if (!$user) {
            return new JsonResponse(['code' => 401, 'message' => 'Usuario no encontrado.'], Response::HTTP_UNAUTHORIZED);
        }

        if (!$this->passwordHasher->isPasswordValid($user, $data['password'])) {
            return new JsonResponse(['code' => 401, 'message' => 'Contraseña incorrecta.'], Response::HTTP_UNAUTHORIZED);
        }

        $token = $this->jwtManager->create($user);
        return new JsonResponse(['token' => $token]);
    }

}