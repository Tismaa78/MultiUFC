<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class MonCompteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nickname', TextType::class, ['label' => 'Pseudo'])
        ->add('email', EmailType::class, ['label' => 'Email'])
        ->add('currentPassword', PasswordType::class, [
            'label' => 'Mot de passe actuel',
            'mapped' => false,
            'required' => false,
            'attr' => ['autocomplete' => 'current-password'],
            'constraints' => [
                new Assert\Length(['min' => 4]),
            ],
        ])
        ->add('plainPassword', RepeatedType::class, [
            'type' => PasswordType::class,
            'mapped' => false,
            'required' => false,
            'first_options' => ['label' => 'Nouveau mot de passe'],
            'second_options' => ['label' => 'Répéter le mot de passe'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
