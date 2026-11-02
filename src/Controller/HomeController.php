<?php

namespace App\Controller;

use App\RN\HomeRN;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(HomeRN $homeRN): Response
    {
        $dados = $homeRN->obterDadosIniciais();

        return $this->render('home/index.html.twig', [
            'dados' => $dados,
        ]);
    }
}