<?php

namespace App\Controller;

use App\Entity\UserAccount;
use App\Repository\UserAccountRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

#[Route('/api/user')]
class UserController extends AbstractController
{

    #[Route('/all', name: 'user_all', methods: ['GET'])]
    public function getAll(UserAccountRepository $userRepository, SerializerInterface $serializer): JsonResponse
    {
        $users = $userRepository->findAll();
        $users = $serializer->serialize($users, 'json');

        return new JsonResponse($users, 200, [], true);
    }

    #[Route('/me', name: 'user_me', methods: ['GET'])]
    public function getAuthenticatedUser(Security $security, SerializerInterface $serializer): JsonResponse
    {
        $user = $security->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Usuario no autenticado'], 401);
        }

        $userData = $serializer->serialize($user, 'json', ['groups' => ['user:read']]);

        return new JsonResponse($userData, 200, [], true);
    }


    #[Route('/create', name: 'create_user', methods: ['POST'])]
    public function createUser(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['password']) || empty($data['password'])) {
            return new JsonResponse(['error' => 'Password is required'], Response::HTTP_BAD_REQUEST);
        }

        $user = new UserAccount();
        $user->setEmail($data['email']);
        $user->setUsername($data['username']);
        $user->setRol("ROLE_USER");

        $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);

        $entityManager->persist($user);
        $entityManager->flush();

        // 🔹 Devolver el ID del usuario creado junto con el mensaje
        return new JsonResponse([
            'message' => 'User created successfully',
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'username' => $user->getUsername()
        ], Response::HTTP_CREATED);
    }


    #[Route('/update/{id}', name: 'user_update', methods: ['PUT'])]
    public function update(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, int $id): JsonResponse
    {
        $data = $request->getContent();
        // Deserializa el objeto UserAccount
        $user = $serializer->deserialize($data, UserAccount::class, 'json');

        // Encontrar el usuario existente
        $existingUser = $em->getRepository(UserAccount::class)->find($id);

        if (!$existingUser) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        // Verificar si el nombre de usuario está siendo cambiado
        if ($user->getUsername() && $user->getUsername() !== $existingUser->getUsername()) {
            // Verificar si ya existe otro usuario con el mismo nombre de usuario
            $duplicateUser = $em->getRepository(UserAccount::class)->findOneBy(['username' => $user->getUsername()]);

            if ($duplicateUser) {
                // Si existe un usuario con el mismo nombre, retornar un error
                return new JsonResponse(['error' => 'El nombre de usuario ya está en uso'], 400);
            }
        }

        // Si se proporciona una nueva contraseña, se valida y actualiza
        if ($user->getPassword()) {
            // Si deseas validar que la contraseña antigua sea correcta, hazlo aquí
            if ($user->getOldPassword()) {
                // Aquí deberías implementar la lógica para verificar que la contraseña antigua es correcta.
                // Por ejemplo, usando bcrypt:
                if (!password_verify($user->getOldPassword(), $existingUser->getPassword())) {
                    return new JsonResponse(['error' => 'La contraseña antigua no es correcta'], 400);
                }
            }

            // Actualiza la contraseña con la nueva
            $existingUser->setPassword(password_hash($user->getPassword(), PASSWORD_BCRYPT));
        }

        // Solo actualiza los campos si están presentes
        if ($user->getUsername()) {
            $existingUser->setUsername($user->getUsername());
        }
        if ($user->getEmail()) {
            $existingUser->setEmail($user->getEmail());
        }

        // Aquí puedes continuar con la actualización de otros campos, como el cliente (Client)
        if ($user->getClient()) {
            $client = $existingUser->getClient();
            if ($user->getClient()->getPhoneNumber()) {
                $client->setPhoneNumber($user->getClient()->getPhoneNumber());
            }
            if ($user->getClient()->getName()) {
                $client->setName($user->getClient()->getName());
            }
            if ($user->getClient()->getSurname()) {
                $client->setSurname($user->getClient()->getSurname());
            }
            if ($user->getClient()->getAddress()) {
                $client->setAddress($user->getClient()->getAddress());
            }
            $em->persist($client);
        }

        // Persistir el usuario actualizado
        $em->persist($existingUser);
        $em->flush();

        return new JsonResponse('User updated', 200);
    }


    #[Route('/delete/{id}', name: 'user_delete', methods: ['DELETE'])]
    public function delete(UserAccountRepository $userRepository, EntityManagerInterface $em, int $id): JsonResponse
    {
        $user = $userRepository->find($id);

        $em->remove($user);
        $em->flush();

        return new JsonResponse('User deleted', 200, []);
    }

    #[Route('/change-password', name: 'user_change_password', methods: ['PUT'])]
    public function changePassword(
        Request $request,
        Security $security,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $em
    ): JsonResponse {
        $user = $security->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Usuario no autenticado'], 401);
        }

        $data = json_decode($request->getContent(), true);
        $oldPassword = $data['oldPassword'] ?? '';
        $newPassword = $data['newPassword'] ?? '';
        $confirmPassword = $data['confirmPassword'] ?? '';

        // Verificar que la nueva contraseña y la confirmación coincidan
        if ($newPassword !== $confirmPassword) {
            return new JsonResponse(['error' => 'Las contraseñas no coinciden'], 400);
        }

        // Verificar que la contraseña antigua sea correcta
        if (!$passwordHasher->isPasswordValid($user, $oldPassword)) {
            return new JsonResponse(['error' => 'La contraseña antigua es incorrecta'], 400);
        }

        // Encriptar la nueva contraseña
        $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);

        // Establecer la nueva contraseña
        $user->setPassword($hashedPassword);

        // Persistir el usuario con la nueva contraseña
        $em->persist($user);
        $em->flush();

        return new JsonResponse(['message' => 'Contraseña cambiada correctamente'], 200);
    }


}



#despues de copiarlo pruebo a crear uno nuevo con el postman y con la api de api/login/check pruebo a cree el token