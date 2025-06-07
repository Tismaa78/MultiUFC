<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('', name: 'app_admin')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $users = $entityManager->getRepository(Utilisateur::class)->findAll();
        $posts = $entityManager->getRepository(Post::class)->findAll();

        return $this->render('admin/index.html.twig', [
            'users' => $users,
            'posts' => $posts,
        ]);
    }

    #[Route('/toggle-role/{id}', name: 'app_admin_toggle_role')]
    public function toggleRole(Utilisateur $user, EntityManagerInterface $entityManager): Response
    {
        if ($user->getRole() === 'ROLE_ADMIN') {
            $user->setRole('ROLE_USER');
        } else {
            $user->setRole('ROLE_ADMIN');
        }

        $entityManager->flush();

        $this->addFlash('success', 'Le rôle de l\'utilisateur a été modifié avec succès.');
        return $this->redirectToRoute('app_admin');
    }

    #[Route('/delete-post/{id}', name: 'app_admin_delete_post')]
    public function deletePost(Post $post, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($post);
        $entityManager->flush();

        $this->addFlash('success', 'L\'article a été supprimé avec succès.');
        return $this->redirectToRoute('app_admin');
    }
} 