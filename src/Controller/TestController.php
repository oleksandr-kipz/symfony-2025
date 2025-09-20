<?php

namespace App\Controller;

use App\Entity\Dishes;
use App\Entity\Menu;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Routing\Attribute\Route;

final class TestController extends AbstractController
{

    public function __construct(private EntityManagerInterface $entityManager) {}


    #[Route('/test', name: 'app_test', methods: ['POST'])]
    public function index(Request $request): JsonResponse
    {
        $requestBody = json_decode($request->getContent(), true);

        $menu = new Menu();

        $menu->setType($requestBody['type']);

        foreach ($requestBody['dishes'] as $dish) {
            $dishes = new Dishes();

            $dishes->setName($dish['name']);

            $menu->addDishes($dishes);
        }

        $this->entityManager->persist($menu);
        $this->entityManager->flush();

        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path'    => 'src/Controller/TestController.php',
        ]);
    }

    #[Route('/test2', name: 'app_test2', methods: ['POST'])]
    public function index2(Request $request): JsonResponse
    {
        $requestBody = json_decode($request->getContent(), true);

        /** @var Menu $menu */
        $menu = $this->entityManager->getRepository(Menu::class)->findOneBy(['id' => $requestBody['menuId']]);

        if (!$menu) {
            throw new UnprocessableEntityHttpException("Not found menu");
        }

        $dishes = new Dishes();

        $dishes
            ->setName($requestBody['name'])
            ->setMenu($menu);

        $this->entityManager->persist($dishes);
        $this->entityManager->flush();

        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path'    => 'src/Controller/TestController.php',
        ]);
    }

}
