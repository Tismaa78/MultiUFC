<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{

    #[Route('/', name: 'app_home')]
    public function index(PostRepository $postRepository): Response
    {
        $posts = $postRepository->findBy([], ['createdAt' => 'DESC']);
        $topPosts = $postRepository->findBy([], ['likes' => 'DESC'], 15);
        $recentPosts = array_slice($posts, 0, 15);
    
        // 🎯 Article aléatoire
        $allPosts = $postRepository->findAll();
        $featuredPost = count($allPosts) > 0 ? $allPosts[array_rand($allPosts)] : null;
    
        return $this->render('home/index.html.twig', [
            'posts' => $posts,
            'topPosts' => $topPosts,
            'recentPosts' => $recentPosts,
            'featuredPost' => $featuredPost,
        ]);
    }
    

}
