<?php

namespace App\Controller;

use App\Entity\Livro;
use App\Form\LivroType;
use App\RN\LivroRN;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

#[Route('/livro', name: 'livro_')]
class LivroController extends AbstractController
{
    public function __construct(
        private LivroRN $livroRN
    ) {
    }

    // **************** LISTAGEM DE LIVROS
    #[Route('/', name: 'listar', methods: ['GET'])]
    public function listar(Request $request): Response
    {
        $status = $request->query->get('status', 'ativos');

        $ativo = $status !== 'inativos';

        $livros = $this->livroRN->listar($ativo);

        return $this->render('livro/listar.html.twig', [
            'livros' => $livros,
            'status' => $status,
        ]);
    }

    // **************** CADASTRO DE NOVO LIVRO
    #[Route('/novo', name: 'novo', methods: ['GET', 'POST'])]
    public function novo(Request $request): Response
    {
        $livro = new Livro();

        $form = $this->createForm(LivroType::class, $livro);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->livroRN->cadastrar($livro);

                $this->addFlash('success', 'Livro cadastrado com sucesso!');

                return $this->redirectToRoute('livro_listar');

            } catch (\InvalidArgumentException $e) {
                $this->addFlash('danger', $e->getMessage());
            }
        }

        return $this->render('livro/novo.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // **************** EDIÇÃO DE LIVRO
    #[Route(
        '/{id}/editar',
        name: 'editar',
        methods: ['GET', 'POST'],
        requirements: ['id' => '\d+']
    )]
    public function editar(int $id, Request $request): Response
    {
        $livro = $this->livroRN->buscarPorId($id);

        if (!$livro) {
            throw $this->createNotFoundException(
                'Livro não encontrado.'
            );
        }

        $form = $this->createForm(LivroType::class, $livro);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->livroRN->editar($livro);

                $this->addFlash('success', 'Livro atualizado com sucesso!');

                return $this->redirectToRoute('livro_listar');

            } catch (\InvalidArgumentException $e) {
                $this->addFlash('danger', $e->getMessage());
            }
        }

        return $this->render('livro/novo.html.twig', [
            'form' => $form->createView(),
            'titulo' => 'Editar livro',
            'descricao' => 'Altere os dados do livro cadastrado.',
            'botao' => 'Salvar alterações',
        ]);
    }

    // **************** INATIVAÇÃO DE LIVRO
    #[Route(
        '/{id}/inativar',
        name: 'inativar',
        methods: ['POST'],
        requirements: ['id' => '\d+']
    )]
    public function inativar(
        int $id,
        Request $request,
        CsrfTokenManagerInterface $csrfTokenManager
    ): Response {
        $token = new CsrfToken(
            'inativar_livro_' . $id,
            $request->request->get('_token')
        );

        if (!$csrfTokenManager->isTokenValid($token)) {
            throw $this->createAccessDeniedException(
                'Token de segurança inválido.'
            );
        }

        try {
            $this->livroRN->inativar($id);

            $this->addFlash('success', 'Livro inativado com sucesso!');

        } catch (\InvalidArgumentException $e) {
            $this->addFlash('danger', $e->getMessage());
        }

        return $this->redirectToRoute('livro_listar');
    }

    // **************** REATIVAÇÃO DE LIVRO
    #[Route(
        '/{id}/reativar',
        name: 'reativar',
        methods: ['POST'],
        requirements: ['id' => '\d+']
    )]
    public function reativar(
        int $id,
        Request $request,
        CsrfTokenManagerInterface $csrfTokenManager
    ): Response {
        $token = new CsrfToken(
            'reativar_livro_' . $id,
            $request->request->get('_token')
        );

        if (!$csrfTokenManager->isTokenValid($token)) {
            throw $this->createAccessDeniedException(
                'Token de segurança inválido.'
            );
        }

        try {
            $this->livroRN->reativar($id);

            $this->addFlash('success', 'Livro reativado com sucesso!');

        } catch (\InvalidArgumentException $e) {
            $this->addFlash('danger', $e->getMessage());
        }

        return $this->redirectToRoute('livro_listar');
    }
}
