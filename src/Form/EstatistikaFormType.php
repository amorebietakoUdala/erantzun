<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Form;

use App\Entity\Enpresa;
use App\Repository\EnpresaRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Description of StatsFormType.
 *
 * @author ibilbao
 */
class EstatistikaFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) :void
    {
        $builder
        ->add('noiztik', DateTimeType::class, [
            'widget' => 'single_text',
            'html5' => false,
            'format' => 'yyyy-MM-dd HH:mm',
            'attr' => ['class' => 'js-datepicker'],
        ])
        ->add('nora', DateTimeType::class, [
            'widget' => 'single_text',
            'html5' => false,
            'format' => 'yyyy-MM-dd HH:mm',
            'attr' => ['class' => 'js-datepicker'],
        ])
        ->add('enpresa', EntityType::class, [
            'placeholder' => 'messages.hautatu_enpresa',
            'class' => Enpresa::class,
            'query_builder' => fn(EnpresaRepository $repo) => $repo->createOrderedQueryBuilder(),
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => true,
        ]);
    }
}
