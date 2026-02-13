<?php

namespace App\Controller;

use App\Entity\Ban;
use App\Repository\BanRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SanctionsController extends AbstractController
{
    public function __construct(private BanRepository $banRepository, private UserRepository $userRepository){}
    #[Route('/api/admin/ban', name: 'app_sanctions_ban', methods: ['POST'])]
    public function addBan(Request $request, EntityManagerInterface $em): Response
    {
        $token = $request->headers->get('Authorization');
        if (!$token) {
            return $this->json([
                'status' => 'error',
                'message' => 'Sorry, you must authenticate to add a ban.',
                ]);
        }
        $token = substr($token, 7);
        $user = $this->userRepository->findOneBy(['token' => $token]);
        if (!$user) {
            return $this->json([
                'status' => 'error',
                'message' => 'user not found',
            ]);
        }
        if ($user->getRole() !== 'ROLE_ADMIN') {
            return $this->json([
                'status' => 'error',
                'message' => 'You are not allowed to add a ban',
            ]);
        }
        $datas = json_decode($request->getContent(), true);
        if (!$datas) {
            return $this->json([
                'status' => 'error',
                'message' => 'Bad JSON',
            ]);
        }

        $ban = new Ban();
        $ban->setUserCible($datas['userCible']);
        $ban->setRaison($datas['raison']);
        $ban->setDateFin(new \DateTime($datas['dateFin']));
        $ban->setIsActive(true);
        $cible = $this->userRepository->findOneBy(['pseudoMinecraft' => $datas['userCible']]);
        if (!$cible) {
            return $this->json([
                'status' => 'error',
                'message' => 'User not found',
            ]);
        }
        return $this->json([]);
    }
}
