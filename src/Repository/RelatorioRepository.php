<?php

namespace App\Repository;

use Doctrine\DBAL\Connection;

class RelatorioRepository
{
    public function __construct(
        private Connection $connection
    ) {
    }
    
    // Consulta a view aplicando os filtros informados
    public function listar(array $filtros = []): array
    {
        $sql = '
            SELECT v.*
            FROM vw_relatorio_livros_autores v
            WHERE 1 = 1
        ';

        $parametros = [];

        // Filtro de situação do livro
        if (($filtros['livro_status'] ?? 'todos') !== 'todos') {
            $sql .= ' AND v.livro_ativo = :livro_ativo';

            $parametros['livro_ativo'] =
                $filtros['livro_status'] === 'ativos' ? 1 : 0;
        }

        // Filtro de situação do autor
        if (($filtros['autor_status'] ?? 'todos') !== 'todos') {
            $sql .= ' AND v.autor_ativo = :autor_ativo';

            $parametros['autor_ativo'] =
                $filtros['autor_status'] === 'ativos' ? 1 : 0;
        }

        // Filtro de situação do assunto
        if (($filtros['assunto_status'] ?? 'todos') !== 'todos') {
            $sql .= ' AND v.assunto_ativo = :assunto_ativo';

            $parametros['assunto_ativo'] =
                $filtros['assunto_status'] === 'ativos' ? 1 : 0;
        }

        // Filtro por autor específico
        if (!empty($filtros['autor_id'])) {
            $sql .= ' AND v.autor_id = :autor_id';

            $parametros['autor_id'] = $filtros['autor_id'];
        }

        // Filtro por assunto específico:
        // encontra os livros associados ao assunto,
        // mas preserva todas as linhas desses livros na VIEW.
        if (!empty($filtros['assunto_id'])) {
            $sql .= '
                AND EXISTS (
                    SELECT 1
                    FROM Livro_Assunto las_filtro
                    INNER JOIN Assunto s_filtro
                        ON s_filtro.codAs = las_filtro.Assunto_codAs
                    WHERE las_filtro.Livro_CodLi = v.livro_id
                    AND s_filtro.codAs = :assunto_id
                )
            ';

            $parametros['assunto_id'] = $filtros['assunto_id'];
        }

        $sql .= ' ORDER BY v.autor_nome ASC, v.livro_titulo ASC';

        return $this->connection
            ->executeQuery($sql, $parametros)
            ->fetchAllAssociative();
    }

}