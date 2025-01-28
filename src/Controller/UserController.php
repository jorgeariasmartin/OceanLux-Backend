<?php

namespace App\Controller;

use App\Entity\UserAccount;
use App\Enum\Role;
use App\Repository\UserAccountRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/user')]
final class UserController extends AbstractController
{

    #[Route('/all', name: 'user_all', methods: ['GET'])]
    public function getAll(UserAccountRepository $userRepository, SerializerInterface $serializer): JsonResponse
    {
        $users = $userRepository->findAll();
        $users = $serializer->serialize($users, 'json');

        return new JsonResponse($users, 200, [], true);
    }

    #[Route('/{id}', name: 'user_one', methods: ['GET'])]
    public function getOne(UserAccountRepository $userAccountRepository, SerializerInterface $serializer, int $id): JsonResponse
    {
        $user = $userAccountRepository->find($id);
        $user = $serializer->serialize($user, 'json');

        return new JsonResponse($user, 200, [], true);
    }

    #[Route('/create', name: 'user_create', methods: ['POST'])]
    public function create(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $data = $request->getContent();
        $user = $serializer->deserialize($data, UserAccount::class, 'json');

        // Hash the password before saving
        $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
        $user->setPassword($hashedPassword);

        // Set the default role to user
        $user->setRol(Role::from("user"));

        $em->persist($user);
        $em->flush();

        return new JsonResponse('User created', 201, []);
    }

    #[Route('/update/{id}', name: 'user_update', methods: ['PUT'])]
    public function update(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, int $id): JsonResponse
    {
        $data = $request->getContent();
        $user = $serializer->deserialize($data, UserAccount::class, 'json');

        $user->setId($id);

        $em->persist($user);
        $em->flush();

        return new JsonResponse('User updated', 200, []);
    }

    #[Route('/delete/{id}', name: 'user_delete', methods: ['DELETE'])]
    public function delete(UserAccountRepository $userRepository, EntityManagerInterface $em, int $id): JsonResponse
    {
        $user = $userRepository->find($id);

        $em->remove($user);
        $em->flush();

        return new JsonResponse('User deleted', 200, []);
    }

}



#despues de copiarlo pruebo a crear uno nuevo con el postman y con la api de api/login/check pruebo a cree el token