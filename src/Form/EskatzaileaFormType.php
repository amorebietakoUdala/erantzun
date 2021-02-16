<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Description of EskatzaileaFormType
 *
 * @author ibilbao
 */
class EskatzaileaFormType extends AbstractType
{

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('id', HiddenType::class)
			->add('nan', TextType::class, [
				// If I enable the following error message appears twice because it's validated here and in the entity. So I'll comment this for now.			 
				// 'constraints' => [new Regex([
				//     'pattern' => '/^\d{7,8}[a-z]$/i',
				//     'message' => 'NANa ez da zuzena'
				// ]),]
			])
			->add('izena', TextType::class, [
				'constraints' => [new NotBlank(),]
			])
			->add('telefonoa')
			->add('faxa')
			->add('helbidea')
			->add('emaila', EmailType::class, [
				// If I enable the following error message appears twice because it's validated here and in the entity. So I'll comment this for now.			 
				//		'constraints' => [new Email(),]
			])
			->add('herria')
			->add('postaKodea')
			//	    ->add('aktibatua', CheckboxType::class)
		;
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => '\App\Entity\Eskatzailea'
		]);
	}
}
