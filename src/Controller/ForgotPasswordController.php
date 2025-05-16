<?php

namespace App\Controller;

use App\Form\ForgotPasswordType;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ForgotPasswordController extends AbstractController
{
    #[Route('/mot-de-passe-oublie', name: 'app_forgot_password')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ForgotPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();

            $user = $entityManager->getRepository(Utilisateur::class)->findOneBy(['email' => $email]);

            if (!$user) {
                $this->addFlash('danger', 'Aucun compte trouvé avec cet email.');
            } else {
                // 💡 Ici, tu ajouteras plus tard la logique pour générer un token et envoyer un email
                $this->addFlash('success', 'Si ce compte existe, un email de réinitialisation a été envoyé.');
            }

            return $this->redirectToRoute('app_forgot_password');
        }

        return $this->render('forgot_password/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
