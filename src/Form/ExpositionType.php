<?php

namespace App\Form;

use App\Entity\Billet;
use App\Entity\Exposition;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Repository\BilletRepository;

class ExpositionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $exposition = $options['data'] ?? null;
        $member =  $exposition->getMember();

        $builder
            ->add('description')
            ->add('publiee')
            ->add('billets', null, [
                'multiple' => true,
                'expanded' => true,
                'by_reference' => false,
                // Limit billet choices to those in the member's album
                'query_builder' => function (BilletRepository $repository) use ($member) {
                    return $repository->createQueryBuilder('o')
                        ->leftJoin('o.album', 'i')
                        ->leftJoin('i.member', 'm')
                        ->where('m.id = :memberId')
                        ->setParameter('memberId',  $member->getId() );
                },
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Exposition::class,
        ]);
    }
}
