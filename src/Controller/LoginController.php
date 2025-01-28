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
        $username = $data['username'] ?? '';
        $password = $data['password'] ?? '';

        $this->logger->info('Checking login for username: ' . $username);

        $user = $this->userAccountRepository->findOneBy(['username' => $username]);

        if (!$user) {
            $this->logger->warning('User not found: ' . $username);
            return new JsonResponse(['code' => 401, 'message' => 'Invalid credentials.'], Response::HTTP_UNAUTHORIZED);
        }

        if (!$this->passwordHasher->isPasswordValid($user, $password)) {
            $this->logger->warning('Invalid password for user: ' . $username);
            return new JsonResponse(['code' => 401, 'message' => 'Invalid credentials.'], Response::HTTP_UNAUTHORIZED);
        }

        $this->logger->info('User authenticated: ' . $username);

        $token = $this->jwtManager->create($user);

        return new JsonResponse(['token' => $token]);
    }
}