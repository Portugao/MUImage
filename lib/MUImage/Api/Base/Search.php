<?php
/**
 * MUImage.
 *
 * @copyright Michael Ueberschaer (MU)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @package MUImage
 * @author Michael Ueberschaer <kontakt@webdesign-in-bremen.com>.
 * @link http://www.webdesign-in-bremen.com
 * @link http://zikula.org
 * @version Generated by ModuleStudio 0.6.2 (http://modulestudio.de).
 */

/**
 * Search api base class.
 */
class MUImage_Api_Base_Search extends Zikula_AbstractApi
{
    /**
     * Get search plugin information.
     *
     * @return array The search plugin information
     */
    public function info()
    {
        return array('title'     => $this->name,
                     'functions' => array($this->name => 'search'));
    }
    
    /**
     * Display the search form.
     *
     * @param array $args List of arguments.
     *
     * @return string Template output
     */
    public function options(array $args = array())
    {
        if (!SecurityUtil::checkPermission($this->name . '::', '::', ACCESS_READ)) {
            return '';
        }
    
        $view = Zikula_View::getInstance($this->name);
    
        $view->assign('active_album', (!isset($args['active_album']) || isset($args['active']['active_album'])));
        $view->assign('active_picture', (!isset($args['active_picture']) || isset($args['active']['active_picture'])));
    
        return $view->fetch('search/options.tpl');
    }
    
    /**
     * Executes the actual search process.
     *
     * @param array $args List of arguments.
     *
     * @return boolean
     *
     * @throws RuntimeException Thrown if search results can not be saved
     */
    public function search(array $args = array())
    {
        if (!SecurityUtil::checkPermission($this->name . '::', '::', ACCESS_READ)) {
            return '';
        }
    
        // ensure that database information of Search module is loaded
        ModUtil::dbInfoLoad('Search');
    
        // save session id as it is used when inserting search results below
        $sessionId  = session_id();
    
        // retrieve list of activated object types
        $searchTypes = isset($args['objectTypes']) ? (array)$args['objectTypes'] : (array) FormUtil::getPassedValue('mUImageSearchTypes', array(), 'GETPOST');
    
        $controllerHelper = new MUImage_Util_Controller($this->serviceManager);
        $utilArgs = array('api' => 'search', 'action' => 'search');
        $allowedTypes = $controllerHelper->getObjectTypes('api', $utilArgs);
        $entityManager = ServiceUtil::getService('doctrine.entitymanager');
        $currentPage = 1;
        $resultsPerPage = 50;
    
        foreach ($searchTypes as $objectType) {
            if (!in_array($objectType, $allowedTypes)) {
                continue;
            }
    
            $whereArray = array();
            $languageField = null;
            switch ($objectType) {
                case 'album':
                    $whereArray[] = 'tbl.workflowState';
                    $whereArray[] = 'tbl.title';
                    $whereArray[] = 'tbl.description';
                    $whereArray[] = 'tbl.albumAccess';
                    $whereArray[] = 'tbl.passwordAccess';
                    $whereArray[] = 'tbl.myFriends';
                    break;
                case 'picture':
                    $whereArray[] = 'tbl.workflowState';
                    $whereArray[] = 'tbl.title';
                    $whereArray[] = 'tbl.description';
                    $whereArray[] = 'tbl.imageUpload';
                    break;
            }
            $where = Search_Api_User::construct_where($args, $whereArray, $languageField);
    
            $entityClass = $this->name . '_Entity_' . ucfirst($objectType);
            $repository = $entityManager->getRepository($entityClass);
    
            // get objects from database
            list($entities, $objectCount) = $repository->selectWherePaginated($where, '', $currentPage, $resultsPerPage, false);
    
            if ($objectCount == 0) {
                continue;
            }
    
            $idFields = ModUtil::apiFunc($this->name, 'selection', 'getIdFields', array('ot' => $objectType));
            $descriptionField = $repository->getDescriptionFieldName();
            foreach ($entities as $entity) {
                $urlArgs = $entity->createUrlArgs();
                $instanceId = $entity->createCompositeIdentifier();
                // could exceed the maximum length of the 'extra' field, improved in 1.4.0
                if (isset($urlArgs['slug'])) {
                    unset($urlArgs['slug']);
                }
    
                // perform permission check
                if (!SecurityUtil::checkPermission($this->name . ':' . ucfirst($objectType) . ':', $instanceId . '::', ACCESS_OVERVIEW)) {
                    continue;
                }
    
                $title = $entity->getTitleFromDisplayPattern();
                $description = !empty($descriptionField) ? $entity[$descriptionField] : '';
                $created = isset($entity['createdDate']) ? $entity['createdDate']->format('Y-m-d H:i:s') : '';
    
                $searchItemData = array(
                    'title'   => $title,
                    'text'    => $description,
                    'extra'   => serialize($urlArgs),
                    'created' => $created,
                    'module'  => $this->name,
                    'session' => $sessionId
                );
    
                if (!DBUtil::insertObject($searchItemData, 'search_result')) {
                    return LogUtil::registerError($this->__('Error! Could not save the search results.'));
                }
            }
        }
    
        return true;
    }
    
    /**
     * Assign URL to items.
     *
     * @param array $args List of arguments.
     *
     * @return boolean
     */
    public function search_check(array $args = array())
    {
        $datarow = &$args['datarow'];
        $urlArgs = unserialize($datarow['extra']);
        $datarow['url'] = ModUtil::url($this->name, 'user', 'display', $urlArgs);
    
        return true;
    }
}
