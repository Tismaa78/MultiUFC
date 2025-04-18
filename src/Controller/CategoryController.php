<?php

namespace App\Controller;

use App\Entity\Categorie;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/categorie', name: 'app_category_')]
final class CategoryController extends AbstractController
{
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Categorie $categorie): Response
    {
        return $this->render('category/show.html.twig', [
            'categorie' => $categorie,
            'posts' => $categorie->getPosts(),
        ]);
    }
}
