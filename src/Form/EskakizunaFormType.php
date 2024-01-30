<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Form;

use App\Entity\EskakizunMota;
use App\Entity\Jatorria;
use App\Entity\Zerbitzua;
use App\Repository\EskakizunMotaRepository;
use App\Repository\JatorriaRepository;
use App\Repository\ZerbitzuaRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Description of EskakizunaFormType.
 *
 * @author ibilbao
 */
class EskakizunaFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) :void
    {
        $readonly = $options['readonly'];
        $locale = $options['locale'];
        $builder
            ->add('lep', null, [
                'disabled' => $readonly,
            ])
            ->add('noiz', DateTimeType::class, [
                'disabled' => $readonly,
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'yyyy-MM-dd HH:mm',
                'constraints' => [new NotBlank()],
            ])
            ->add('kalea', null, [
                'disabled' => $readonly,
                'required' => true,
            ])
            ->add('zerbitzua', EntityType::class, [
                'disabled' => $readonly,
                'placeholder' => 'messages.hautatu_zerbitzua',
                'class' => Zerbitzua::class,
                'group_by' => 'enpresa',
                'choice_label' => function ($zerbitzua) use ($locale) {
                    if ('es' === $locale) {
                        return $zerbitzua->getIzenaEs();
                    } else {
                        return $zerbitzua->getIzenaEu();
                    }
                },
                'query_builder' => fn(ZerbitzuaRepository $repo) => $repo->createZerbitzuAktiboakQueryBuilder(),
            ])
            // ->add('argazkia', FileType::class, [
            //     'disabled' => $readonly,
            //     'data_class' => null,
            //     'label' => false,
            // ])
            ->add('eskakizunMota', EntityType::class, [
                'disabled' => $readonly,
                'label' => false,
                'placeholder' => 'messages.hautatu_eskakizun_mota',
                'class' => EskakizunMota::class,
                'choice_attr' => fn($choice, $key, $value) => ['class' => 'form-check-input ml-1'],
                'expanded' => true,
                'multiple' => false,
                'query_builder' => fn(EskakizunMotaRepository $repo) => $repo->createOrderedQueryBuilder(),
                'choice_label' => function (EskakizunMota $eskakizunMota) use ($locale) {
                    if ('es' === $locale) {
                        return $eskakizunMota->getDeskripzioaEs();
                    } else {
                        return $eskakizunMota->getDeskripzioaEu();
                    }
                },
            ])
            ->add('jatorria', EntityType::class, [
                'disabled' => $readonly,
                'label' => false,
                'placeholder' => 'messages.hautatu_jatorria',
                'class' => Jatorria::class,
                'choice_attr' => fn($choice, $key, $value) => ['class' => 'form-check-input'],
                'expanded' => true,
                'multiple' => false,
                'choice_label' => function (Jatorria $jatorria) use ($locale) {
                    if ('es' === $locale) {
                        return $jatorria->getDeskripzioaEs();
                    } else {
                        return $jatorria->getDeskripzioaEu();
                    }
                },
                'query_builder' => fn(JatorriaRepository $repo) => $repo->createOrderedQueryBuilder(),
            ])
            ->add('georeferentziazioa', GeoreferentziazioaFormType::class, [
                'label' => false,
            ])
            ->add('eranskinak', CollectionType::class, [
                'entry_type' => EranskinaFormType::class,
                //		'entry_options' => ['label' => 'messages.ezabatu' ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => false,
            ])
            ->add('argazkiak', CollectionType::class, [
                'entry_type' => ArgazkiaFormType::class,
                //		'entry_options' => ['label' => 'messages.ezabatu' ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => false,
            ]);
        if (false === $options['editatzen']) {
            $builder->add('mamia', TextareaType::class, [
                'attr' => ['class' => 'tinymce'],
            ]);
            if (true === $options['readonly']) {
                $builder->add('erantzunak', ErantzunaFormType::class, [
                    'data_class' => null,
                ]);
            }
        } else {
            $builder->add('mamia', TextareaType::class, [
                'attr' => ['class' => 'tinymce readonly'],
                'constraints' => [
                    new NotBlank(),
                ],
            ]);
            $builder->add('erantzunak', ErantzunaFormType::class, [
                'data_class' => null,
            ]);
        }
        if (in_array('ROLE_ADMIN', $options['role']) or in_array('ROLE_ARDURADUNA', $options['role']) or in_array('ROLE_INFORMATZAILEA', $options['role'])) {
            $builder->add('eskatzailea', EskatzaileaFormType::class, [
                'disabled' => $readonly,
                'label' => false,
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired(['editatzen', 'role']);
        $resolver->setDefaults([
            'csrf_protection' => false,
            'readonly' => false,
            'role' => [],
            'locale' => 'es',
        ]);
    }
}
