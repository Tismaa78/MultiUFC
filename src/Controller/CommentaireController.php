<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Form\CommentaireType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Cookie;

#[Route('/commentaire')]
class CommentaireController extends AbstractController
{
    #[Route('/{id}', name: 'app_commentaire_delete', methods: ['POST'])]
    public function delete(Request $request, Commentaire $commentaire, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser() !== $commentaire->getUser()) {
            $this->addFlash('danger', 'Tu ne peux supprimer que tes propres commentaires.');
            return $this->redirectToRoute('app_post_show', ['id' => $commentaire->getPost()->getId()]);
        }

        if ($this->isCsrfTokenValid('delete_comment_' . $commentaire->getId(), $request->request->get('_token'))) {
            $entityManager->remove($commentaire);
            $entityManager->flush();

            $this->addFlash('danger', 'Commentaire supprimé.');
        }

        return $this->redirectToRoute('app_post_show', [
            'id' => $commentaire->getPost()->getId(),
        ]);
    }

    #[Route('/{id}/reply', name: 'app_commentaire_reply', methods: ['GET', 'POST'])]
    public function reply(Request $request, Commentaire $commentaire, EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('warning', 'Connecte-toi pour répondre.');
            return $this->redirectToRoute('app_login');
        }

        $reply = new Commentaire();
        $form = $this->createForm(CommentaireType::class, $reply);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reply->setUser($this->getUser());
            $reply->setPost($commentaire->getPost());
            $reply->setParent($commentaire);
            $reply->setIsReply(true);
            $reply->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($reply);
            $entityManager->flush();

            $this->addFlash('success', 'Réponse ajoutée.');
            return $this->redirectToRoute('app_post_show', [
                'id' => $commentaire->getPost()->getId(),
            ]);
        }

        return $this->render('commentaire/reply.html.twig', [
            'form' => $form,
            'commentaire' => $commentaire,
        ]);
    }

    #[Route('/{id}/like', name: 'app_commentaire_like', methods: ['POST'])]
    public function like(Commentaire $commentaire, Request $request, EntityManagerInterface $entityManager): Response
    {
        $token = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('like_comment_' . $commentaire->getId(), $token)) {
            return new Response('Invalid CSRF token', 400);
        }
    
        $cookieName = 'liked_comment_' . $commentaire->getId();
    
        if ($request->cookies->has($cookieName)) {
            return new Response('Already liked', 200);
        }
    
        $commentaire->setLikes($commentaire->getLikes() + 1);
        $entityManager->flush();
    
        $response = new Response('ok');
        $response->headers->setCookie(new Cookie($cookieName, true, strtotime('+1 year')));
    
        return $response;
    }    
}
