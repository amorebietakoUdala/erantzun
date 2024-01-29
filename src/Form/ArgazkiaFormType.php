<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Form;

use App\Entity\Argazkia;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;


/**
 * Description of ArgazkiaFormType
 *
 * @author ibilbao
 */
class ArgazkiaFormType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) :void {
	$builder
	    ->add('imageFile', VichImageType::class,[
			'required' => false,
			'label' => false,
			'by_reference' => false,
			'allow_delete' => true,
			'download_uri' => false,
	//		'download_label' => 'download_file',
	//		'image_uri' => true,
			'attr' => ['class' => 'js-file'],
			'constraints' => [
	//		    new NotBlank(),
		    ],
	    ])
	;
    }

    public function configureOptions(OptionsResolver $resolver): void {
	$resolver->setDefaults([
	    'data_class' => Argazkia::class,
	    'csrf_protection' => false,
	]);
    }

}
