<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Form;

use App\Entity\Eranskina;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

/**
 * Description of EranskinaFormType
 *
 * @author ibilbao
 */
class EranskinaFormType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) :void {
	$builder
	    ->add('eranskinaFile', VichFileType::class,[
		'required' => false,
		'allow_delete' => true,
		'download_uri' => true,
		'download_label' => fn(Eranskina $eranskina) => $eranskina->getEranskinaName(),
//		'image_uri' => true,
		'attr' => ['class' => 'js-file'],
		'label' => false,
		'constraints' => [
//		    new NotBlank(),
		    ],
	    ])
	;
    }

    public function configureOptions(OptionsResolver $resolver): void {
	$resolver->setDefaults([
	    'data_class' => Eranskina::class,
	    'csrf_protection' => false,
	]);
    }

}
