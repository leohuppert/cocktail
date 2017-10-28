<?php

namespace AppBundle\Form;

use AppBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('login', TextType::class, array(
                'required' => false,
            ))
            ->add('plainPassword', RepeatedType::class, array(
                'type'            => PasswordType::class,
                'required'        => false,
                'invalid_message' => 'Les mots de passe ne correspondent pas',
                'first_options'   => array('label' => 'Mot de passe'),
                'second_options'  => array('label' => 'Répéter le mot de passe'),
            ))
            ->add('firstName', TextType::class, array(
                'label'    => 'Prénom',
                'required' => false,
            ))
            ->add('lastName', TextType::class, array(
                'label'    => 'Nom',
                'required' => false,
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
            'validation_groups' => false
        ));
    }
}