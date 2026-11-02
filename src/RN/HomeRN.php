<?php

namespace App\RN;

class HomeRN
{
    // Retorna infos para montar a página
    public function obterDadosIniciais(): array
    {
        return [
            'titulo' => 'Sistema de Cadastro de Livros',

            'descricao' => 'Gerenciamento de livros, autores e assuntos.',

            'modulos' => [
                [
                    'nome' => 'Livros',
                    'descricao' => 'Cadastro e gerenciamento de livros.',
                    'icone' => 'book',
                ],
                [
                    'nome' => 'Autores',
                    'descricao' => 'Cadastro e gerenciamento de autores.',
                    'icone' => 'users',
                ],
                [
                    'nome' => 'Assuntos',
                    'descricao' => 'Cadastro e gerenciamento de assuntos.',
                    'icone' => 'folder',
                ],
                [
                    'nome' => 'Relatórios',
                    'descricao' => 'Consulta de livros agrupados por autor.',
                    'icone' => 'chart',
                ],
            ],
        ];
    }
}
