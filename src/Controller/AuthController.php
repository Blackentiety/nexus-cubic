<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AuthController extends AbstractController
{
    public function __construct(private UserRepository $userRepository){}

    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function registerUser(Request $request, EntityManagerInterface $em): Response
    {
        $data = json_decode($request->getContent(), true);
        if (!$data){
            return $this->json([
                'status' => 'error',
                'message' => 'Invalid JSON'
            ]);
        }
        $user = new User();
        $user->setEmail($data['email']);
        $user->setPassword(md5($data['password']));
        $user->setRole('ROLE_USER');
        $user->setPseudoMinecraft($data['pseudoMinecraft']);
        $user->setUuidMinecraft($data['uuidMinecraft']);
        $user->setCredits(0.0);
        $user->setDateInscription(new \DateTime());
        $token = md5(uniqid());
        $user->setToken($token);
        $em->persist($user);
        $em->flush();

        return $this->json([
            'status' => 'success',
            'token' => $token,
            'userId' => $user->getId(),
            'PseudoMinecraft' => $user->getPseudoMinecraft()
        ]);
    }

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function loginUser(Request $request, EntityManagerInterface $em): Response
    {
        $data = json_decode($request->getContent(), true);
        if (!$data){
            return $this->json([
                'status' => 'error',
                'message' => 'Invalid JSON'
            ]);
        }
        $user = $this->userRepository->findOneBy(['email' => $data['email']]);
        if (!$user){
            return $this->json([
                'status' => 'error',
                'message' => 'User not found'
            ]);
        }
        if (md5($data['password']) != $user->getPassword()){
            return $this->json([
                'status' => 'error',
                'message' => 'Wrong password'
            ]);
        }
        $token = md5(uniqid());
        $user->setToken($token);
        $em->persist($user);
        $em->flush();
        return $this->json([
            'status' => 'success',
            'token' => $token,
        ]);
    }

    #[Route('/api/me', name: 'api_me', methods: ['GET'])]
    public function me(Request $request, EntityManagerInterface $em): Response
    {
        $token = $request->headers->get('Authorization');
        if (!$token){
            return $this->json([
                'status' => 'error',
                'message' => 'Token not provided'
            ]);
        }
        $token = substr($token, 7);
        $user = $this->userRepository->findOneBy(['token' => $token]);
        if (!$user){
            return $this->json([
                'status' => 'error',
                'message' => 'User not found'
            ]);
        }
        return $this->json([
            'status' => 'success',
            'token' => $token,
            'userId' => $user->getId(),
            'PseudoMinecraft' => $user->getPseudoMinecraft(),
            'credits' => $user->getCredits(),
            'uuidMinecraft' => $user->getUuidMinecraft(),
            'dateInscription' => $user->getDateInscription(),
            'email' => $user->getEmail(),
        ]);
    }

    #[Route('/api/me', name: 'api_me_update', methods: ['PUT'])]
    public function updateUser(Request $request, EntityManagerInterface $em): Response
    {
        $token = $request->headers->get('Authorization');
        if (!$token){
            return $this->json([
                'status' => 'error',
                'message' => 'Token not provided'
            ]);
        }
        $token = substr($token, 7);
        $data = json_decode($request->getContent(), true);
        if (!$data){
            return $this->json([
                'status' => 'error',
                'message' => 'Invalid JSON'
            ]);
        }
        $user = $this->userRepository->findOneBy(['token' => $token]);
        if (!$user){
            return $this->json([
                'status' => 'error',
                'message' => 'User not found'
            ]);
        }
        $user->setPseudoMinecraft($data['pseudoMinecraft']);
        $em->persist($user);
        $em->flush();
        return $this->json([
            'status' => 'success',
            'token' => $token,
        ]);
    }

    #[Route('/auth', name: 'app_auth')]
    public function index(): Response
    {
        return $this->render('auth/index.html.twig', [
            'controller_name' => 'AuthController',
        ]);
    }
}
