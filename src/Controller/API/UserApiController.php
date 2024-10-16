<?php

namespace App\Controller\API;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\JwtTokenManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserApiController extends AbstractController
{
    private $jwtTokenManager;

    public function __construct(JwtTokenManager $jwtTokenManager)
    {
        $this->jwtTokenManager = $jwtTokenManager;
    }

    #[Route("/api/users", methods: "POST")]
    public function create(#[MapRequestPayload(serializationContext: [
        'groups' => ['users.create']
    ])] User $user, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $em)
    {
        $user->setPassword($userPasswordHasher->hashPassword($user, $user->getPassword()));
        $em->persist($user);
        $em->flush();
        return $this->json($user, 200, [], [
            'groups' => ['users.show']
        ]);
    }

    #[Route("/api/users/login", methods: "POST")]
    public function login(Request $request, UserRepository $repository,  UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $em)
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        $user = $repository->findOneBy(['email' => $email]);
        if (!$user || !$userPasswordHasher->isPasswordValid($user, $password)) {
            return new JsonResponse(['message' => 'Invalid credentials'], Response::HTTP_UNAUTHORIZED);
        }

        $claims = [
            'userId' => $user->getId(),
            // 'email' => $user->getEmail(),
        ];
        $token = $this->jwtTokenManager->createToken($claims, 3600);

        // Generate token and update database
        $user->setApiToken($token->toString());
        $em->persist($user);
        $em->flush();

        return new JsonResponse(['token' => $user->getApiToken()]);
    }
}
