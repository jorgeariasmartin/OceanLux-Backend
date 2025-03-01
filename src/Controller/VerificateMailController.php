<?php
namespace App\Controller;

use App\Entity\UserAccount;
use Doctrine\ORM\EntityManagerInterface;
use Random\RandomException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

#[Route('/api')]
class VerificateMailController extends AbstractController
{
    /**
     * @throws TransportExceptionInterface
     * @throws RandomException
     */
    #[Route('/send-verification-email', name: 'send_verification_email', methods: ['OPTIONS', 'POST'])]
    public function sendVerificationEmail(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        // Manejar solicitud OPTIONS para CORS
        if ($request->isMethod('OPTIONS')) {
            $response = new Response();
            $response->headers->set('Access-Control-Allow-Origin', '*');
            $response->headers->set('Access-Control-Allow-Methods', 'POST, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization');
            return $response;
        }

        // Lógica normal de la API
        $data = json_decode($request->getContent(), true);

        if (!isset($data['user_id'])) {
            return new JsonResponse(['error' => 'User ID is required'], Response::HTTP_BAD_REQUEST);
        }

        $user = $entityManager->getRepository(UserAccount::class)->find($data['user_id']);

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $token = bin2hex(random_bytes(32));
        $expiresAt = new \DateTime('+1 hour');

        $user->setValidationToken($token);
        $user->setExpiresAt($expiresAt);
        $entityManager->flush();

        $frontendUrl = $_ENV['FRONTEND_URL'] ?? 'http://localhost:4200';
        $verificationLink = $frontendUrl . '/user/verify?token=' . $token;

        $email = (new Email())
            ->from('oceanlux.noreply@gmail.com')
            ->to($user->getEmail())
            ->subject('Bienvenido a OceanLux')
            ->html("
                <p>Para empezar, verifique su cuenta pulsando el siguiente enlace:</p>
                <p><a href=\"$verificationLink\">Verificar mi cuenta</a></p>
                <p>El enlace expirará en una hora.</p>
                <p>Si no ha solicitado este correo, por favor ignore este mensaje.</p>
                <p>Gracias por confiar en OceanLux.</p>
            ");

        try {
            $mailer->send($email);
            $response = new JsonResponse(['message' => 'Verification email sent'], Response::HTTP_OK);
        } catch (TransportExceptionInterface $e) {
            $response = new JsonResponse(['error' => 'Email could not be sent'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Agregar cabeceras CORS a la respuesta
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }

    #[Route('/user/verify', name: 'verify_email', methods: ['GET'])]
    public function verifyEmail(Request $request, EntityManagerInterface $entityManager): Response
    {
        $token = $request->query->get('token');

        if (!$token) {
            return new JsonResponse(['error' => 'Token is required'], Response::HTTP_BAD_REQUEST);
        }

        $user = $entityManager->getRepository(UserAccount::class)->findOneBy(['validationToken' => $token]);

        if (!$user) {
            return new JsonResponse(['error' => 'Invalid token'], Response::HTTP_NOT_FOUND);
        }

        // Comprobar si el token ha expirado
        if ($user->getExpiresAt() < new \DateTime()) {
            return new JsonResponse(['error' => 'Token has expired'], Response::HTTP_BAD_REQUEST);
        }

        // Marcar al usuario como verificado
        $user->setIsVerified(true);
        $user->setValidationToken(null); // Eliminar el token después de la verificación
        $user->setExpiresAt(null); // También eliminamos la expiración
        $entityManager->flush();

        return new JsonResponse(['message' => 'User successfully verified'], Response::HTTP_OK);
    }


}
