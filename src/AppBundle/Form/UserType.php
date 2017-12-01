<?php

namespace AppBundle\Form;

use AppBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('login', TextType::class)
            ->add('plainPassword', RepeatedType::class, array(
                'type'            => PasswordType::class,
                'required'        => true,
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
            ))
            ->add('gender', ChoiceType::class, array(
                'label'       => 'Sexe',
                'placeholder' => 'Sélectionnez votre sexe',
                'required'    => false,
                'choices'     => array(
                    'Homme' => 'h',
                    'Femme' => 'f'
                )
            ))
            ->add('email', EmailType::class, array(
                'label' => 'Adresse électronique',
                'required' => false
            ))
            ->add('birthDate', DateType::class, array(
                'label'    => 'Date de naissance',
                'required' => false,
                'years'    => range(date('Y')-70, date('Y')),
                'format'   => 'dd-MM-yyyy',
            ))
            ->add('address', TextType::class, array(
                'label'    => 'Adresse',
                'required' => false
            ))
            ->add('postCode', TextType::class, array(
                'label'    => 'Code postal',
                'required' => false
            ))
            ->add('city', TextType::class, array(
                'label'    => 'Ville',
                'required' => false
            ))
            ->add('phoneNumber', TextType::class, array(
                'label'    => 'Numéro de téléphone',
                'required' => false
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
        ));
    }
}
