<?php
/**
 * MUImage.
 *
 * @copyright Michael Ueberschaer (MU)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Michael Ueberschaer <kontakt@webdesign-in-bremen.com>.
 * @link http://www.webdesign-in-bremen.com
 * @link http://zikula.org
 * @version Generated by ModuleStudio (http://modulestudio.de).
 */

namespace MU\MUImageModule\Form\Type\QuickNavigation\Base;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Common\Translator\TranslatorTrait;
use MU\MUImageModule\Helper\ListEntriesHelper;

/**
 * Album quick navigation form type base class.
 */
class AlbumQuickNavType extends AbstractType
{
    use TranslatorTrait;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var ListEntriesHelper
     */
    protected $listHelper;

    /**
     * AlbumQuickNavType constructor.
     *
     * @param TranslatorInterface $translator   Translator service instance
     * @param RequestStack        $requestStack RequestStack service instance
     * @param ListEntriesHelper   $listHelper   ListEntriesHelper service instance
     */
    public function __construct(TranslatorInterface $translator, RequestStack $requestStack, ListEntriesHelper $listHelper)
    {
        $this->setTranslator($translator);
        $this->requestStack = $requestStack;
        $this->listHelper = $listHelper;
    }

    /**
     * Sets the translator.
     *
     * @param TranslatorInterface $translator Translator service instance
     */
    public function setTranslator(/*TranslatorInterface */$translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setMethod('GET')
            ->add('all', 'Symfony\Component\Form\Extension\Core\Type\HiddenType', [
                'data' => $options['all'],
                'empty_data' => 0
            ])
            ->add('own', 'Symfony\Component\Form\Extension\Core\Type\HiddenType', [
                'data' => $options['own'],
                'empty_data' => 0
            ])
        ;

        $this->addCategoriesField($builder, $options);
        $this->addIncomingRelationshipFields($builder, $options);
        $this->addListFields($builder, $options);
        $this->addSearchField($builder, $options);
        $this->addSortingFields($builder, $options);
        $this->addAmountField($builder, $options);
        $this->addBooleanFields($builder, $options);
        $builder->add('updateview', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', [
            'label' => $this->__('OK'),
            'attr' => [
                'id' => 'quicknavSubmit',
                'class' => 'btn btn-default btn-sm'
            ]
        ]);
    }

    /**
     * Adds a categories field.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addCategoriesField(FormBuilderInterface $builder, array $options)
    {
        $objectType = 'album';
    
        $builder->add('categories', 'Zikula\CategoriesModule\Form\Type\CategoriesType', [
            'label' => $this->__('Category'),
            'empty_data' => [],
            'attr' => [
                'class' => 'input-sm category-selector',
                'title' => $this->__('This is an optional filter.')
            ],
            'help' => $this->__('This is an optional filter.'),
            'required' => false,
            'multiple' => false,
            'module' => 'MUMUImageModule',
            'entity' => ucfirst($objectType) . 'Entity',
            'entityCategoryClass' => 'MU\MUImageModule\Entity\\' . ucfirst($objectType) . 'CategoryEntity'
        ]);
    }

    /**
     * Adds fields for incoming relationships.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addIncomingRelationshipFields(FormBuilderInterface $builder, array $options)
    {
        $mainSearchTerm = '';
        $request = $this->requestStack->getCurrentRequest();
        if ($request->query->has('q')) {
            // remove current search argument from request to avoid filtering related items
            $mainSearchTerm = $request->query->get('q');
            $request->query->remove('q');
        }
    
        $builder->add('parent', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', [
            'class' => 'MUMUImageModule:AlbumEntity',
            'choice_label' => 'getTitleFromDisplayPattern',
            'placeholder' => $this->__('All'),
            'required' => false,
            'label' => $this->__('Parent'),
            'attr' => [
                'id' => 'parent',
                'class' => 'input-sm'
            ]
        ]);
    
        if ($mainSearchTerm != '') {
            // readd current search argument
            $request->query->set('q', $mainSearchTerm);
        }
    }

    /**
     * Adds list fields.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addListFields(FormBuilderInterface $builder, array $options)
    {
        $listEntries = $this->listHelper->getEntries('album', 'workflowState');
        $choices = [];
        $choiceAttributes = [];
        foreach ($listEntries as $entry) {
            $choices[$entry['text']] = $entry['value'];
            $choiceAttributes[$entry['text']] = ['title' => $entry['title']];
        }
        $builder->add('workflowState', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', [
            'label' => $this->__('Workflow state'),
            'attr' => [
                'class' => 'input-sm'
            ],
            'required' => false,
            'placeholder' => $this->__('All'),
            'choices' => $choices,
            'choices_as_values' => true,
            'choice_attr' => $choiceAttributes,
            'multiple' => false,
            'expanded' => false
        ]);
        $listEntries = $this->listHelper->getEntries('album', 'albumAccess');
        $choices = [];
        $choiceAttributes = [];
        foreach ($listEntries as $entry) {
            $choices[$entry['text']] = $entry['value'];
            $choiceAttributes[$entry['text']] = ['title' => $entry['title']];
        }
        $builder->add('albumAccess', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', [
            'label' => $this->__('Album access'),
            'attr' => [
                'class' => 'input-sm'
            ],
            'required' => false,
            'placeholder' => $this->__('All'),
            'choices' => $choices,
            'choices_as_values' => true,
            'choice_attr' => $choiceAttributes,
            'multiple' => false,
            'expanded' => false
        ]);
    }

    /**
     * Adds a search field.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addSearchField(FormBuilderInterface $builder, array $options)
    {
        $builder->add('q', 'Symfony\Component\Form\Extension\Core\Type\SearchType', [
            'label' => $this->__('Search'),
            'attr' => [
                'id' => 'searchTerm',
                'class' => 'input-sm'
            ],
            'required' => false,
            'max_length' => 255
        ]);
    }


    /**
     * Adds sorting fields.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addSortingFields(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('sort', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', [
                'label' => $this->__('Sort by'),
                'attr' => [
                    'id' => 'mUMUImageModuleSort',
                    'class' => 'input-sm'
                ],
                'choices' => [
                    $this->__('Id') => 'id',
                    $this->__('Title') => 'title',
                    $this->__('Description') => 'description',
                    $this->__('Parent_id') => 'parent_id',
                    $this->__('Album access') => 'albumAccess',
                    $this->__('Password access') => 'passwordAccess',
                    $this->__('My friends') => 'myFriends',
                    $this->__('Not in frontend') => 'notInFrontend',
                    $this->__('Creation date') => 'createdDate',
                    $this->__('Creator') => 'createdUserId',
                    $this->__('Update date') => 'updatedDate'
                ],
                'choices_as_values' => true,
                'required' => false,
                'expanded' => false
            ])
            ->add('sortdir', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', [
                'label' => $this->__('Sort direction'),
                'empty_data' => 'asc',
                'attr' => [
                    'id' => 'mUMUImageModuleSortDir',
                    'class' => 'input-sm'
                ],
                'choices' => [
                    $this->__('Ascending') => 'asc',
                    $this->__('Descending') => 'desc'
                ],
                'choices_as_values' => true,
                'required' => false,
                'expanded' => false
            ])
        ;
    }

    /**
     * Adds a page size field.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addAmountField(FormBuilderInterface $builder, array $options)
    {
        $builder->add('num', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', [
            'label' => $this->__('Page size'),
            'empty_data' => 20,
            'attr' => [
                'id' => 'mUMUImageModulePageSize',
                'class' => 'input-sm text-right'
            ],
            'choices' => [
                5 => 5,
                10 => 10,
                15 => 15,
                20 => 20,
                30 => 30,
                50 => 50,
                100 => 100
            ],
            'choices_as_values' => true,
            'required' => false,
            'expanded' => false
        ]);
    }

    /**
     * Adds boolean fields.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addBooleanFields(FormBuilderInterface $builder, array $options)
    {
        $builder->add('notInFrontend', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', [
            'label' => $this->__('Not in frontend'),
            'attr' => [
                'class' => 'input-sm'
            ],
            'required' => false,
            'placeholder' => $this->__('All'),
            'choices' => [
                $this->__('No') => 'no',
                $this->__('Yes') => 'yes'
            ],
            'choices_as_values' => true
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'mumuimagemodule_albumquicknav';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'all' => 0,
                'own' => 0
            ])
            ->setRequired(['all', 'own'])
            ->setAllowedValues([
                'all' => [0, 1],
                'own' => [0, 1]
            ])
        ;
    }
}
