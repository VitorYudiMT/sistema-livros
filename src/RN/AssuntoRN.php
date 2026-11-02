<?php

namespace App\RN;

use App\Entity\Assunto;
use App\Repository\AssuntoRepository;
use Doctrine\ORM\EntityManagerInterface;

class AssuntoRN
{
    public function __construct(
        private AssuntoRepository $assuntoRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    // Retorna os assuntos conforme o status informado.
    public function listar(bool $ativo = true): array
    {
        return $this->assuntoRepository->findBy(
            ['ativo' => $ativo],
            ['descricao' => 'ASC']
        );
    }

    
    // Busca um assunto pelo identificador.
    public function buscarPorId(int $id): ?Assunto
    {
        return $this->assuntoRepository->find($id);
    }

    // Verifica se já existe uma descrição cadastrada. Considera também os registros inativos.
    public function existeDescricao(
        string $descricao,
        ?int $idIgnorado = null
    ): bool {
        $query = $this->assuntoRepository->createQueryBuilder('a')
            ->where('LOWER(a.descricao) = LOWER(:descricao)')
            ->setParameter('descricao', trim($descricao))
            ->setMaxResults(1);

        if ($idIgnorado !== null) {
            $query->andWhere('a.id != :id')
                ->setParameter('id', $idIgnorado);
        }

        return $query->getQuery()->getOneOrNullResult() !== null;
    }

    // Cadastro
    public function cadastrar(Assunto $assunto): Assunto
    {
        $descricao = trim($assunto->getDescricao());

        if ($descricao === '') {
            throw new \InvalidArgumentException(
                'A descrição do assunto é obrigatória.'
            );
        }

        if ($this->existeDescricao($descricao)) {
            throw new \InvalidArgumentException(
                'Já existe um assunto cadastrado com essa descrição.'
            );
        }

        $assunto->setDescricao($descricao);

        $this->entityManager->persist($assunto);
        $this->entityManager->flush();

        return $assunto;
    }

    // Atualiza assunto
    public function editar(Assunto $assunto): Assunto
    {
        if (!$assunto->isAtivo()) {
            throw new \InvalidArgumentException(
                'Não é possível editar um assunto inativo.'
            );
        }

        $descricao = trim($assunto->getDescricao());

        if ($descricao === '') {
            throw new \InvalidArgumentException(
                'A descrição do assunto é obrigatória.'
            );
        }

        if ($this->existeDescricao($descricao, $assunto->getId())) {
            throw new \InvalidArgumentException(
                'Já existe outro assunto cadastrado com essa descrição.'
            );
        }

        $assunto->setDescricao($descricao);

        $this->entityManager->flush();

        return $assunto;
    }

    // Inativar
    public function inativar(int $id): void
    {
        $assunto = $this->buscarPorId($id);

        if (!$assunto) {
            throw new \InvalidArgumentException(
                'Assunto não encontrado.'
            );
        }

        $assunto->setAtivo(false);

        $this->entityManager->flush();
    }

    // Reativar
    public function reativar(int $id): void
    {
        $assunto = $this->buscarPorId($id);

        if (!$assunto) {
            throw new \InvalidArgumentException(
                'Assunto não encontrado.'
            );
        }

        $assunto->setAtivo(true);

        $this->entityManager->flush();
    }
}
