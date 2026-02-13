<?php

namespace App\Controller;

use App\Entity\Item;
use App\Repository\ItemRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ShopController extends AbstractController
{
    public function __construct(private ItemRepository $itemRepository, private UserRepository $userRepository){}

    #[Route('/api/shop', name: 'api_shop_all', methods: ['GET'])]
    public function getAllItems(): Response
    {
        $items = $this->itemRepository->findAll();
        if (!$items) {
            return $this->json([
                'status' => 'error',
                'message' => 'No items found',
            ]);
        }
        return $this->json(['status' => 'success', 'result'=>$items]);
    }
    #[Route('/api/shop/buy/{id}', name: 'api_shop_buy', methods: ['POST'])]
    public function buyItem(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $item = $this->itemRepository->find($id);
        if (!$item) {
            return $this->json([
                'status' => 'error',
                'message' => 'Item not found',
            ]);
        }
        $token = $request->headers->get('Authorization');
        if (!$token) {
            return $this->json([
                'status' => 'error',
                'message' => 'Authorization header not found',
            ]);
        }
        $token = substr($token, 7);
        $user = $this->userRepository->findOneBy(['token' => $token]);
        if (!$user) {
            return $this->json([
                'status' => 'error',
                'message' => 'User not found',
            ]);
        }
        $credit = $user->getCredits();
        $price = $item->getPrix();
        if ($price > $credit) {
            return $this->json([
                'status' => 'error',
                'message' => 'You don\'t have enough credit'
            ]);
        }
        $user->setCredits($credit - $price);
        $user->addItem($item);
        $em->persist($user);
        $em->flush();
        $message = $user->getPseudoMinecraft() . ' a acheter ' . $item->getNom() . ' pour ' . $price . ' Cubics';
        return $this->json([
            'status' => 'success',
            'message' => $message,
        ]);
    }

    #[Route('/api/inventory', name: 'api_shop_inventory', methods: ['GET'])]
    public function getUserInventory(Request $request, EntityManagerInterface $em): Response
    {
        $token = $request->headers->get('Authorization');
        if (!$token) {
            return $this->json([
                'status' => 'error',
                'message' => 'Authorization header not found',
            ]);
        }
        $token = substr($token, 7);
        $user = $this->userRepository->findOneBy(['token' => $token]);
        if (!$user) {
            return $this->json([
                'status' => 'error',
                'message' => 'User not found',
            ]);
        }
        $inventory = $user->getItems();
        if (!$inventory) {
            return $this->json([
                'status' => 'error',
                'message' => 'Inventory not found',
            ]);
        }
        return $this->json(['status' => 'success', 'result'=>$inventory]);
    }

}
