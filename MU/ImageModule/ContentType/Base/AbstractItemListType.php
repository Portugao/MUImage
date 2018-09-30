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

namespace MU\ImageModule\ContentType\Base;

use Zikula\Common\Content\AbstractContentType;
use MU\ImageModule\ContentType\Form\Type\ItemListType as FormType;
use MU\ImageModule\Entity\Factory\EntityFactory;
use MU\ImageModule\Helper\CategoryHelper;
use MU\ImageModule\Helper\ControllerHelper;
use MU\ImageModule\Helper\FeatureActivationHelper;
use MU\ImageModule\Helper\ModelHelper;

/**
 * Generic item list content type base class.
 */
abstract class AbstractItemListType extends AbstractContentType
{
    /**
     * @var ControllerHelper
     */
    protected $controllerHelper;
    
    /**
     * @var ModelHelper
     */
    protected $modelHelper;
    
    /**
     * @var EntityFactory
     */
    protected $entityFactory;
    
    /**
     * @var FeatureActivationHelper
     */
    protected $featureActivationHelper;
    
    /**
     * @var CategoryHelper
     */
    protected $categoryHelper;
    
    /**
     * List of object types allowing categorisation.
     *
     * @var array
     */
    protected $categorisableObjectTypes;
    
    /**
     * @inheritDoc
     */
    public function getIcon()
    {
        return 'th-list';
    }
    
    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return $this->translator->__('Image list', 'muimagemodule');
    }
    
    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return $this->translator->__('Display a list of image objects.', 'muimagemodule');
    }
    
    /**
     * @inheritDoc
     */
    public function getDefaultData()
    {
        return [
            'objectType' => 'album',
            'sorting' => 'default',
            'amount' => 1,
            'template' => 'itemlist_display.html.twig',
            'customTemplate' => null,
            'filter' => ''
        ];
    }
    
    /**
     * @inheritDoc
     */
    public function getData()
    {
        $data = parent::getData();
    
        $contextArgs = ['name' => 'list'];
        if (!isset($data['objectType']) || !in_array($data['objectType'], $this->controllerHelper->getObjectTypes('contentType', $contextArgs))) {
            $data['objectType'] = $this->controllerHelper->getDefaultObjectType('contentType', $contextArgs);
        }
    
        if (!isset($data['template'])) {
            $data['template'] = 'itemlist_' . $data['objectType'] . '_display.html.twig';
        }
    
        $objectType = $data['objectType'];
        if ($this->featureActivationHelper->isEnabled(FeatureActivationHelper::CATEGORIES, $objectType)) {
            $this->categorisableObjectTypes = ['album', 'avatar'];
    
            $primaryRegistry = $this->categoryHelper->getPrimaryProperty($objectType);
            if (!isset($data['categories'])) {
                $data['categories'] = [$primaryRegistry => []];
            } else {
                if (!is_array($data['categories'])) {
                    $data['categories'] = explode(',', $data['categories']);
                }
                if (count($data['categories']) > 0) {
                    $firstCategories = reset($data['categories']);
                    if (!is_array($firstCategories)) {
                        $firstCategories = [$firstCategories];
                    }
                    $data['categories'] = [$primaryRegistry => $firstCategories];
                }
            }
        }
        $this->data = $data;
    
        return $data;
    }
    
    /**
     * @inheritDoc
     */
    public function displayView()
    {
        $objectType = $this->data['objectType'];
        $repository = $this->entityFactory->getRepository($objectType);
    
        // create query
        $orderBy = $this->modelHelper->resolveSortParameter($this->data['objectType'], $this->data['sorting']);
        $qb = $repository->getListQueryBuilder($this->data['filter'], $orderBy);
    
        $this->getData();
        if (in_array($objectType, $this->categorisableObjectTypes)) {
            if ($this->featureActivationHelper->isEnabled(FeatureActivationHelper::CATEGORIES, $objectType)) {
                // apply category filters
                if (is_array($this->data['categories']) && count($this->data['categories']) > 0) {
                    $qb = $this->categoryHelper->buildFilterClauses($qb, $objectType, $this->data['categories']);
                }
            }
        }
    
        // get objects from database
        $currentPage = 1;
        $resultsPerPage = isset($this->data['amount']) ? $this->data['amount'] : 1;
        $query = $repository->getSelectWherePaginatedQuery($qb, $currentPage, $resultsPerPage);
        try {
            list($entities, $objectCount) = $repository->retrieveCollectionResult($query, true);
        } catch (\Exception $exception) {
            $entities = [];
            $objectCount = 0;
        }
    
        if ($this->featureActivationHelper->isEnabled(FeatureActivationHelper::CATEGORIES, $objectType)) {
            $entities = $this->categoryHelper->filterEntitiesByPermission($entities);
        }
    
        $data = $this->data;
        $data['items'] = $entities;
    
        $data = $this->controllerHelper->addTemplateParameters($objectType, $data, 'contentType', []);
        $this->data = $data;
    
        return parent::displayView();
    }
    
    /**
     * @inheritDoc
     */
    public function getViewTemplatePath($suffix = '')
    {
        $templateFile = $this->data['template'];
        if ('custom' == $templateFile && null !== $this->data['customTemplate'] && '' != $this->data['customTemplate']) {
            $templateFile = $this->data['customTemplate'];
        }
    
        $templateForObjectType = str_replace('itemlist_', 'itemlist_' . $this->data['objectType'] . '_', $templateFile);
    
        $templateOptions = [
            'ContentType/' . $templateForObjectType,
            'ContentType/' . $templateFile,
            'ContentType/itemlist_display.html.twig'
        ];
    
        $template = '';
        foreach ($templateOptions as $templatePath) {
            if ($this->twigLoader->exists('@MUImageModule/' . $templatePath)) {
                $template = '@MUImageModule/' . $templatePath;
                break;
            }
        }
    
        return $template;
    }
    
    /**
     * @inheritDoc
     */
    public function getEditFormClass()
    {
        return FormType::class;
    }
    
    /**
     * @inheritDoc
     */
    public function getEditFormOptions($context)
    {
        $options = parent::getEditFormOptions($context);
        $data = $this->getData();
        $options['object_type'] = $data['objectType'];
        $options['is_categorisable'] = in_array($this->data['objectType'], $this->categorisableObjectTypes);
        $options['category_helper'] = $this->categoryHelper;
        $options['feature_activation_helper'] = $this->featureActivationHelper;
    
        return $options;
    }
    
    /**
     * @param ControllerHelper $controllerHelper
     */
    public function setControllerHelper(ControllerHelper $controllerHelper)
    {
        $this->controllerHelper = $controllerHelper;
    }
    
    /**
     * @param ModelHelper $modelHelper
     */
    public function setModelHelper(ModelHelper $modelHelper)
    {
        $this->modelHelper = $modelHelper;
    }
    
    /**
     * @param EntityFactory $entityFactory
     */
    public function setEntityFactory(EntityFactory $entityFactory)
    {
        $this->entityFactory = $entityFactory;
    }
    
    /**
     * @param FeatureActivationHelper $featureActivationHelper
     * @param CategoryHelper $categoryHelper
     */
    public function setCategoryDependencies(
        FeatureActivationHelper $featureActivationHelper,
        CategoryHelper $categoryHelper
    ) {
        $this->featureActivationHelper = $featureActivationHelper;
        $this->categoryHelper = $categoryHelper;
    }
    
    /**
     * @param CategoryHelper $categoryHelper
     */
    public function setCategoryHelper(CategoryHelper $categoryHelper)
    {
        $this->categoryHelper = $categoryHelper;
    }
}
