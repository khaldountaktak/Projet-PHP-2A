<?php

namespace App\Form;

use App\Entity\Billet;
use App\Entity\Exposition;
use App\Entity\Member;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Repository\BilletRepository;

class ExpositionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $exposition = $options['data'] ?? null;
        $member = $exposition ? $exposition->getMember() : null;

        $builder
            ->add('description')
            ->add('publiee')
            ->add('member', EntityType::class, [
                'class' => Member::class,
                'choice_label' => 'id',
                'disabled' => true,
            ])
            ->add('billets', EntityType::class, [
                'class' => Billet::class,
                'choice_label' => 'id',
                'multiple' => true,
                // Limit billet choices to those in the member's album
                'query_builder' => function (BilletRepository $repository) use ($member) {
                    return $repository->createQueryBuilder('b')
                        ->leftJoin('b.album', 'a')
                        ->leftJoin('a.member', 'm')
                        ->where('m.id = :memberId')
                        ->setParameter('memberId', $member ? $member->getId() : 0);
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
