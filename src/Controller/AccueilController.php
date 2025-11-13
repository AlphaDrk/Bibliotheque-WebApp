<?php

namespace App\Controller;

use App\Repository\LivreRepository;
use App\Repository\AuteurRepository;
use App\Repository\CategorieRepository;
use App\Repository\EditeurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class AccueilController extends AbstractController
{
    #[Route('/', name: 'app_accueil')]
    public function index(
        Request $request,
        LivreRepository $livreRepository,
        AuteurRepository $auteurRepository,
        CategorieRepository $categorieRepository,
        EditeurRepository $editeurRepository
    ): Response {
        // terme pour pré-remplir la barre si besoin
        $q = $request->query->getString('q');
        // Récupération des statistiques
        $totalLivres = $livreRepository->count([]);
        $totalAuteurs = $auteurRepository->count([]);
        $totalCategories = $categorieRepository->count([]);
        $totalEditeurs = $editeurRepository->count([]);
        
        // Récupération des derniers livres
        $derniersLivres = $livreRepository->findBy([], ['id' => 'DESC'], 5);
        // Liste des catégories et éditeurs pour la page d'accueil
        $categories = $categorieRepository->findAll();
        $editeurs = $editeurRepository->findAll();
        
        // Calcul de la valeur totale du stock
        $valeurStock = 0;
        $livres = $livreRepository->findAll();
        foreach ($livres as $livre) {
            $valeurStock += $livre->getQte() * $livre->getPu();
        }

        return $this->render('accueil/index.html.twig', [
            'totalLivres' => $totalLivres,
            'totalAuteurs' => $totalAuteurs,
            'totalCategories' => $totalCategories,
            'totalEditeurs' => $totalEditeurs,
            'derniersLivres' => $derniersLivres,
            'categories' => $categories,
            'editeurs' => $editeurs,
            'valeurStock' => $valeurStock,
            'q' => $q,
        ]);
    }

    // Endpoint JSON pour l'auto-complétion de la barre globale
    #[Route('/search', name: 'app_search_suggestions', methods: ['GET'])]
    public function searchSuggestions(
        Request $request,
        LivreRepository $livreRepository,
        AuteurRepository $auteurRepository,
        EditeurRepository $editeurRepository
    ): JsonResponse {
        $q = trim($request->query->get('q', ''));
        if ($q === '') {
            return $this->json(['items' => []]);
        }

        $items = [];

        // Livres
        $livres = $livreRepository->createQueryBuilder('l')
            ->leftJoin('l.editeur', 'e')->addSelect('e')
            ->leftJoin('l.auteurs', 'a')->addSelect('a')
            ->andWhere('l.titre LIKE :t OR l.isbn LIKE :t OR e.nom LIKE :t OR a.nom LIKE :t OR a.prenom LIKE :t')
            ->setParameter('t', '%'.$q.'%')
            ->setMaxResults(5)
            ->orderBy('l.titre', 'ASC')
            ->getQuery()->getResult();
        foreach ($livres as $livre) {
            $label = $livre->getTitre();
            if ($livre->getEditeur()) {
                $label .= ' — '.$livre->getEditeur()->getNom();
            }
            $items[] = [
                'type' => 'Livre',
                'label' => $label,
                'url' => $this->generateUrl('app_livre_show', ['id' => $livre->getId()]),
            ];
        }

        // Auteurs
        $auteurs = $auteurRepository->createQueryBuilder('au')
            ->andWhere('au.nom LIKE :t OR au.prenom LIKE :t')
            ->setParameter('t', '%'.$q.'%')
            ->setMaxResults(5)
            ->orderBy('au.nom', 'ASC')
            ->getQuery()->getResult();
        foreach ($auteurs as $auteur) {
            $items[] = [
                'type' => 'Auteur',
                'label' => $auteur->getPrenom().' '.$auteur->getNom(),
                'url' => $this->generateUrl('app_auteur_show', ['id' => $auteur->getId()]),
            ];
        }

        // Éditeurs
        $editeurs = $editeurRepository->createQueryBuilder('ed')
            ->andWhere('ed.nom LIKE :t OR ed.pays LIKE :t OR ed.adresse LIKE :t OR ed.telephone LIKE :t')
            ->setParameter('t', '%'.$q.'%')
            ->setMaxResults(5)
            ->orderBy('ed.nom', 'ASC')
            ->getQuery()->getResult();
        foreach ($editeurs as $editeur) {
            $items[] = [
                'type' => 'Éditeur',
                'label' => $editeur->getNom(),
                'url' => $this->generateUrl('app_editeur_show', ['id' => $editeur->getId()]),
            ];
        }

        return $this->json(['items' => $items]);
    }
}
