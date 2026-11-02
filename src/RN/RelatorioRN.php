<?php

namespace App\RN;

use App\Repository\RelatorioRepository;

class RelatorioRN
{
    public function __construct(
        private RelatorioRepository $relatorioRepository
    ) {
    }

    /**
     * Consulta e organiza os dados do relatório por autor e livro.
     */
    public function listar(array $filtros = []): array
    {
        $registros = $this->relatorioRepository->listar($filtros);

        $autores = [];

        foreach ($registros as $registro) {

            $autorId = $registro['autor_id'];

            $livroId = $registro['livro_id'];

            // Inicializa o grupo do autor
            if (!isset($autores[$autorId])) {
                $autores[$autorId] = [
                    'id' => $autorId,
                    'nome' => $registro['autor_nome'],
                    'ativo' => (bool) $registro['autor_ativo'],
                    'livros' => [],
                ];
            }

            // Inicializa o livro dentro do grupo do autor
            if (!isset($autores[$autorId]['livros'][$livroId])) {
                $autores[$autorId]['livros'][$livroId] = [
                    'id' => $livroId,
                    'titulo' => $registro['livro_titulo'],
                    'editora' => $registro['livro_editora'],
                    'edicao' => $registro['livro_edicao'],
                    'ano' => $registro['livro_ano'],
                    'valor' => $registro['livro_valor'],
                    'ativo' => (bool) $registro['livro_ativo'],
                    'assuntos' => [],
                ];
            }

            // Adiciona o assunto sem duplicá-lo
            $assuntoId = $registro['assunto_id'];

            if (
                $assuntoId !== null &&
                !isset(
                    $autores[$autorId]['livros'][$livroId]['assuntos'][$assuntoId]
                )
            ) {
                $autores[$autorId]['livros'][$livroId]['assuntos'][$assuntoId] = [
                    'id' => $assuntoId,
                    'descricao' => $registro['assunto_descricao'],
                    'ativo' => (bool) $registro['assunto_ativo'],
                ];
            }
        }

        // Converte as coleções associativas em arrays sequenciais
        foreach ($autores as &$autor) {
            foreach ($autor['livros'] as &$livro) {
                $livro['assuntos'] = array_values($livro['assuntos']);
            }

            unset($livro);

            $autor['livros'] = array_values($autor['livros']);
        }

        unset($autor);

        return array_values($autores);
    }
}
