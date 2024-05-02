<?php

namespace App\Form;

use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category', ChoiceType::class, [
                'choices' => array_combine(
                    array_map(
                        function ($category) {
                            return $category->getName();
                        },
                        $options['categories']
                    ),
                    array_map(
                        function ($category) {
                            return $category->getId();
                        },
                        $options['categories']
                    )
                ),
                'label' => 'Filtrer par catégorie',
                'placeholder' => 'Toutes les catégories',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
            'categories' => null,
        ]);
    }
}
