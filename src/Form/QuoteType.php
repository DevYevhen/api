<?php

namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Quote;
use App\Form\AuthorType;

class QuoteType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('author', AuthorType::class)
            ->add('body');
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => Quote::class,
            //will used in REST controller, disable csrf protection
            'csrf_protection' => false
        ]);
    }
}

// vim: sw=4:ts=4:ft=php:expandtab:
