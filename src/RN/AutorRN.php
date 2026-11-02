<?php

namespace App\RN;

use App\Entity\Autor;
use App\Repository\AutorRepository;
use Doctrine\ORM\EntityManagerInterface;

class AutorRN
{
    public function __construct(
        private AutorRepository $autorRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    // LISTAGEM DE REGISTROS CONFORME O STATUS (ATIVOS OU INATIVOS)
    public function listar(bool $ativo = true): array
    {
        return $this->autorRepository->findBy(
            ['ativo' => $ativo],
            ['nome' => 'ASC']
        );
    }

    // BUSCA UM REGISTRO POR ID - TELA DE EDIÇÃO
    public function buscarPorId(int $id): ?Autor
    {
        return $this->autorRepository->find($id);
    }

    // NOVO REGISTRO
    public function cadastrar(Autor $autor): Autor
    {
        $nome = trim($autor->getNome());

        if ($nome === '') {
            throw new \InvalidArgumentException(
                'O nome do autor é obrigatório.'
            );
        }

        if ($this->existeNome($nome)) {
            throw new \InvalidArgumentException(
                'Já existe um autor cadastrado com esse nome.'
            );
        }

        $autor->setNome($nome);

        $this->entityManager->persist($autor);
        $this->entityManager->flush();

        return $autor;
    }

    // VERIFICA SE O NOME JÁ EXISTE
    public function existeNome(
        string $nome,
        ?int $idIgnorado = null
    ): bool {
        $query = $this->autorRepository->createQueryBuilder('a')
            ->where('LOWER(a.nome) = LOWER(:nome)')
            ->setParameter('nome', trim($nome))
            ->setMaxResults(1);

        if ($idIgnorado !== null) {
            $query->andWhere('a.id != :id')
                ->setParameter('id', $idIgnorado);
        }

        return $query->getQuery()->getOneOrNullResult() !== null;
    }

    // EDITAR
    public function editar(Autor $autor): Autor
    {
        $nome = trim($autor->getNome());

        // não permite editar um autor inativo
        if (!$autor->isAtivo()) {
            throw new \InvalidArgumentException(
                'Não é possível editar um autor inativo.'
            );
        }

        if ($nome === '') {
            throw new \InvalidArgumentException(
                'O nome do autor é obrigatório.'
            );
        }

        if ($this->existeNome($nome, $autor->getId())) {
            throw new \InvalidArgumentException(
                'Já existe outro autor cadastrado com esse nome.'
            );
        }

        $autor->setNome($nome);

        $this->entityManager->flush();

        return $autor;
    }

    // INATIVA UM REGISTRO AO CLICARM EM EXCLUIR
    public function inativar(int $id): void
    {
        $autor = $this->buscarPorId($id);

        if (!$autor) {
            throw new \InvalidArgumentException(
                'Autor não encontrado.'
            );
        }

        $autor->setAtivo(false);

        $this->entityManager->flush();
    }

    // REATIVA UM REGISTRO INATIVADO
    public function reativar(int $id): void
    {
        $autor = $this->buscarPorId($id);

        if (!$autor) {
            throw new \InvalidArgumentException(
                'Autor não encontrado.'
            );
        }

        $autor->setAtivo(true);

        $this->entityManager->flush();
    }
}