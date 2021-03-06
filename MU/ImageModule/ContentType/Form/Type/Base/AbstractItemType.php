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

namespace MU\ImageModule\ContentType\Form\Type\Base;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Zikula\Common\Content\AbstractContentFormType;
use Zikula\Common\Content\ContentTypeInterface;
use Zikula\Common\Translator\TranslatorInterface;
use MU\ImageModule\Entity\Factory\EntityFactory;
use MU\ImageModule\Helper\EntityDisplayHelper;

/**
 * Detail content type form type base class.
 */
abstract class AbstractItemType extends AbstractContentFormType
{
    /**
     * @var EntityFactory
     */
    protected $entityFactory;

    /**
     * @var EntityDisplayHelper
     */
    protected $entityDisplayHelper;

    /**
     * ItemType constructor.
     *
     * @param TranslatorInterface $translator          Translator service instance
     * @param EntityFactory       $entityFactory       EntityFactory service instance
     * @param EntityDisplayHelper $entityDisplayHelper EntityDisplayHelper service instance
     */
    public function __construct(
        TranslatorInterface $translator,
        EntityFactory $entityFactory,
        EntityDisplayHelper $entityDisplayHelper
    ) {
        $this->setTranslator($translator);
        $this->entityFactory = $entityFactory;
        $this->entityDisplayHelper = $entityDisplayHelper;
    }

    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->addObjectTypeField($builder, $options);
        $this->addIdField($builder, $options);
        $this->addDisplayModeField($builder, $options);
        $this->addTemplateField($builder, $options);
    }

    /**
     * Adds an object type field.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addObjectTypeField(FormBuilderInterface $builder, array $options = [])
    {
        $builder->add('objectType', ChoiceType::class, [
            'label' => $this->__('Object type', 'muimagemodule') . ':',
            'empty_data' => 'album',
            'attr' => [
                'title' => $this->__('If you change this please save the element once to reload the parameters below.', 'muimagemodule')
            ],
            'help' => $this->__('If you change this please save the element once to reload the parameters below.', 'muimagemodule'),
            'choices' => [
                $this->__('Albums', 'muimagemodule') => 'album',
                $this->__('Pictures', 'muimagemodule') => 'picture',
                $this->__('Avatars', 'muimagemodule') => 'avatar'
            ],
            'multiple' => false,
            'expanded' => false
        ]);
    }

    /**
     * Adds a item identifier field.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addIdField(FormBuilderInterface $builder, array $options = [])
    {
        $repository = $this->entityFactory->getRepository($options['object_type']);
        // select without joins
        $entities = $repository->selectWhere('', '', false);
    
        $choices = [];
        foreach ($entities as $entity) {
            $choices[$this->entityDisplayHelper->getFormattedTitle($entity)] = $entity->getKey();
        }
        ksort($choices);
    
        $builder->add('id', ChoiceType::class, [
            'multiple' => false,
            'expanded' => false,
            'choices' => $choices,
            'required' => true,
            'label' => $this->__('Entry to display', 'muimagemodule') . ':'
        ]);
    }

    /**
     * Adds a display mode field.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addDisplayModeField(FormBuilderInterface $builder, array $options = [])
    {
        $builder->add('displayMode', ChoiceType::class, [
            'label' => $this->__('Display mode', 'muimagemodule') . ':',
            'label_attr' => [
                'class' => 'radio-inline'
            ],
            'empty_data' => 'embed',
            'choices' => [
                $this->__('Link to object', 'muimagemodule') => 'link',
                $this->__('Embed object display', 'muimagemodule') => 'embed'
            ],
            'multiple' => false,
            'expanded' => true
        ]);
    }

    /**
     * Adds template fields.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addTemplateField(FormBuilderInterface $builder, array $options = [])
    {
        $builder
            ->add('customTemplate', TextType::class, [
                'label' => $this->__('Custom template', 'muimagemodule') . ':',
                'required' => false,
                'attr' => [
                    'maxlength' => 80,
                    'title' => $this->__('Example', 'muimagemodule') . ': displaySpecial.html.twig'
                ],
                'help' => [
                    $this->__('Example', 'muimagemodule') . ': <em>displaySpecial.html.twig</em>',
                    $this->__('Needs to be located in the "External/YourEntity/" directory.', 'muimagemodule')
                ]
            ])
        ;
    }

    /**
     * @inheritDoc
     */
    public function getBlockPrefix()
    {
        return 'muimagemodule_contenttype_detail';
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'context' => ContentTypeInterface::CONTEXT_EDIT,
                'object_type' => 'album'
            ])
            ->setRequired(['object_type'])
            ->setAllowedTypes('context', 'string')
            ->setAllowedTypes('object_type', 'string')
            ->setAllowedValues('context', [ContentTypeInterface::CONTEXT_EDIT, ContentTypeInterface::CONTEXT_TRANSLATION])
        ;
    }
}
