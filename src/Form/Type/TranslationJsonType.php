<?php
declare(strict_types=1);

namespace App\Form\Type;

use App\Repository\LanguageRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TranslationJsonType extends AbstractType
{
    /**
     * @var \App\Repository\LanguageRepository $languageRepository
     */
    private LanguageRepository $languageRepository;

    /**
     * @param \App\Repository\LanguageRepository $languageRepository
     */
    public function __construct(LanguageRepository $languageRepository)
    {
        $this->languageRepository = $languageRepository;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $languages = $this->languageRepository->findAll();

        foreach ($languages as $language) {
            $builder->add($language->getCode(), $options['type']);
        }
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'type' => TextType::class,
        ]);
        $resolver->setAllowedTypes('type', 'string');
        parent::configureOptions($resolver);
    }
}