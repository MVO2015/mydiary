<?php

namespace AppBundle\Form;

use AppBundle\Entity\DiaryEntry;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BaseDiaryEntryType extends AbstractType
{
    private $transformer;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->transformer = $options['data_transformer'];
        $builder
        ->add('dateTime', DateTimeType::class, [
                'label' => "Date and time: ",
                'widget' => 'single_text',
                'html5' => false,
            ]
        )
        ->add('note', TextareaType::class,
            ['label' => "Note: "])
        ->add('tags', EntityType::class, [
            'class' => 'AppBundle:Tag',
            'multiple' => true,
            'required' => false,
            'attr' => ['class'=>'select2 form-control']
        ]);
        $builder->get('tags')->addViewTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => DiaryEntry::class,
            'data_transformer' => []
        ]);
    }

}
