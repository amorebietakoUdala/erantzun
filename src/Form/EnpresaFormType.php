<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Form;

use App\Entity\Enpresa;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

/**
 * Description of EmpresaFormType
 *
 * @author ibilbao
 */
class EnpresaFormType extends AbstractType {
    
    public function buildForm(FormBuilderInterface $builder, array $options) :void {
        $builder
            ->add('izena')
            ->add('ordena')
            ->add('aktibatua', CheckboxType::class,[
                'data' => true,
                'label' => 'messages.aktibatua',
                'label_attr' => ['class' => 'checkbox-inline']                
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void {
	$resolver->setDefaults([
	    'data_class' => Enpresa::class
	]);
    }

}
