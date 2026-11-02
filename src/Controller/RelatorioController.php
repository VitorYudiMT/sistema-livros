<?php

namespace App\Controller;

use App\RN\RelatorioRN;
use App\Repository\AutorRepository;
use App\Repository\AssuntoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/relatorio', name: 'relatorio_')]
class RelatorioController extends AbstractController
{
    public function __construct(
        private RelatorioRN $relatorioRN,
        private AutorRepository $autorRepository,
        private AssuntoRepository $assuntoRepository
    ) {
    }

    #[Route('/livros', name: 'livros', methods: ['GET'])]
    public function livros(Request $request): Response
    {
        $filtros = [
            'livro_status' => $request->query->get('livro_status', 'todos'),
            'autor_status' => $request->query->get('autor_status', 'todos'),
            'assunto_status' => $request->query->get('assunto_status', 'todos'),
            'autor_id' => $request->query->get('autor_id') !== ''
                ? $request->query->getInt('autor_id')
                : null,

            'assunto_id' => $request->query->get('assunto_id') !== ''
                ? $request->query->getInt('assunto_id')
                : null,
        ];

        // Consulta os dados do relatório
        $autores = $this->relatorioRN->listar($filtros);

        // Recupera as opções dos filtros
        $listaAutores = $this->autorRepository->findBy(
            [],
            ['nome' => 'ASC']
        );

        $listaAssuntos = $this->assuntoRepository->findBy(
            [],
            ['descricao' => 'ASC']
        );

        return $this->render('relatorio/livros.html.twig', [
            'autores' => $autores,
            'listaAutores' => $listaAutores,
            'listaAssuntos' => $listaAssuntos,
            'filtros' => $filtros,
        ]);
    }
}