<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Plat;
use App\Repository\CommandeRepository;
use App\Repository\PlatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/commandes', name: 'api_commandes_')]
class CommandeController extends AbstractController
{
    #[Route('/create', name: 'create', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $entityManager,
        PlatRepository $platRepository
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        // Vérification des données requises
        if (!isset($data['userId']) || !isset($data['platId']) || !isset($data['quantite'])) {
            return $this->json(['message' => 'Données manquantes'], Response::HTTP_BAD_REQUEST);
        }

        // Récupération du plat
        $plat = $platRepository->find($data['platId']);
        if (!$plat) {
            return $this->json(['message' => 'Plat non trouvé'], Response::HTTP_NOT_FOUND);
        }

        // Création de la commande
        $commande = new Commande();
        $commande->setUserId($data['userId']);
        $commande->setPlat($plat);
        $commande->setQuantite($data['quantite']);

        $entityManager->persist($commande);
        $entityManager->flush();

        return $this->json([
            'id' => $commande->getId(),
            'numeroTicket' => $commande->getNumeroTicket(),
            'userId' => $commande->getUserId(),
            'plat' => [
                'id' => $plat->getId(),
                'nom' => $plat->getNom(),
                'prix' => $plat->getPrix()
            ],
            'quantite' => $commande->getQuantite(),
            'statut' => $commande->getStatut(),
            'dateCommande' => $commande->getDateCommande()->format('Y-m-d H:i:s')
        ], Response::HTTP_CREATED);
    }

    #[Route('/user/{userId}', name: 'list_by_user', methods: ['GET'])]
    public function listByUser(string $userId, CommandeRepository $commandeRepository): JsonResponse
    {
        $commandes = $commandeRepository->findByUserId($userId);
        $commandesData = [];

        foreach ($commandes as $commande) {
            $plat = $commande->getPlat();
            $commandesData[] = [
                'id' => $commande->getId(),
                'numeroTicket' => $commande->getNumeroTicket(),
                'plat' => [
                    'id' => $plat->getId(),
                    'nom' => $plat->getNom(),
                    'prix' => $plat->getPrix()
                ],
                'quantite' => $commande->getQuantite(),
                'statut' => $commande->getStatut(),
                'dateCommande' => $commande->getDateCommande()->format('Y-m-d H:i:s')
            ];
        }

        return $this->json($commandesData);
    }

    #[Route('/{id}/status', name: 'update_status', methods: ['PATCH'])]
    public function updateStatus(
        Commande $commande,
        Request $request,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['statut'])) {
            return $this->json(['message' => 'Statut manquant'], Response::HTTP_BAD_REQUEST);
        }

        $commande->setStatut($data['statut']);
        $entityManager->flush();

        return $this->json([
            'id' => $commande->getId(),
            'statut' => $commande->getStatut()
        ]);
    }
}
