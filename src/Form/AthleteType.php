<?php

namespace App\Form;

use App\Entity\Athlete;
use App\Entity\Discipline;
use App\Entity\Pays;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class AthleteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class)
            ->add('prenom', TextType::class)
            ->add('dateNaissance', BirthdayType::class)
            ->add('photo', FileType::class, [
                'label' => "Photo de profil au format jpeg ou png",
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File(mimeTypes:['image/jpeg', 'image/png'])
                ]
            ])
            ->add('discipline', EntityType::class, [
                'class' => Discipline::class,
                'choice_label' => 'nom'
            ])
            ->add('pays', EntityType::class, [
                'class' => Pays::class,
                'choice_label' => 'nom'
            ])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Athlete::class,
        ]);
    }
}
