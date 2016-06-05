<?php

namespace Purethink\CMSBundle\Admin;

use Purethink\CMSBundle\Entity\ExtensionHasField;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Validator\Constraints\NotNull;
use Purethink\CMSBundle\Entity\ComponentHasValue;

class ComponentHasValueAdmin extends Admin
{
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
                $formMapper->add('content', 'ckeditor', [
                    'config_name' => 'default',
                    'required'    => $field->getRequired(),
                    'label'       => $field->getLabelOfField(),
                    'constraints' => $constrains
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
}
