<?php

namespace App\Form;

use App\Entity\ApiClientsGrants;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ApiClientsGrantsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('client_id', TextType::class, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('install_id', TextType::class, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('active', TextType::class, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('perms', ApiInstallPermType::class, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('branch_id', TextType::class, [
                'attr' => ['class' => 'form-control']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ApiClientsGrants::class,
        ]);
    }
}
