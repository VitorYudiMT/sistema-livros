<?php

namespace App\Controller;

use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use App\Entity\Autor;
use App\Form\AutorType;
use App\RN\AutorRN;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/autor', name: 'autor_')]
class AutorController extends AbstractController
{
    public function __construct(
        private AutorRN $autorRN
    ) {
    }

    #[Route('/', name: 'listar', methods: ['GET'])]
    public function listar(Request $request): Response
    {
        $status = $request->query->get('status', 'ativos');

        $ativo = $status !== 'inativos';

        $autores = $this->autorRN->listar($ativo);

        return $this->render('autor/listar.html.twig', [
            'autores' => $autores,
            'status' => $status,
        ]);
    }

    // **************** CADASTRO DE NOVO AUTOR
    #[Route('/novo', name: 'novo', methods: ['GET', 'POST'])]
    public function novo(Request $request): Response
    {
        $autor = new Autor();

        $form = $this->createForm(AutorType::class, $autor);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            try {
                $this->autorRN->cadastrar($autor);

                $this->addFlash('success', 'Autor cadastrado com sucesso!');

                return $this->redirectToRoute('autor_listar');

            } catch (\InvalidArgumentException $e) {

                $this->addFlash('danger', $e->getMessage());

            }
        }

        return $this->render('autor/novo.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // **************** EDIÇÃO DE AUTOR
    #[Route('/{id}/editar', name: 'editar', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function editar(int $id, Request $request): Response
    {
        $autor = $this->autorRN->buscarPorId($id);

        if (!$autor) {
            throw $this->createNotFoundException(
                'Autor não encontrado.'
            );
        }

        $form = $this->createForm(AutorType::class, $autor);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->autorRN->editar($autor);

                $this->addFlash('success', 'Autor atualizado com sucesso!');

                return $this->redirectToRoute('autor_listar');

            } catch (\InvalidArgumentException $e) {
                $this->addFlash('danger', $e->getMessage());
            }
        }

        return $this->render('autor/novo.html.twig', [
            'form' => $form->createView(),
            'titulo' => 'Editar autor',
            'descricao' => 'Altere os dados do autor cadastrado.',
            'botao' => 'Salvar alterações',
        ]);
    }

    // **************** INATIVAÇÃO DE AUTOR
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
            'inativar_autor_' . $id,
            $request->request->get('_token')
        );

        if (!$csrfTokenManager->isTokenValid($token)) {
            throw $this->createAccessDeniedException(
                'Token de segurança inválido.'
            );
        }

        try {
            $this->autorRN->inativar($id);

            $this->addFlash('success', 'Autor inativado com sucesso!');

        } catch (\InvalidArgumentException $e) {
            $this->addFlash('danger', $e->getMessage());
        }

        return $this->redirectToRoute('autor_listar');
    }

    // **************** REATIVAÇÃO DE AUTOR
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
            'reativar_autor_' . $id,
            $request->request->get('_token')
        );

        if (!$csrfTokenManager->isTokenValid($token)) {
            throw $this->createAccessDeniedException(
                'Token de segurança inválido.'
            );
        }

        try {
            $this->autorRN->reativar($id);

            $this->addFlash('success', 'Autor reativado com sucesso!');

        } catch (\InvalidArgumentException $e) {
            $this->addFlash('danger', $e->getMessage());
        }

        return $this->redirectToRoute('autor_listar');
    }
}