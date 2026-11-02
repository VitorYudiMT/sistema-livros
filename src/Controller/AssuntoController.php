<?php

namespace App\Controller;

use App\Entity\Assunto;
use App\Form\AssuntoType;
use App\RN\AssuntoRN;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

#[Route('/assunto', name: 'assunto_')]
class AssuntoController extends AbstractController
{
    public function __construct(
        private AssuntoRN $assuntoRN
    ) {
    }

    // **************** LISTAGEM DE ASSUNTOS
    #[Route('/', name: 'listar', methods: ['GET'])]
    public function listar(Request $request): Response
    {
        $status = $request->query->get('status', 'ativos');

        $ativo = $status !== 'inativos';

        $assuntos = $this->assuntoRN->listar($ativo);

        return $this->render('assunto/listar.html.twig', [
            'assuntos' => $assuntos,
            'status' => $status,
        ]);
    }

    // **************** CADASTRO DE NOVO ASSUNTO
    #[Route('/novo', name: 'novo', methods: ['GET', 'POST'])]
    public function novo(Request $request): Response
    {
        $assunto = new Assunto();

        $form = $this->createForm(AssuntoType::class, $assunto);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->assuntoRN->cadastrar($assunto);

                $this->addFlash('success', 'Assunto cadastrado com sucesso!');

                return $this->redirectToRoute('assunto_listar');

            } catch (\InvalidArgumentException $e) {
                $this->addFlash('danger', $e->getMessage());
            }
        }

        return $this->render('assunto/novo.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // **************** EDIÇÃO DE ASSUNTO
    #[Route(
        '/{id}/editar',
        name: 'editar',
        methods: ['GET', 'POST'],
        requirements: ['id' => '\d+']
    )]
    public function editar(int $id, Request $request): Response
    {
        $assunto = $this->assuntoRN->buscarPorId($id);

        if (!$assunto) {
            throw $this->createNotFoundException(
                'Assunto não encontrado.'
            );
        }

        $form = $this->createForm(AssuntoType::class, $assunto);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->assuntoRN->editar($assunto);

                $this->addFlash('success', 'Assunto atualizado com sucesso!');

                return $this->redirectToRoute('assunto_listar');

            } catch (\InvalidArgumentException $e) {
                $this->addFlash('danger', $e->getMessage());
            }
        }

        return $this->render('assunto/novo.html.twig', [
            'form' => $form->createView(),
            'titulo' => 'Editar assunto',
            'descricao' => 'Altere os dados do assunto cadastrado.',
            'botao' => 'Salvar alterações',
        ]);
    }

    // **************** INATIVAÇÃO DE ASSUNTO
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
            'inativar_assunto_' . $id,
            $request->request->get('_token')
        );

        if (!$csrfTokenManager->isTokenValid($token)) {
            throw $this->createAccessDeniedException(
                'Token de segurança inválido.'
            );
        }

        try {
            $this->assuntoRN->inativar($id);

            $this->addFlash('success', 'Assunto inativado com sucesso!');

        } catch (\InvalidArgumentException $e) {
            $this->addFlash('danger', $e->getMessage());
        }

        return $this->redirectToRoute('assunto_listar');
    }

    // **************** REATIVAÇÃO DE ASSUNTO
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
            'reativar_assunto_' . $id,
            $request->request->get('_token')
        );

        if (!$csrfTokenManager->isTokenValid($token)) {
            throw $this->createAccessDeniedException(
                'Token de segurança inválido.'
            );
        }

        try {
            $this->assuntoRN->reativar($id);

            $this->addFlash('success', 'Assunto reativado com sucesso!');

        } catch (\InvalidArgumentException $e) {
            $this->addFlash('danger', $e->getMessage());
        }

        return $this->redirectToRoute('assunto_listar');
    }
}
