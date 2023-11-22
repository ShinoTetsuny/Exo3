<?php

namespace App\Form;

use App\Entity\Grade;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Subject;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class GradeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('value', NumberType::class)
            ->add('subject', EntityType::class, [
                'class' => Subject::class,
                'choice_label' => function ($subject) {
                    return $subject->getName() . ' - Coeff: ' . $subject->getCoefficient();
                },
            ])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Grade::class,
        ]);
    }
}
