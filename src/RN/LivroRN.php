<?php

namespace App\RN;

use App\Entity\Livro;
use App\Repository\LivroRepository;
use App\Repository\AutorRepository;
use App\Repository\AssuntoRepository;
use Doctrine\ORM\EntityManagerInterface;

class LivroRN
{
    public function __construct(
        private LivroRepository $livroRepository,
        private AutorRepository $autorRepository,
        private AssuntoRepository $assuntoRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    /**
     * Retorna os livros conforme o status informado.
     */
    public function listar(bool $ativo = true): array
    {
        return $this->livroRepository->findBy(
            ['ativo' => $ativo],
            ['titulo' => 'ASC']
        );
    }

    /**
     * Busca um livro pelo identificador.
     */
    public function buscarPorId(int $id): ?Livro
    {
        return $this->livroRepository->find($id);
    }

    /**
     * Valida e normaliza os dados do livro.
     */
    private function validarDados(Livro $livro): void
    {
        $titulo = trim($livro->getTitulo() ?? '');
        $editora = trim($livro->getEditora() ?? '');
        $anoPublicacao = trim($livro->getAnoPublicacao() ?? '');
        $valor = trim($livro->getValor() ?? '');

        if ($titulo === '') {
            throw new \InvalidArgumentException(
                'O título do livro é obrigatório.'
            );
        }

        if ($editora === '') {
            throw new \InvalidArgumentException(
                'A editora do livro é obrigatória.'
            );
        }

        if ($livro->getEdicao() === null || $livro->getEdicao() <= 0) {
            throw new \InvalidArgumentException(
                'A edição deve ser um número inteiro maior que zero.'
            );
        }

        if (!preg_match('/^\d{4}$/', $anoPublicacao)) {
            throw new \InvalidArgumentException(
                'O ano de publicação deve possuir quatro dígitos.'
            );
        }

        if (!is_numeric($valor) || (float) $valor < 0) {
            throw new \InvalidArgumentException(
                'O valor deve ser numérico e não negativo.'
            );
        }

        $livro->setTitulo($titulo);
        $livro->setEditora($editora);
        $livro->setAnoPublicacao($anoPublicacao);
        $livro->setValor(number_format((float) $valor, 2, '.', ''));
    }

    
    // Valida os autores e assuntos associados ao livro.
    private function validarRelacionamentos(Livro $livro): void
    {
        if ($livro->getAutores()->isEmpty()) {
            throw new \InvalidArgumentException(
                'Selecione pelo menos um autor para o livro.'
            );
        }

        foreach ($livro->getAutores() as $autor) {
            if (!$autor->isAtivo()) {
                throw new \InvalidArgumentException(
                    'Não é possível associar autores inativos ao livro.'
                );
            }
        }

        if ($livro->getAssuntos()->isEmpty()) {
            throw new \InvalidArgumentException(
                'Selecione pelo menos um assunto para o livro.'
            );
        }

        foreach ($livro->getAssuntos() as $assunto) {
            if (!$assunto->isAtivo()) {
                throw new \InvalidArgumentException(
                    'Não é possível associar assuntos inativos ao livro.'
                );
            }
        }
    }

    // Cadastro
    public function cadastrar(Livro $livro): Livro
    {
        $this->validarDados($livro);
        $this->validarRelacionamentos($livro);

        $this->entityManager->persist($livro);
        $this->entityManager->flush();

        return $livro;
    }

    // Editar
    public function editar(Livro $livro): Livro
    {
        if (!$livro->isAtivo()) {
            throw new \InvalidArgumentException(
                'Não é possível editar um livro inativo.'
            );
        }

        $this->validarDados($livro);
        $this->validarRelacionamentos($livro);

        $this->entityManager->flush();

        return $livro;
    }

    // Inativa
    public function inativar(int $id): void
    {
        $livro = $this->buscarPorId($id);

        if (!$livro) {
            throw new \InvalidArgumentException(
                'Livro não encontrado.'
            );
        }

        $livro->setAtivo(false);

        $this->entityManager->flush();
    }

    // Reativa
    public function reativar(int $id): void
    {
        $livro = $this->buscarPorId($id);

        if (!$livro) {
            throw new \InvalidArgumentException(
                'Livro não encontrado.'
            );
        }

        $livro->setAtivo(true);

        $this->entityManager->flush();
    }
}