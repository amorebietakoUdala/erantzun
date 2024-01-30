<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Form;

use App\Entity\Erantzuna;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

/**
 * Description of ErantzunaFormType
 *
 * @author ibilbao
 */
class ErantzunaFormType extends AbstractType {

   public function buildForm(FormBuilderInterface $builder, array $options) :void {
		$builder
			->add('erantzuna', TextareaType::class,[
				'attr' => ['class' => 'tinymce'],
				'label' => false,
				'constraints' => [
						],
				])
		;
   }

   public function configureOptions(OptionsResolver $resolver): void {
		$resolver->setDefaults([
			'data_class' => Erantzuna::class,
			'csrf_protection' => false,
		]);
   }

}
