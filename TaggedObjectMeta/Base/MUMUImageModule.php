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

namespace MU\MUImageModule\TaggedObjectMeta\Base;

use DateUtil;
use ServiceUtil;
use UserUtil;
use Zikula\TagModule\AbstractTaggedObjectMeta;
use Zikula\Core\UrlInterface;

/**
 * This class provides object meta data for the Tag module.
 */
class MUMUImageModule extends AbstractTaggedObjectMeta
{
    /**
     * Constructor.
     *
     * @param integer             $objectId  Identifier of treated object
     * @param integer             $areaId    Name of hook area
     * @param string              $module    Name of the owning module
     * @param string              $urlString **deprecated**
     * @param UrlInterface $urlObject Object carrying url arguments
     */
    function __construct($objectId, $areaId, $module, $urlString = null, UrlInterface $urlObject = null)
    {
        // call base constructor to store arguments in member vars
        parent::__construct($objectId, $areaId, $module, $urlString, $urlObject);
    
        // derive object type from url object
        $urlArgs = $urlObject->getArgs();
        $objectType = isset($urlArgs['ot']) ? $urlArgs['ot'] : 'album';
    
        $serviceManager = ServiceUtil::getManager();
    
        $permissionHelper = $serviceManager->get('zikula_permissions_module.api.permission');
        $component = $module . ':' . ucfirst($objectType) . ':';
        $perm = $permissionHelper->hasPermission($component, $objectId . '::', ACCESS_READ);
        if (!$perm) {
            return;
        }
    
        $repository = $serviceManager->get('mu_muimage_module.' . $objectType . '_factory')->getRepository();
        $useJoins = false;
    
        /** TODO support composite identifiers properly at this point */
        $entity = $repository->selectById($objectId, $useJoins);
        if ($entity === false || (!is_array($entity) && !is_object($entity))) {
            return;
        }
    
        $this->setObjectTitle($entity->getTitleFromDisplayPattern());
    
        $dateFieldName = $repository->getStartDateFieldName();
        if ($dateFieldName != '') {
            $this->setObjectDate($entity[$dateFieldName]);
        } else {
            $this->setObjectDate('');
        }
    
        if (method_exists($entity, 'getCreatedUserId')) {
            $this->setObjectAuthor(UserUtil::getVar('uname', $entity['createdUserId']));
        } else {
            $this->setObjectAuthor('');
        }
    }
    
    /**
     * Sets the object title.
     *
     * @param string $title
     */
    public function setObjectTitle($title)
    {
        $this->title = $title;
    }
    
    /**
     * Sets the object date.
     *
     * @param string $date
     */
    public function setObjectDate($date)
    {
    
        $this->date = DateUtil::formatDatetime($date, 'datetimebrief');
    }
    
    /**
     * Sets the object author.
     *
     * @param string $author
     */
    public function setObjectAuthor($author)
    {
        $this->author = $author;
    }
}
