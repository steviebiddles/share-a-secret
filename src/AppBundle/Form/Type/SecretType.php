<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SecretType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('secret', TextType::class, array(
                'required' => true,
            ))
            ->add('views', ChoiceType::class, array(
                'required' => true,
                'data' => 3,
                'choices' => array(1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5),
                'choices_as_values' => true,
            ))
            ->add('expires', ChoiceType::class, array(
                'required' => true,
                'choices' => array(1 => 1, 2 => 2, 3 => 3),
                'choices_as_values' => true,
            ));

        $date = new \DateTime();

        $builder->get('expires')
            ->addModelTransformer(new CallbackTransformer(
                function ($original) use ($date) {
                    if (!empty($original)) {
                        return $date->diff($original)->format('%a');
                    }

                    return $date->diff(new \DateTime('+3 day'))->format('%a');
                },
                function ($submitted) use ($date) {
                    if (!isset($submitted)) {
                        $submitted = 3;
                    }
                    return $date->add(
                        new \DateInterval('P' . $submitted . 'D')
                    );
                }
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Secret',
        ));
    }
}
