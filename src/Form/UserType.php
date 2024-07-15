<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('roles', CollectionType::class, [
                'entry_type'   => ChoiceType::class,
                'entry_options'  => [
                    'label'=>false,
                    'choices'  => [
                        'ADMIN' => 'ROLE_ADMIN',
                        'USER'     => 'ROLE_USER',
                    ],
                ],
            ])
            ->add('password')
            ->add('lastname')
            ->add('firstname')
            ->add('adress')
            ->add('zipcode')
            ->add('city')
            ->add('hasContrat', CheckboxType::class, [
                'label'    => 'L\'utilisateur a-t-il un contrat ?',
                'required' => false,
                'mapped' => false, // Ne pas mapper ce champ à l'entité User directement
            ])
        ;

            
            
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
