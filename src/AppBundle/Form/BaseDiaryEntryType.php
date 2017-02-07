<?php

namespace AppBundle\Form;

use AppBundle\Entity\DiaryEntry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BaseDiaryEntryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateTime', DateTimeType::class, array(
                'label' => "Date and time: ",
                'widget' => 'choice',
                'input' => 'datetime'))
            ->add('note', TextareaType::class, array('label' => "Note: "))
            ->add('category', TextType::class, array('label' => 'Category: ') )
            ->add('cancel', SubmitType::class, [
                    'validation_groups' => false,
                    'attr' => ['class' => "btn btn-lg btn-default"],
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => DiaryEntry::class,
        ));
    }
}