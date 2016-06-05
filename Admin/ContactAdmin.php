<?php

namespace Purethink\CMSBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\Filter\DateType;

class ContactAdmin extends Admin
{
    protected $datagridValues = [
        '_sort_by'    => 'createdAt',
        '_sort_order' => 'DESC',
        'createdAt'   => ['type' => DateType::TYPE_GREATER_THAN]
    ];

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('admin.general', ['class' => 'col-md-8'])
            ->add('name', null, [
                'label' => 'admin.contact.name'
            ])
            ->add('email', null, [
                'label' => 'admin.contact.email'
            ])
            ->add('subject', null, [
                'label' => 'admin.contact.subject'
            ])
            ->add('message', null, [
                'label' => 'admin.contact.message'
            ])
            ->end()
            ->with('admin.options', ['class' => 'col-md-4'])
            ->add('response', null, [
                'label'    => 'admin.contact.response',
                'required' => false
            ])
            ->end();
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name', null, [
                'label' => 'admin.contact.name'
            ])
            ->add('email', null, [
                'label' => 'admin.contact.email'
            ])
            ->add('response', 'boolean', [
                'editable' => true,
                'label'    => 'admin.contact.response'
            ])
            ->add('subject', null, [
                'label' => 'admin.contact.subject'
            ])
            ->add('createdAt', null, [
                'label' => 'admin.created_at'
            ]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name', null, [
                'label' => 'admin.contact.name'
            ])
            ->add('email', null, [
                'label' => 'admin.contact.email'
            ])
            ->add('subject', null, [
                'label' => 'admin.contact.subject'
            ])
            ->add('createdAt', 'doctrine_orm_datetime', [
                'label'         => 'admin.created_at',
                'field_type'    => 'sonata_type_datetime_picker',
                'field_options' => [
                    'format' => 'dd MMM yyyy, HH:mm',
                ]
            ]);
    }
}