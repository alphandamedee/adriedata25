<?php
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use App\Repository\CategorieProduitRepository;

class NavbarCategoriesExtension extends AbstractExtension implements GlobalsInterface
{
    private $categorieRepo;

    public function __construct(CategorieProduitRepository $categorieRepo)
    {
        $this->categorieRepo = $categorieRepo;
    }

    public function getGlobals(): array
    {
        return [
            'categories' => $this->categorieRepo->findAll(),
        ];
    }
}
