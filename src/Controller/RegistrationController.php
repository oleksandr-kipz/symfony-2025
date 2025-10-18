<?php

namespace App\Controller;

use App\Service\RequestCheckerService;
use App\Service\User\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
final class RegistrationController extends AbstractController
{

    public const REGISTRATION_USER_DATA = [
        "email",
        "password"
    ];

    /**
     * @param EntityManagerInterface $entityManager
     * @param RequestCheckerService $requestCheckerService
     * @param UserService $userService
     */
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly RequestCheckerService  $requestCheckerService,
        private readonly UserService            $userService
    ) {}

    /**
     * @throws Exception
     */
    #[Route('/registration', name: 'app_registration', methods: ['POST'])]
    public function index(Request $request): JsonResponse
    {
        $requestBody = json_decode($request->getContent(), true);

        $this->requestCheckerService->check($requestBody, self::REGISTRATION_USER_DATA);

        $user = $this->userService->createUser($requestBody['email'], $requestBody['password']);

        $this->entityManager->flush();

        return $this->json(["userId" => $user->getId()]);
    }

}
