<?php
/**
 * Image.
 *
 * @copyright Michael Ueberschaer (MU)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Michael Ueberschaer <info@homepages-mit-zikula.de>.
 * @link https://homepages-mit-zikula.de
 * @link https://ziku.la
 * @version Generated by ModuleStudio (https://modulestudio.de).
 */

namespace MU\ImageModule\Form\Type\Base;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Common\Translator\TranslatorTrait;
use Zikula\ExtensionsModule\Api\ApiInterface\VariableApiInterface;
use MU\ImageModule\Entity\Factory\EntityFactory;
use MU\ImageModule\Form\Type\Field\TranslationType;
use MU\ImageModule\Form\Type\Field\UploadType;
use MU\ImageModule\Helper\CollectionFilterHelper;
use MU\ImageModule\Helper\EntityDisplayHelper;
use MU\ImageModule\Helper\FeatureActivationHelper;
use MU\ImageModule\Helper\ListEntriesHelper;
use MU\ImageModule\Helper\TranslatableHelper;
use MU\ImageModule\Traits\ModerationFormFieldsTrait;

/**
 * Picture editing form type base class.
 */
abstract class AbstractPictureType extends AbstractType
{
    use TranslatorTrait;
    use ModerationFormFieldsTrait;

    /**
     * @var EntityFactory
     */
    protected $entityFactory;

    /**
     * @var CollectionFilterHelper
     */
    protected $collectionFilterHelper;

    /**
     * @var EntityDisplayHelper
     */
    protected $entityDisplayHelper;

    /**
     * @var VariableApiInterface
     */
    protected $variableApi;

    /**
     * @var TranslatableHelper
     */
    protected $translatableHelper;

    /**
     * @var ListEntriesHelper
     */
    protected $listHelper;

    /**
     * @var FeatureActivationHelper
     */
    protected $featureActivationHelper;

    /**
     * PictureType constructor.
     *
     * @param TranslatorInterface $translator     Translator service instance
     * @param EntityFactory $entityFactory EntityFactory service instance
     * @param CollectionFilterHelper $collectionFilterHelper CollectionFilterHelper service instance
     * @param EntityDisplayHelper $entityDisplayHelper EntityDisplayHelper service instance
     * @param VariableApiInterface $variableApi VariableApi service instance
     * @param TranslatableHelper $translatableHelper TranslatableHelper service instance
     * @param ListEntriesHelper $listHelper ListEntriesHelper service instance
     * @param FeatureActivationHelper $featureActivationHelper FeatureActivationHelper service instance
     */
    public function __construct(
        TranslatorInterface $translator,
        EntityFactory $entityFactory,
        CollectionFilterHelper $collectionFilterHelper,
        EntityDisplayHelper $entityDisplayHelper,
        VariableApiInterface $variableApi,
        TranslatableHelper $translatableHelper,
        ListEntriesHelper $listHelper,
        FeatureActivationHelper $featureActivationHelper
    ) {
        $this->setTranslator($translator);
        $this->entityFactory = $entityFactory;
        $this->collectionFilterHelper = $collectionFilterHelper;
        $this->entityDisplayHelper = $entityDisplayHelper;
        $this->variableApi = $variableApi;
        $this->translatableHelper = $translatableHelper;
        $this->listHelper = $listHelper;
        $this->featureActivationHelper = $featureActivationHelper;
    }

    /**
     * Sets the translator.
     *
     * @param TranslatorInterface $translator Translator service instance
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->addEntityFields($builder, $options);
        $this->addIncomingRelationshipFields($builder, $options);
        $this->addModerationFields($builder, $options);
        $this->addSubmitButtons($builder, $options);
    }

    /**
     * Adds basic entity fields.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addEntityFields(FormBuilderInterface $builder, array $options = [])
    {
        
        $builder->add('title', TextType::class, [
            'label' => $this->__('Title') . ':',
            'empty_data' => '',
            'attr' => [
                'maxlength' => 255,
                'class' => '',
                'title' => $this->__('Enter the title of the picture.')
            ],
            'required' => false,
        ]);
        
        $builder->add('description', TextareaType::class, [
            'label' => $this->__('Description') . ':',
            'help' => $this->__f('Note: this value must not exceed %amount% characters.', ['%amount%' => 2000]),
            'empty_data' => '',
            'attr' => [
                'maxlength' => 2000,
                'class' => '',
                'title' => $this->__('Enter the description of the picture.')
            ],
            'required' => false,
        ]);
        
        if ($this->variableApi->getSystemVar('multilingual') && $this->featureActivationHelper->isEnabled(FeatureActivationHelper::TRANSLATIONS, 'picture')) {
            $supportedLanguages = $this->translatableHelper->getSupportedLanguages('picture');
            if (is_array($supportedLanguages) && count($supportedLanguages) > 1) {
                $currentLanguage = $this->translatableHelper->getCurrentLanguage();
                $translatableFields = $this->translatableHelper->getTranslatableFields('picture');
                $mandatoryFields = $this->translatableHelper->getMandatoryFields('picture');
                foreach ($supportedLanguages as $language) {
                    if ($language == $currentLanguage) {
                        continue;
                    }
                    $builder->add('translations' . $language, TranslationType::class, [
                        'fields' => $translatableFields,
                        'mandatory_fields' => $mandatoryFields[$language],
                        'values' => isset($options['translations'][$language]) ? $options['translations'][$language] : []
                    ]);
                }
            }
        }
        
        $builder->add('imageUpload', UploadType::class, [
            'label' => $this->__('Image upload') . ':',
            'help' => [$this->__f('Note: the image must have a width between %min% and %max% pixels.', ['%min%' => 400, '%max%' => 1000]), $this->__f('Note: the image must have a height between %min% and %max% pixels.', ['%min%' => 400, '%max%' => 1000])],
            'attr' => [
                'class' => ' validate-upload',
                'title' => $this->__('Enter the image upload of the picture.')
            ],
            'required' => true && $options['mode'] == 'create',
            'entity' => $options['entity'],
            'allowed_extensions' => 'gif, jpeg, jpg, png',
            'allowed_size' => '200k'
        ]);
        
        $builder->add('imageView', IntegerType::class, [
            'label' => $this->__('Image view') . ':',
            'empty_data' => 0,
            'attr' => [
                'maxlength' => 11,
                'class' => '',
                'title' => $this->__('Enter the image view of the picture.') . ' ' . $this->__('Only digits are allowed.')
            ],
            'required' => false,
            'scale' => 0
        ]);
        
        $builder->add('albumImage', CheckboxType::class, [
            'label' => $this->__('Album image') . ':',
            'attr' => [
                'class' => '',
                'title' => $this->__('album image ?')
            ],
            'required' => false,
        ]);
        
        $builder->add('pos', IntegerType::class, [
            'label' => $this->__('Pos') . ':',
            'empty_data' => 1,
            'attr' => [
                'maxlength' => 11,
                'class' => '',
                'title' => $this->__('Enter the pos of the picture.') . ' ' . $this->__('Only digits are allowed.')
            ],
            'required' => true,
            'scale' => 0
        ]);
    }

    /**
     * Adds fields for incoming relationships.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addIncomingRelationshipFields(FormBuilderInterface $builder, array $options = [])
    {
        $queryBuilder = function(EntityRepository $er) {
            // select without joins
            return $er->getListQueryBuilder('', '', false);
        };
        $entityDisplayHelper = $this->entityDisplayHelper;
        $choiceLabelClosure = function ($entity) use ($entityDisplayHelper) {
            return $entityDisplayHelper->getFormattedTitle($entity);
        };
        $builder->add('album', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', [
            'class' => 'MUImageModule:AlbumEntity',
            'choice_label' => $choiceLabelClosure,
            'multiple' => false,
            'expanded' => false,
            'query_builder' => $queryBuilder,
            'placeholder' => $this->__('Please choose an option.'),
            'required' => false,
            'label' => $this->__('Album'),
            'attr' => [
                'title' => $this->__('Choose the album.')
            ]
        ]);
    }

    /**
     * Adds submit buttons.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addSubmitButtons(FormBuilderInterface $builder, array $options = [])
    {
        foreach ($options['actions'] as $action) {
            $builder->add($action['id'], SubmitType::class, [
                'label' => $action['title'],
                'icon' => ($action['id'] == 'delete' ? 'fa-trash-o' : ''),
                'attr' => [
                    'class' => $action['buttonClass']
                ]
            ]);
            if ($options['mode'] == 'create' && $action['id'] == 'submit' && !$options['inline_usage']) {
                // add additional button to submit item and return to create form
                $builder->add('submitrepeat', SubmitType::class, [
                    'label' => $this->__('Submit and repeat'),
                    'icon' => 'fa-repeat',
                    'attr' => [
                        'class' => $action['buttonClass']
                    ]
                ]);
            }
        }
        $builder->add('reset', ResetType::class, [
            'label' => $this->__('Reset'),
            'icon' => 'fa-refresh',
            'attr' => [
                'class' => 'btn btn-default',
                'formnovalidate' => 'formnovalidate'
            ]
        ]);
        $builder->add('cancel', SubmitType::class, [
            'label' => $this->__('Cancel'),
            'icon' => 'fa-times',
            'attr' => [
                'class' => 'btn btn-default',
                'formnovalidate' => 'formnovalidate'
            ]
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getBlockPrefix()
    {
        return 'muimagemodule_picture';
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                // define class for underlying data (required for embedding forms)
                'data_class' => 'MU\ImageModule\Entity\PictureEntity',
                'empty_data' => function (FormInterface $form) {
                    return $this->entityFactory->createPicture();
                },
                'error_mapping' => [
                    'imageUpload' => 'imageUpload.imageUpload',
                ],
                'mode' => 'create',
                'actions' => [],
                'has_moderate_permission' => false,
                'allow_moderation_specific_creator' => false,
                'allow_moderation_specific_creation_date' => false,
                'translations' => [],
                'filter_by_ownership' => true,
                'inline_usage' => false
            ])
            ->setRequired(['entity', 'mode', 'actions'])
            ->setAllowedTypes('mode', 'string')
            ->setAllowedTypes('actions', 'array')
            ->setAllowedTypes('has_moderate_permission', 'bool')
            ->setAllowedTypes('allow_moderation_specific_creator', 'bool')
            ->setAllowedTypes('allow_moderation_specific_creation_date', 'bool')
            ->setAllowedTypes('translations', 'array')
            ->setAllowedTypes('filter_by_ownership', 'bool')
            ->setAllowedTypes('inline_usage', 'bool')
            ->setAllowedValues('mode', ['create', 'edit'])
        ;
    }
}
