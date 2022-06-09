<?php
declare(strict_types=1);

namespace App\Form\Type;

use App\Repository\LanguageRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

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
            $builder->add($language->getCode());
        }
    }
}