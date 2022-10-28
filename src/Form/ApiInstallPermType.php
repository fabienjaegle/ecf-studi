<?php

namespace App\Form;

use App\Entity\ApiInstallPerm;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ApiInstallPermType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('members_read', CheckboxType::class, [
                'row_attr' => ['class' => 'form-check form-switch'],
                'attr' => ['class' => 'form-check-input'],
                'label_attr' => ['class' => 'form-check-label']
            ])
            ->add('members_write', CheckboxType::class, [
                'row_attr' => ['class' => 'form-check form-switch'],
                'attr' => ['class' => 'form-check-input'],
                'label_attr' => ['class' => 'form-check-label']
            ])
            ->add('members_add', CheckboxType::class, [
                'row_attr' => ['class' => 'form-check form-switch'],
                'attr' => ['class' => 'form-check-input'],
                'label_attr' => ['class' => 'form-check-label']
            ])
            ->add('members_products_add', CheckboxType::class, [
                'row_attr' => ['class' => 'form-check form-switch'],
                'attr' => ['class' => 'form-check-input'],
                'label_attr' => ['class' => 'form-check-label']
            ])
            ->add('members_payment_schedules_read', CheckboxType::class, [
                'row_attr' => ['class' => 'form-check form-switch'],
                'attr' => ['class' => 'form-check-input'],
                'label_attr' => ['class' => 'form-check-label']
            ])
            ->add('members_statistiques_read', CheckboxType::class, [
                'row_attr' => ['class' => 'form-check form-switch'],
                'attr' => ['class' => 'form-check-input'],
                'label_attr' => ['class' => 'form-check-label']
            ])
            ->add('members_subscription_read', CheckboxType::class, [
                'row_attr' => ['class' => 'form-check form-switch'],
                'attr' => ['class' => 'form-check-input'],
                'label_attr' => ['class' => 'form-check-label']
            ])
            ->add('payment_schedules_read', CheckboxType::class, [
                'row_attr' => ['class' => 'form-check form-switch'],
                'attr' => ['class' => 'form-check-input'],
                'label_attr' => ['class' => 'form-check-label']
            ])
            ->add('payment_schedules_write', CheckboxType::class, [
                'row_attr' => ['class' => 'form-check form-switch'],
                'attr' => ['class' => 'form-check-input'],
                'label_attr' => ['class' => 'form-check-label']
            ])
            ->add('payment_day_read', CheckboxType::class, [
                'row_attr' => ['class' => 'form-check form-switch'],
                'attr' => ['class' => 'form-check-input'],
                'label_attr' => ['class' => 'form-check-label']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ApiInstallPerm::class,
        ]);
    }
}
