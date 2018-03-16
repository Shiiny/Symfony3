<?php

namespace OC\PlatformBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use OC\PlatformBundle\Repository\CategoryRepository;

class AdvertType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //$pattern = 'D%';

        $builder
            ->add('date',       DateType::class)
            ->add('title',      TextType::class)
            ->add('author',     TextType::class)
            ->add('content',    TextareaType::class)
            ->add('published',  CheckboxType::class, array('required' => false))
            ->add('image',      ImageEntityType::class)
            ->add('categories', EntityType::class, array(
                'class'   => 'OCPlatformBundle:Category',
                'choice_label'    => 'name',
                'multiple' => true,
                /*query_builder' => function(CategoryRepository $repository) use($pattern) {
                    return $repository->getLikeQueryBuilder($pattern);
                }*/
            ))
            ->add('save',       SubmitType::class);

            $builder->addEventListener(
                FormEvents::PRE_SET_DATA, // l'événement
                function(FormEvent $event) { // Fonction à l'exec lors de l'événement
                    // On recupère l'objet Advert
                    $advert = $event->getData();
                    if($advert === null) {
                        return; // on sort
                    }
                    // Si l'annonce est publié ou qu'elle n'existe pas
                    if(!$advert->getPublished() || $advert->getId() === null) {
                        // on ajout le champ "published"
                        $event->getForm()->add('published', CheckboxType::class, array('required' => false));
                    }
                    else {
                        $event->getForm()->remove('published');
                    }
                }
            );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'OC\PlatformBundle\Entity\Advert'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'oc_platformbundle_advert';
    }


}
