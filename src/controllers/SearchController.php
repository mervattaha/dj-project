<?php

namespace App\Controllers;

use Twig\Environment;
use PDO;
use Exception;
use App\Repositories\DJRepository;

class SearchController extends BaseController
{
    protected  $pdo;
    protected  $twig;
    protected  $djRepository;

    public function __construct(PDO $pdo, Environment $twig)
    {
        $this->pdo = $pdo;
        $this->twig = $twig;
        $this->djRepository = new DJRepository($pdo);
    }

    public function searchDJsByLocation($query)
    {
        try {
            $djs = $this->djRepository->searchDJsByLocation($query);

            echo $this->twig->render('search_results.twig', [
                'djs' => $djs,
                'search_query' => $query
            ]);
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    
}
