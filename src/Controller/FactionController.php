<?php

namespace App\Controller;

use App\Entity\Faction;
use App\Repository\FactionRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FactionController extends AbstractController
{
    public function __construct(private FactionRepository $factionRepository, private UserRepository $userRepository){}
    #[Route('/api/faction', name: 'api_faction_create', methods: ['POST'])]
    public function createFaction(Request $request, EntityManagerInterface $em): Response
    {
        $token = $request->headers->get('Authorization');
        if (!$token){
            return $this->json([
                'error' => 'error',
                'message' => 'Unauthorized'
            ]);
        }
        $token = substr($token, 7);
        $user = $this->userRepository->findOneBy(['token' => $token]);
        if (!$user) {
            return $this->json([
                'error' => 'error',
                'message' => 'User not found'
            ]);
        }
        $datas = json_decode($request->getContent(), true);
        if (!$datas) {
            return $this->json([
                'error' => 'error',
                'message' => 'Invalid JSON'
            ]);
        }
        $price = 1000;
        $credit = $user->getCredits();
        if ($price > $credit) {
            return $this->json([
                'error' => 'error',
                'message' => 'Insufficient credits'
            ]);
        }
        $faction = new Faction();
        $faction->setNom($datas['nom']);
        $faction->setDescription($datas['description']);
        $faction->setPower(10);
        $faction->setIdCreator($user->getId());
        $user->setCredits($credit - $price);
        $em->persist($faction);
        $em->persist($user);
        $em->flush();
        return $this->json([
            'status' => 'success',
            'data' => $faction,
        ]);
    }
}
