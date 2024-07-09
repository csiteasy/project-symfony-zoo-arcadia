<?php

namespace App\Form;

use App\Entity\review;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo',null,['label'=>'Pseudo'])
            ->add('content',null,['label'=>'Avis'])
            ->add('published',null,['label'=>'Publié ?'])
            ->add('publishedAt',null,['label'=>'Date de Publication'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => review::class,
        ]);
    }
}
