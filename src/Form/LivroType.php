<?php

namespace App\Form;

use App\Entity\Livro;
use App\Entity\Autor;
use App\Entity\Assunto;
use App\Repository\AutorRepository;
use App\Repository\AssuntoRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LivroType extends AbstractType
{
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {
        $builder
            ->add('titulo', TextType::class, [
                'label' => 'Título',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Informe o título do livro',
                    'maxlength' => 40,
                ],
            ])

            ->add('editora', TextType::class, [
                'label' => 'Editora',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Informe a editora',
                    'maxlength' => 40,
                ],
            ])

            ->add('edicao', IntegerType::class, [
                'label' => 'Edição',
                'attr' => [
                    'class' => 'form-control',
                    'min' => 1,
                ],
            ])

            ->add('anoPublicacao', TextType::class, [
                'label' => 'Ano de publicação',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex.: 2025',
                    'maxlength' => 4,
                ],
            ])

            ->add('valor', NumberType::class, [
                'label' => 'Valor',
                'scale' => 2,
                'grouping' => false,
                'html5' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => '0,00',
                    'min' => 0,
                    'step' => '0.01',
                ],
            ])

            ->add('autores', EntityType::class, [
                'class' => Autor::class,
                'choice_label' => 'nome',
                'label' => 'Autores',
                'multiple' => true,
                'expanded' => false,
                'required' => true,
                'query_builder' => function (AutorRepository $repository) {
                    return $repository->createQueryBuilder('a')
                        ->where('a.ativo = :ativo')
                        ->setParameter('ativo', true)
                        ->orderBy('a.nome', 'ASC');
                },
                'attr' => [
                    'class' => 'form-select',
                    'size' => 5,
                ],
            ])

            ->add('assuntos', EntityType::class, [
                'class' => Assunto::class,
                'choice_label' => 'descricao',
                'label' => 'Assuntos',
                'multiple' => true,
                'expanded' => false,
                'required' => true,
                'query_builder' => function (AssuntoRepository $repository) {
                    return $repository->createQueryBuilder('a')
                        ->where('a.ativo = :ativo')
                        ->setParameter('ativo', true)
                        ->orderBy('a.descricao', 'ASC');
                },
                'attr' => [
                    'class' => 'form-select',
                    'size' => 5,
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Livro::class,
        ]);
    }
}
