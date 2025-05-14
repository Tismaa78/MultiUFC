<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Commentaire;
use App\Form\PostType;
use App\Form\CommentaireType;
use App\Form\MonCompteType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/post')]
final class PostController extends AbstractController
{
    #[Route(name: 'app_post_index', methods: ['GET'])]
    public function index(PostRepository $postRepository): Response
    {
        return $this->render('post/index.html.twig', [
            'posts' => $postRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_post_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('warning', 'Vous devez être connecté pour créer un article.');
            return $this->redirectToRoute('app_login');
        }

        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();
                try {
                    $imageFile->move($this->getParameter('uploads_directory'), $newFilename);
                    $post->setFeaturedImages('/uploads/' . $newFilename);
                } catch (FileException $e) {
                    $this->addFlash('danger', 'Erreur lors de l\'upload de l\'image.');
                }
            }

            $post->setUser($this->getUser());
            $post->setCreatedAt(new \DateTimeImmutable());

            if ($post->getLikes() === null) {
                $post->setLikes(0);
            }

            $entityManager->persist($post);
            $entityManager->flush();

            $this->addFlash('success', 'Article publié avec succès !');
            return $this->redirectToRoute('app_post_index');
        }

        return $this->render('post/new.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/mes-articles', name: 'app_post_my', methods: ['GET', 'POST'])]
    public function myPosts(
        Request $request,
        PostRepository $postRepository,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $user = $this->getUser();
    
        if (!$user) {
            $this->addFlash('warning', 'Vous devez être connecté pour accéder à vos articles.');
            return $this->redirectToRoute('app_login');
        }
    
        $form = $this->createForm(MonCompteType::class, $user);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $currentPassword = $form->get('currentPassword')->getData();
            $plainPassword = $form->get('plainPassword')->getData();
            
            if (!empty($plainPassword)) {
                if (!$passwordHasher->isPasswordValid($user, $currentPassword)) {
                    $this->addFlash('danger', 'Le mot de passe actuel est incorrect.');
                    return $this->redirectToRoute('app_post_my');
                }
            
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
            }
            
        
            $entityManager->flush();
            $this->addFlash('success', 'Vos informations ont bien été mises à jour.');
            return $this->redirectToRoute('app_post_my');
        }
        
    
        $posts = $postRepository->findBy(['user' => $user]);
    
        return $this->render('post/my_posts.html.twig', [
            'posts' => $posts,
            'user_form' => $form->createView(),
        ]);
    }
    
    #[Route('/articles-populaires', name: 'app_post_popular')]
    public function popular(PostRepository $postRepository): Response
    {
        $posts = $postRepository->findBy([], ['likes' => 'DESC']);

        return $this->render('post/popular.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/articles-recents', name: 'app_post_recents')]
    public function recentList(PostRepository $postRepository): Response
    {
        $recentPosts = $postRepository->findBy([], ['createdAt' => 'DESC']);

        return $this->render('post/recents.html.twig', [
            'posts' => $recentPosts,
        ]);
    }

    #[Route('/{id}', name: 'app_post_show', methods: ['GET', 'POST'])]
    public function show(Post $post, Request $request, EntityManagerInterface $entityManager): Response
    {
        $commentaire = new Commentaire();
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commentaire->setPost($post);
            $commentaire->setUser($this->getUser());
            $commentaire->setCreatedAt(new \DateTimeImmutable());
            $commentaire->setLikes(0);

            $entityManager->persist($commentaire);
            $entityManager->flush();

            $this->addFlash('success', 'Commentaire ajouté !');
            return $this->redirectToRoute('app_post_show', ['id' => $post->getId()]);
        }

        return $this->render('post/show.html.twig', [
            'post' => $post,
            'comment_form' => $form->createView(),
            'commentaires' => $post->getCommentaires(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_post_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser() !== $post->getUser()) {
            $this->addFlash('danger', 'Tu ne peux modifier que tes propres articles.');
            return $this->redirectToRoute('app_post_index');
        }

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();
                try {
                    $imageFile->move($this->getParameter('uploads_directory'), $newFilename);
                    $post->setFeaturedImages('/uploads/' . $newFilename);
                } catch (FileException $e) {
                    $this->addFlash('danger', 'Erreur lors de l\'upload de l\'image.');
                }
            }

            $entityManager->flush();

            $this->addFlash('success', 'Article modifié avec succès !');
            return $this->redirectToRoute('app_post_index');
        }

        return $this->render('post/edit.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_post_delete', methods: ['POST'])]
    public function delete(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser() !== $post->getUser()) {
            $this->addFlash('danger', 'Tu ne peux supprimer que tes propres articles.');
            return $this->redirectToRoute('app_post_index');
        }

        if ($this->isCsrfTokenValid('delete' . $post->getId(), $request->request->get('_token'))) {
            $entityManager->remove($post);
            $entityManager->flush();

            $this->addFlash('danger', 'Article supprimé.');
        }

        return $this->redirectToRoute('app_post_index');
    }

    #[Route('/{id}/like', name: 'app_post_like', methods: ['POST'])]
    public function like(Post $post, Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if (!$this->isCsrfTokenValid('like_post_' . $post->getId(), $request->request->get('_token'))) {
            $this->addFlash('danger', 'Jeton CSRF invalide.');
            return $this->redirectToRoute('app_post_show', ['id' => $post->getId()]);
        }

        $post->setLikes($post->getLikes() + 1);
        $entityManager->flush();

        return $this->redirectToRoute('app_post_show', ['id' => $post->getId()]);
    }
}
