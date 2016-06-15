<?php

namespace Purethink\CMSBundle\Admin;

use Purethink\CMSBundle\Entity\ExtensionHasField;
use Purethink\CMSBundle\Service\Language;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Validator\Constraints\NotNull;
use Purethink\CMSBundle\Entity\ComponentHasValue;

class ComponentHasValueAdmin extends Admin
{
    /** @var Language */
    private $language;

    protected function configureFormFields(FormMapper $formMapper)
    {
        $object = $this->getSubject();
        /** @var ComponentHasValue $object */
        $field = $object->getExtensionHasField();

        $constrains = $field->getRequired() ? [new NotNull()] : [];

        switch ($field->getTypeOfField()) {
            case ExtensionHasField::TYPE_BOOLEAN:
                $formMapper->add('boolean', 'sonata_type_translatable_choice', [
                    'required'    => $field->getRequired(),
                    'label'       => $field->getLabelOfField(),
                    'constraints' => $constrains,
                    'choices'     => [
                        1 => 'label_type_yes',
                        0 => 'label_type_no'
                    ]
                ]);
                break;
            case ExtensionHasField::TYPE_DATE:
                $formMapper->add('date', 'sonata_type_date_picker', [
                    'format'      => 'yyyy-MM-dd',
                    'required'    => $field->getRequired(),
                    'label'       => $field->getLabelOfField(),
                    'constraints' => $constrains
                ]);
                break;
            case ExtensionHasField::TYPE_DATETIME:
                $formMapper->add('date', 'sonata_type_datetime_picker', [
                    'format'      => 'HH:mm:ss yyyy-MM-dd',
                    'required'    => $field->getRequired(),
                    'label'       => $field->getLabelOfField(),
                    'constraints' => $constrains
                ]);
                break;
            case ExtensionHasField::TYPE_ARTICLE:
                $formMapper->add('article', 'sonata_type_model_list', [
                    'required'    => $field->getRequired(),
                    'btn_delete'  => !$field->getRequired() ? 'link_delete' : false,
                    'btn_add'     => false,
                    'label'       => $field->getLabelOfField(),
                    'constraints' => $constrains
                ]);
                break;
            case ExtensionHasField::TYPE_FILE:
                $formMapper->add('file', 'sonata_type_model_list', [
                    'required'    => $field->getRequired(),
                    'btn_delete'  => !$field->getRequired() ? 'link_delete' : false,
                    'label'       => $field->getLabelOfField(),
                    'constraints' => $constrains
                ]);
                break;
            case ExtensionHasField::TYPE_WYSIWYG:
                $formMapper->add('translations', 'a2lix_translations', [
                    'label'          => false,
                    'locales'        => $this->language->getAvailableLocales(),
                    'fields'         => [
                        'text' => [
                            'field_type'  => 'ckeditor',
                            'config_name' => 'default',
                            'required'    => $field->getRequired(),
                            'constraints' => $constrains,
                            'label'       => $field->getLabelOfField()
                        ]
                    ],
                    'exclude_fields' => ['createdAt', 'updatedAt', 'deletedAt']
                ]);
                break;
            case ExtensionHasField::TYPE_TEXT:
            case ExtensionHasField::TYPE_TEXTAREA:
                $formMapper->add('translations', 'a2lix_translations', [
                    'label'          => false,
                    'locales'        => $this->language->getAvailableLocales(),
                    'fields'         => [
                        'text' => [
                            'field_type'  => $field->getTypeOfFieldString(),
                            'required'    => $field->getRequired(),
                            'constraints' => $constrains,
                            'label'       => $field->getLabelOfField()
                        ]
                    ],
                    'exclude_fields' => ['createdAt', 'updatedAt', 'deletedAt']
                ]);
                break;
            default:
                $formMapper->add('content', $field->getTypeOfFieldString(), [
                    'required'    => $field->getRequired(),
                    'label'       => $field->getLabelOfField(),
                    'constraints' => $constrains
                ]);
        }
    }

    public function setLanguageService(Language $language)
    {
        $this->language = $language;
    }
}
