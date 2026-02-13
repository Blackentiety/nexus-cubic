<?php

namespace App\Controller;

use App\Repository\ItemRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ShopController extends AbstractController
{
    public function __construct(private ItemRepository $itemRepository){}

    #[Route('/api/shop', name: 'api_shop_index', methods: ['GET'])]
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
    #[Route('/shop', name: 'app_shop')]
    public function index(): Response
    {
        return $this->render('shop/index.html.twig', [
            'controller_name' => 'ShopController',
        ]);
    }
}
