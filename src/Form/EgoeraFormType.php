<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Form;

use App\Entity\Egoera;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Description of EmpresaFormType
 *
 * @author ibilbao
 */
class EgoeraFormType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) :void {
	$builder
	    ->add('deskripzioa_es')
	    ->add('deskripzioa_eu')
	;
    }

    public function configureOptions(OptionsResolver $resolver): void {
	$resolver->setDefaults([
	    'data_class' => Egoera::class
	]);
    }

}
