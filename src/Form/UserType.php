<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email')
            ->add('roles', ChoiceType::class, [
                'mapped' => false,
                'choices' => [
                    'Administrateur' => 'ROLE_ADMIN',
                    'Rédacteur' => 'ROLE_EDITOR',
                    'Utilisateur' => 'ROLE_USER'
                ]
            ])
            ->add('firstName')
            ->add('lastName')
            ->add('postalAddress')
            ->add('postalCode')
            ->add('city')
            ->add('isNewsletter')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
