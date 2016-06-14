<?php

namespace Purethink\CMSBundle\Admin;

use Purethink\CMSBundle\Service\Language;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class SiteAdmin extends Admin
{
    /** @var Language */
    private $language;

    protected $formOptions = [
        'cascade_validation' => true,
        'validation_groups'  => ['site', 'Default']
    ];

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->remove('show')
            ->remove('create')
            ->remove('batch')
            ->remove('export')
            ->remove('delete');
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('admin.general', ['class' => 'col-md-8'])
            ->add('translations', 'a2lix_translations', [
                'label'          => false,
                'locales'        => $this->language->getAvailableLocales(),
                'fields'         => [
                    'title'       => [
                        'label' => 'admin.metadata.title',
                    ],
                    'keyword'     => [
                        'field_type' => 'textarea',
                        'label'      => 'admin.metadata.keyword'
                    ],
                    'description' => [
                        'field_type' => 'textarea',
                        'label'      => 'admin.metadata.description'
                    ]
                ],
                'exclude_fields' => ['createdAt', 'updatedAt', 'deletedAt']
            ])
            ->end()
            ->with('admin.options', ['class' => 'col-md-4'])
            ->add('contactEmail', null, [
                'label' => 'admin.site.contact_email'
            ])
            ->add('sendContactRequestOnEmail', null, [
                'label'    => 'admin.site.send_contact_request_on_email',
                'required' => false
            ])
            ->add('trackingCode', 'textarea', [
                'label'    => 'admin.site.tracking_code',
                'required' => false,
                'attr'     => [
                    'rows' => 6
                ]
            ])
            ->add('addTitleToSubPages', null, [
                'label'    => 'admin.site.add_title_to_sub_pages',
                'required' => false
            ])
            ->end();
    }

    public function toString($object)
    {
        return $this->trans('admin.sidebar.site');
    }

    public function setLanguageService(Language $language)
    {
        $this->language = $language;
    }
}
