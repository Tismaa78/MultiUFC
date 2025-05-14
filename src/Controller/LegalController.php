<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LegalController extends AbstractController
{
    #[Route('/mentions-legales', name: 'app_mentions_legales')]
    public function mentions(): Response
    {
        return $this->render('footer/mentions_legales.html.twig');
    }
    
    #[Route('/conditions-utilisation', name: 'app_conditions_utilisation')]
    public function conditions(): Response
    {
        return $this->render('footer/conditions_utilisation.html.twig');
    }
    
    #[Route('/politique-confidentialite', name: 'app_confidentialite')]
    public function confidentialite(): Response
    {
        return $this->render('footer/confidentialite.html.twig');
    }
    
    #[Route('/contact', name: 'app_contact')]
    public function contact(): Response
    {
        return $this->render('footer/contact.html.twig');
    }
    
}