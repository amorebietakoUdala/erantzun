<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Form;

use App\Entity\Zerbitzua;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use App\Entity\Enpresa;
use App\Repository\EnpresaRepository;

/**
 * Description of ZerbitzuaFormType
 *
 * @author ibilbao
 */
class ZerbitzuaFormType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) :void {
		$builder
			->add('izena_es')
			->add('izena_eu')
			->add('enpresa', EntityType::class, [
				'placeholder'=>'messages.hautatu_enpresa',
				'class' => Enpresa::class,
				'query_builder' => fn(EnpresaRepository $repo) => $repo->createOrderedQueryBuilder()
			])
			->add('ordena')
			->add('aktibatua', CheckboxType::class, [
					'data' => true,
					'label' => 'messages.aktibatua',
					'label_attr' => ['class' => 'checkbox-inline']
			])
		;
    }

    public function configureOptions(OptionsResolver $resolver): void {
	$resolver->setDefaults([
	    'data_class' => Zerbitzua::class
	]);
    }

}
