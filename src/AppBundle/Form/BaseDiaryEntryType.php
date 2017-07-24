<?php

namespace AppBundle\Form;

use AppBundle\ChoiceLoader;
use AppBundle\Entity\DiaryEntry;
use AppBundle\Form\DataTransformer\TagToTextTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BaseDiaryEntryType extends AbstractType
{
//    private $transformer;

//    public function __construct(TagToTextTransformer $transformer)
//    {
//        $this->transformer = $transformer;
//    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('dateTime', DateTimeType::class, [
                'label' => "Date and time: ",
                'widget' => 'single_text',
                'html5' => false,
            ]
        )
        ->add('note', TextareaType::class,
            ['label' => "Note: "])
        ->add('delete', SubmitType::class,
            ['attr' => ['class' => "diarybtn"])
        ->add('update', SubmitType::class,
            ['attr' => ['class' => "diarybtn"]])
        ->add('cancel', SubmitType::class,
            ['attr' => ['class' => "diarybtn"]]);
//        ->add('tempTags', ChoiceType::class,  [
//            'multiple' => true,
//            'required' => false,
//            'attr' => ['class' => "form-control"],
//            'choice_loader' => new ChoiceLoader($options['tag_choices']),
//        ]);
//        ->add('tags', CollectionType::class, array(
//            'entry_type' => TagType::class,
//            'allow_add' => true,
//        ));
//        $builder->get('tag')
//            ->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => DiaryEntry::class,
            'tag_choices' => []
        ]);
    }
}
