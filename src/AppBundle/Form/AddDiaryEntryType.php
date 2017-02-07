<?php

namespace AppBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class AddDiaryEntryType extends BaseDiaryEntryType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('save', SubmitType::class, [
                'attr' => ['class' => "btn btn-lg btn-success"],
                ]
            )
        ;
    }
}