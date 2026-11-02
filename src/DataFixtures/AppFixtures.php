<?php

namespace App\DataFixtures;

use App\Entity\Assunto;
use App\Entity\Autor;
use App\Entity\Livro;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // AUTORES
        $autor1 = (new Autor())
            ->setNome('Ana Martins');

        $autor2 = (new Autor())
            ->setNome('Bruno Almeida');

        $autor3 = (new Autor())
            ->setNome('Carla Souza');

        $autor4 = (new Autor())
            ->setNome('Diego Ferreira')
            ->setAtivo(false);

        // ASSUNTOS
        $assunto1 = (new Assunto())
            ->setDescricao('Programacao');

        $assunto2 = (new Assunto())
            ->setDescricao('Tecnologia');

        $assunto3 = (new Assunto())
            ->setDescricao('Sistemas Legados')
            ->setAtivo(false);

        // LIVRO 1
        $livro1 = (new Livro())
            ->setTitulo('Fundamentos da Programacao')
            ->setEditora('Editora Demonstracao')
            ->setEdicao(1)
            ->setAnoPublicacao('2022')
            ->setValor('59.90');

        $livro1->addAutor($autor1);
        $livro1->addAssunto($assunto1);
        $livro1->addAssunto($assunto2);

        // LIVRO 2
        $livro2 = (new Livro())
            ->setTitulo('Desenvolvimento Web com PHP')
            ->setEditora('Editora Tecnica')
            ->setEdicao(2)
            ->setAnoPublicacao('2023')
            ->setValor('89.90');

        $livro2->addAutor($autor2);
        $livro2->addAssunto($assunto1);
        $livro2->addAssunto($assunto2);

        // LIVRO 3 - DOIS AUTORES
        $livro3 = (new Livro())
            ->setTitulo('Arquitetura de Software')
            ->setEditora('Editora Demonstracao')
            ->setEdicao(1)
            ->setAnoPublicacao('2024')
            ->setValor('74.50');

        $livro3->addAutor($autor1);
        $livro3->addAutor($autor3);
        $livro3->addAssunto($assunto2);

        // LIVRO 4 - INATIVO
        $livro4 = (new Livro())
            ->setTitulo('Praticas de Sistemas Legados')
            ->setEditora('Editora Tecnica')
            ->setEdicao(1)
            ->setAnoPublicacao('2021')
            ->setValor('49.90')
            ->setAtivo(false);

        $livro4->addAutor($autor4);
        $livro4->addAssunto($assunto3);

        // PERSISTENCIA DOS AUTORES
        $manager->persist($autor1);
        $manager->persist($autor2);
        $manager->persist($autor3);
        $manager->persist($autor4);

        // PERSISTENCIA DOS ASSUNTOS
        $manager->persist($assunto1);
        $manager->persist($assunto2);
        $manager->persist($assunto3);

        // PERSISTENCIA DOS LIVROS
        $manager->persist($livro1);
        $manager->persist($livro2);
        $manager->persist($livro3);
        $manager->persist($livro4);

        // EXECUTA A GRAVACAO NO BANCO
        $manager->flush();
    }
}
