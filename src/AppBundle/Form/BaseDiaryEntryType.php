<?php

namespace AppBundle\Form;

use AppBundle\Entity\DiaryEntry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
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
        ->add('dateTime', DateTimeType::class, [
                'label' => "Date and time: ",
                'widget' => 'single_text',
                'html5' => false,
            ]
        )
        ->add('note', TextareaType::class, ['label' => "Note: "])
        ->add('category', TextType::class, ['label' => 'Category: '])
        ->add('category', ChoiceType::class, [
                'label' => 'Category: ',
                'choices' => array('diary' => 1, 'Symfony' => 2),
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => DiaryEntry::class,
        ]);
    }
}
