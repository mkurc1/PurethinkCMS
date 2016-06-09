<?php

namespace Purethink\CMSBundle\Admin;

use Purethink\CMSBundle\Entity\MenuAction;
use Purethink\CMSBundle\Entity\MenuArticle;
use Purethink\CMSBundle\Entity\MenuSection;
use Purethink\CMSBundle\Entity\MenuUrl;
use Purethink\CMSBundle\Service\AvailableRoute;
use Purethink\CMSBundle\Service\Language;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\Filter\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class MenuAdmin extends Admin
{
    /**
     * @var AvailableRoute
     */
    private $availableRoute;

    /**
     * @var Language
     */
    private $language;

    protected $parentAssociationMapping = 'type';

    protected $datagridValues = [
        '_sort_by'  => 'name',
        'createdAt' => ['type' => DateType::TYPE_GREATER_THAN],
        'updatedAt' => ['type' => DateType::TYPE_GREATER_THAN]
    ];

    protected function configureFormFields(FormMapper $formMapper)
    {
        $menu = $this->getSubject();

        $formMapper
            ->with('admin.general', ['class' => 'col-md-8'])
            ->add('translations', 'a2lix_translations', [
                'label'          => false,
                'locales'        => $this->language->getAvailableLocales(),
                'fields'         => [
                    'name' => [
                        'label' => 'admin.menu.name',
                    ]
                ],
                'exclude_fields' => ['createdAt', 'updatedAt', 'deletedAt']
            ]);

        if ($menu instanceof MenuArticle) {
            $formMapper->add('article', 'sonata_type_model_list', [
                'label'   => 'admin.menu.article',
                'btn_add' => false
            ]);
        }

        if ($menu instanceof MenuUrl) {
            $formMapper->add('url', 'url', [
                'label'    => 'admin.menu.url',
                'required' => true
            ]);
        }

        if ($menu instanceof MenuSection) {
            $formMapper->add('section', null, [
                'label'    => 'admin.menu.section',
                'required' => true
            ]);
        }

        if ($menu instanceof MenuAction) {
            $formMapper->add('action', ChoiceType::class, [
                'label'    => 'admin.menu.action',
                'choices'  => $this->availableRoute->getRoutes(),
                'required' => true
            ]);
        }

        $formMapper
            ->add('menu', 'sonata_type_model_list', [
                'label'    => 'admin.menu.menu',
                'help'     => 'admin.menu.menu_help',
                'required' => false,
                'btn_add'  => false
            ])
            ->end()
            ->with('admin.options', ['class' => 'col-md-4'])
            ->add('published', null, [
                'label' => 'admin.menu.published'
            ])
            ->add('isNewPage', null, [
                'label' => 'admin.menu.is_new_page'
            ])
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id', null, [
                'label' => 'admin.id'
            ])
            ->add('translations.name', null, [
                'label' => 'admin.menu.name'
            ])
            ->add('published', null, [
                'label' => 'admin.menu.published'
            ])
            ->add('createdAt', 'doctrine_orm_datetime', [
                'label'         => 'admin.created_at',
                'field_type'    => 'sonata_type_datetime_picker',
                'field_options' => [
                    'format' => 'dd MMM yyyy, HH:mm',
                ]
            ])
            ->add('updatedAt', 'doctrine_orm_datetime', [
                'label'         => 'admin.updated_at',
                'field_type'    => 'sonata_type_datetime_picker',
                'field_options' => [
                    'format' => 'dd MMM yyyy, HH:mm',
                ]
            ]);
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id', null, [
                'label' => 'admin.id'
            ])
            ->addIdentifier('name', null, [
                'label' => 'admin.menu.name'
            ])
            ->add('menu', null, [
                'label' => 'admin.menu.menu'
            ])
            ->add('position', null, [
                'label'    => 'admin.menu.position',
                'editable' => true
            ])
            ->add('typeOf', null, [
                'label' => 'admin.menu.type_of_link'
            ])
            ->add('published', null, [
                'label'    => 'admin.menu.published',
                'editable' => true
            ])
            ->add('createdAt', null, [
                'label' => 'admin.created_at'
            ])
            ->add('updatedAt', null, [
                'label' => 'admin.updated_at'
            ]);
    }

    public function setAvailableRoute(AvailableRoute $availableRoute)
    {
        $this->availableRoute = $availableRoute;
    }

    public function setLanguageService(Language $language)
    {
        $this->language = $language;
    }
}
