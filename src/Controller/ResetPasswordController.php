<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\ForgotPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Mailer\MailerInterface;


class ResetPasswordController extends AbstractController
{
    #[Route('/mot-de-passe-oublie', name: 'app_forgot_password')]
    public function forgotPassword(
        Request $request,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer
    ): Response {
        $form = $this->createForm(ForgotPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $user = $entityManager->getRepository(Utilisateur::class)->findOneBy(['email' => $email]);

            if ($user) {
                $token = Uuid::v4()->toRfc4122();
                $user->setResetToken($token);

                $entityManager->flush();

                $emailMessage = (new TemplatedEmail())
                    ->from(new Address('mma.ufc.eu@gmail.com', 'MMA & UFC Blog'))
                    ->to($user->getEmail())
                    ->subject('Réinitialisation de mot de passe')
                    ->htmlTemplate('emails/reset_password.html.twig')
                    ->context([
                        'resetToken' => $token,
                        'user' => $user,
                    ]);

                $mailer->send($emailMessage);
                $this->addFlash('success', 'Un lien de réinitialisation a été envoyé par email.');
            }

            return $this->redirectToRoute('app_login');
        }

        return $this->render('reset_password/forgot.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/reset-password/{token}', name: 'app_reset_password')]
    public function resetPassword(
        string $token,
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $user = $entityManager->getRepository(Utilisateur::class)->findOneBy(['resetToken' => $token]);

        if (!$user) {
            $this->addFlash('danger', 'Lien de réinitialisation invalide.');
            return $this->redirectToRoute('app_login');
        }

        if ($request->isMethod('POST')) {
            $newPassword = $request->request->get('password');
            if ($newPassword) {
                $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
                $user->setPassword($hashedPassword);
                $user->setResetToken(null);

                $entityManager->flush();

                $this->addFlash('success', 'Mot de passe réinitialisé avec succès.');
                return $this->redirectToRoute('app_login');
            }
        }

        return $this->render('reset_password/reset.html.twig', [
            'token' => $token,
        ]);
    }
}
