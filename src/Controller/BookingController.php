<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Trip;
use App\Entity\UserAccount;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Enum\BookingStatus;


#[Route('api/booking')]
final class BookingController extends AbstractController
{
    #[Route('/create', name: 'app_booking', methods: ['POST'])]
    public function createBooking(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Validar datos requeridos
        if (!isset($data['booking_date'], $data['number_of_guest'], $data['total_price'], $data['user_id'], $data['trip_id'])) {
            return new JsonResponse(['error' => 'Missing required fields'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Buscar Usuario y Viaje en la base de datos
        $user = $entityManager->getRepository(UserAccount::class)->find($data['user_id']);
        $trip = $entityManager->getRepository(Trip::class)->find($data['trip_id']);

        if (!$user || !$trip) {
            return new JsonResponse(['error' => 'User or Trip not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Crear una nueva reserva con estado "pendiente" (en valor int = 0)
        $booking = new Booking();
        $booking->setBookingDate(new \DateTime($data['booking_date']));
        $booking->setNumberOfGuest((int) $data['number_of_guest']);
        $booking->setTotalPrice((float) $data['total_price']);
        $booking->setUserId($user);
        $booking->setTripId($trip);
        $booking->setStatus(BookingStatus::PENDING); // Estado pendiente
        $booking->setRate(0);  // Asegúrate de que rate esté establecido en 0 si no se proporciona

        // Guardar en la base de datos
        $entityManager->persist($booking);
        $entityManager->flush();

        return new JsonResponse([
            'message' => 'Booking created successfully',
            'booking_id' => $booking->getId(),
            'status' => $booking->getStatus()->value, // Esto devolverá el valor int (0)
            'rate' => $booking->getRate()  // Esto devolverá el valor rate (por defecto 0)
        ], JsonResponse::HTTP_CREATED);
    }

    #[Route('/pending/{userId}', name: 'app_booking_pending_user', methods: ['GET'])]
    public function getPendingReservations(int $userId, EntityManagerInterface $entityManager): JsonResponse
    {
        // Obtener todas las reservas pendientes para un usuario
        $reservations = $entityManager->getRepository(Booking::class)
            ->findBy(['user_id' => $userId, 'status' => BookingStatus::PENDING]);

        // Transformar las reservas a un formato adecuado para enviar al frontend
        $reservationsData = array_map(function ($reservation) {
            return [
                'id' => $reservation->getId(),
                'booking_date' => $reservation->getBookingDate()->format('Y-m-d H:i:s'),
                'number_of_guest' => $reservation->getNumberOfGuest(),
                'total_price' => $reservation->getTotalPrice(),
                'status' => $reservation->getStatus(),
                'rate' => $reservation->getRate(),
                'trip_id' => $reservation->getTripId()->getId(),
            ];
        }, $reservations);

        return new JsonResponse($reservationsData);
    }

    #[Route('/delete/{id}', name: 'app_booking_delete', methods: ['DELETE'])]
    public function deleteBooking(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        // Buscar la reserva por su id
        $booking = $entityManager->getRepository(Booking::class)->find($id);

        // Verificar si la reserva existe
        if (!$booking) {
            return new JsonResponse(['error' => 'Booking not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Actualizar el estado de la reserva a CANCELLED (2)
        $booking->setStatus(BookingStatus::CANCELLED);

        // Persistir los cambios en la base de datos
        $entityManager->persist($booking);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Booking status updated to CANCELLED'], JsonResponse::HTTP_OK);
    }
    #[Route('/update-status/{id}', name: 'app_booking_update_status', methods: ['PUT'])]
    public function updateBookingStatus(int $id, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        // Buscar la reserva por su id
        $booking = $entityManager->getRepository(Booking::class)->find($id);

        // Verificar si la reserva existe
        if (!$booking) {
            return new JsonResponse(['error' => 'Booking not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Decodificar el JSON recibido
        $data = json_decode($request->getContent(), true);

        // Validar que se reciba el estado
        if (!isset($data['status'])) {
            return new JsonResponse(['error' => 'Missing status field'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Actualizar el estado de la reserva
        $booking->setStatus(BookingStatus::from($data['status']));

        // Guardar los cambios
        $entityManager->persist($booking);
        $entityManager->flush();

        return new JsonResponse([
            'message' => 'Booking status updated successfully',
            'id' => $booking->getId(),
            'new_status' => $booking->getStatus()
        ], JsonResponse::HTTP_OK);
    }

    #[Route('/confirmed/{userId}', name: 'app_booking_confirmed_user', methods: ['GET'])]
    public function getConfirmedReservations(int $userId, EntityManagerInterface $entityManager): JsonResponse
    {
        // Obtener todas las reservas confirmadas para un usuario
        $reservations = $entityManager->getRepository(Booking::class)
            ->findBy(['user_id' => $userId, 'status' => BookingStatus::CONFIRMED]);

        // Transformar las reservas a un formato adecuado para enviar al frontend
        $reservationsData = array_map(function ($reservation) {
            return [
                'id' => $reservation->getId(),
                'booking_date' => $reservation->getBookingDate()->format('Y-m-d H:i:s'),
                'number_of_guest' => $reservation->getNumberOfGuest(),
                'total_price' => $reservation->getTotalPrice(),
                'status' => $reservation->getStatus(),
                'rate' => $reservation->getRate(),
                'trip_id' => $reservation->getTripId()->getId(),
            ];
        }, $reservations);

        return new JsonResponse($reservationsData);
    }

}
