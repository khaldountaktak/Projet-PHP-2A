<?php

namespace App\Form;

use App\Entity\Album;
use App\Entity\Billet;
use App\Entity\Exposition;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BilletType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pays')
            ->add('valeur')
            ->add('DateApparition')
            ->add('album', EntityType::class, [
                'class' => Album::class,
                'disabled' => true, 
            ])
            ->add('expositions', EntityType::class, [
                'class' => Exposition::class,
                'choice_label' => 'id',
                'multiple' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Billet::class,
        ]);
    }
}