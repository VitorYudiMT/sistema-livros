<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261102060000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Cria a view de relatorio de livros agrupados por autor.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE VIEW vw_relatorio_livros_autores AS
            SELECT
                l.Code AS livro_id,
                l.Titulo AS livro_titulo,
                l.Editora AS livro_editora,
                l.Edicao AS livro_edicao,
                l.AnoPublicacao AS livro_ano,
                l.Valor AS livro_valor,
                l.Ativo AS livro_ativo,

                a.CodAu AS autor_id,
                a.Nome AS autor_nome,
                a.Ativo AS autor_ativo,

                s.codAs AS assunto_id,
                s.Descricao AS assunto_descricao,
                s.Ativo AS assunto_ativo

            FROM Livro l

            LEFT JOIN Livro_Autor la
                ON la.Livro_CodLi = l.Code

            LEFT JOIN Autor a
                ON a.CodAu = la.Autor_CodAu

            LEFT JOIN Livro_Assunto las
                ON las.Livro_CodLi = l.Code

            LEFT JOIN Assunto s
                ON s.codAs = las.Assunto_codAs
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP VIEW vw_relatorio_livros_autores');
    }
}