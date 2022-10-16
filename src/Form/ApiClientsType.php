<?php

namespace App\Form;

use App\Entity\ApiClients;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ApiClientsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('short_description', TextType::class, [
                'label' => 'Description courte',
                'attr' => ['class' => 'form-control']
            ])
            ->add('full_description', TextareaType::class, [
                'label' => 'Description longue',
                'attr' => ['class' => 'form-control']
            ])
            ->add('logo_url', TextType::class, [
                'label' => 'URL du logo',
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('url', TextType::class, [
                'label' => 'URL',
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('dpo', TextType::class, [
                'label' => 'Délégué à la Protection des données (DPO)',
                'attr' => ['class' => 'form-control']
            ])
            ->add('technical_contact', TextType::class, [
                'label' => 'Contact technique',
                'attr' => ['class' => 'form-control']
            ])
            ->add('commercial_contact', TextType::class, [
                'label' => 'Contact commercial',
                'attr' => ['class' => 'form-control']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ApiClients::class,
        ]);
    }
}
