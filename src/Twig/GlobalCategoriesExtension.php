<?php

namespace App\Twig;

use App\Repository\CategorieRepository;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class GlobalCategoriesExtension extends AbstractExtension implements GlobalsInterface
{
    private CategorieRepository $categorieRepository;

    public function __construct(CategorieRepository $categorieRepository)
    {
        $this->categorieRepository = $categorieRepository;
    }

    public function getGlobals(): array
    {
        return [
            'categories' => $this->categorieRepository->findAll(),
        ];
    }
}
