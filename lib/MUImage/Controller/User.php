<?php
/**
 * MUImage.
 *
 * @copyright Michael Ueberschaer
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @package MUImage
 * @author Michael Ueberschaer <kontakt@webdesign-in-bremen.com>.
 * @link http://www.webdesign-in-bremen.com
 * @link http://zikula.org
 * @version Generated by ModuleStudio 0.5.4 (http://modulestudio.de) at Sun Feb 19 15:20:07 CET 2012.
 */

/**
 * This is the User controller class providing navigation and interaction functionality.
 */
class MUImage_Controller_User extends MUImage_Controller_Base_User
{
    /**
     * Post initialise.
     *
     * Run after construction.
     *
     * @return void
     */
    protected function postInitialize()
    {
        // Set caching to true by default.
        $this->view->setCaching(Zikula_View::CACHE_DISABLED);
    }
    
	/**
	 * This method provides a generic item detail view.
	 *
	 * @param string  $id           Check for entity
	 * @return parent function
	 */
	public function display()
	{
		$id = $this->request->getGet()->filter('id', 0 , FILTER_SANITIZE_STRING);
		$ot = $this->request->getget()->filter('ot','album' , FILTER_SANITIZE_STRING);
		
		// DEBUG: permission check aspect starts
		$this->throwForbiddenUnless(SecurityUtil::checkPermission('MUImage:Album:', $id.'::', ACCESS_READ));
		// DEBUG: permission check aspect ends
			
		if ($id != 0) {

			$count = MUImage_Util_View::countPictures();
			$count2 = MUImage_Util_View::countAlbums();

			$this->view->assign('numpictures', $count);
			$this->view->assign('numalbums', $count2);

		}
		// we get the picture object
		$picturerepository = MUImage_Util_Model::getPictureRepository();
		$picture = $picturerepository->selectById($id);
		// if object is a picture, we want to count views, the picture id is not the actual userid
		// or the user is not loggedin we add 1 to view
		if ($ot == 'picture' && ModUtil::getVar('MUImage', 'countImageView') == true && ($picture->getCreatedUserId() != UserUtil::getVar('uid') || UserUtil::isLoggedIn() == false)) {
			$picture->setImageView($picture->getImageView() + 1);

			$serviceManager = $this->getServiceManager();
			$entityManager = $serviceManager->getService('doctrine.entitymanager');

			$entityManager->flush();
		}
		if ($ot == 'picture') {
			$showTitle = ModUtil::getVar($this->name, 'showTitle');
			$showDescription = ModUtil::getVar($this->name, 'showDescription');
			$this->view->assign('showTitle', $showTitle);
			$this->view->assign('showDescription', $showDescription);
		}
		
		// we look if a template is set in session var
		$template = SessionUtil::getVar('template');
		if ($template == 0) {
		   SessionUtil::setVar('template', 1);
		   $template = SessionUtil::getVar('template');
		}
		
		// we get all modvars and assign them to the template
		$modvars = ModUtil::getVar($this->name);
		$this->view->assign('modulevars', $modvars);
		
		$this->view->assign('template', $template);
		
		$otherPictures = MUImage_Util_View::otherUserPictures();
		$this->view->assign('otherPictures', $otherPictures);
			
		return parent::display($args);
			
	}
	
	/**
	 * This method provides a item list overview.
	 *
	 * @param string  $ot           Treated object type.
	 * @param string  $sort         Sorting field.
	 * @param string  $sortdir      Sorting direction.
	 * @param int     $pos          Current pager position.
	 * @param int     $num          Amount of entries to display.
	 * @param string  $tpl          Name of alternative template (to be used instead of the default template).
	 * @param boolean $raw          Optional way to display a template instead of fetching it (required for standalone output).
	 *
	 * @return mixed Output.
	 */
	public function view()
	{
	    $controllerHelper = new MUImage_Util_Controller($this->serviceManager);
	
	    // parameter specifying which type of objects we are treating
	    $objectType = $this->request->query->filter('ot', 'album', FILTER_SANITIZE_STRING);
	    $utilArgs = array('controller' => 'user', 'action' => 'view');
	    if (!in_array($objectType, $controllerHelper->getObjectTypes('controllerAction', $utilArgs))) {
	        $objectType = $controllerHelper->getDefaultObjectType('controllerAction', $utilArgs);
	    }
	    $permLevel = ACCESS_READ;
	    $this->throwForbiddenUnless(SecurityUtil::checkPermission($this->name . ':' . ucfirst($objectType) . ':', '::', $permLevel), LogUtil::getErrorMsgPermission());

	    /*******OWN CODE ************/
	    
	    if ($objectType == 'album') {
	        // DEBUG: permission check aspect starts
	        $this->throwForbiddenUnless(SecurityUtil::checkPermission('MUImage:Album:', '::', ACCESS_READ));
	        // DEBUG: permission check aspect ends
	         
	        $count = MUImage_Util_View::countPictures();
	        $count2 = MUImage_Util_View::countAlbums();
	    
	        $this->view->assign('numpictures', $count);
	        $this->view->assign('numalbums', $count2);
	    
	    }
	    if ($objectType == 'picture') {
	        // DEBUG: permission check aspect starts
	        $this->throwForbiddenUnless(SecurityUtil::checkPermission('MUImage:Picture:', '::', ACCESS_READ));
	        // DEBUG: permission check aspect ends
	    }
	     
	    // no view for pictures in the user area
	    if ($objectType == 'picture') {
	        $url = ModUtil::url($this->name, 'user', 'view', array('ot' => 'album'));
	        return System::redirect($url);
	    }	    
	    
	    /*******OWN CODE ************/
	    
	    // redirect to entity controller
	
	    System::queryStringSetVar('lct', 'user');
	    $this->request->query->set('lct', 'user');
	
	    return ModUtil::func($this->name, $objectType, 'view', array('lct' => 'user'));
	}

	/**
	 * This method provides a generic item list overview.
	 *
	 * @param string  $objectType   Treated object type.
	 * @return parent function.
	 */
	/*public function view($args)
	{
	    $objectType = (isset($args['ot']) && !empty($args['ot'])) ? $args['ot'] : $this->request->getGet()->filter('ot', 'album', FILTER_SANITIZE_STRING);
	    	
	    if ($objectType == 'album') {
	        // DEBUG: permission check aspect starts
	        $this->throwForbiddenUnless(SecurityUtil::checkPermission('MUImage:Album:', '::', ACCESS_READ));
	        // DEBUG: permission check aspect ends
	    
	        $count = MUImage_Util_View::countPictures();
	        $count2 = MUImage_Util_View::countAlbums();
	        	
	        $this->view->assign('numpictures', $count);
	        $this->view->assign('numalbums', $count2);
	        	
	    }
	    if ($objectType == 'picture') {
	        // DEBUG: permission check aspect starts
	        $this->throwForbiddenUnless(SecurityUtil::checkPermission('MUImage:Picture:', '::', ACCESS_READ));
	        // DEBUG: permission check aspect ends
	    }
	    
	    // no view for pictures in the user area
	    if ($objectType == 'picture') {
	        $url = ModUtil::url($this->name, 'user', 'view', array('ot' => 'album'));
	        return System::redirect($url);
	    }
	    
	    $controllerHelper = new MUImage_Util_Controller($this->serviceManager);
	    
	    // parameter specifying which type of objects we are treating
	    $objectType = $this->request->query->filter('ot', 'album', FILTER_SANITIZE_STRING);
	    $utilArgs = array('controller' => 'user', 'action' => 'view');
	    if (!in_array($objectType, $controllerHelper->getObjectTypes('controllerAction', $utilArgs))) {
	        $objectType = $controllerHelper->getDefaultObjectType('controllerAction', $utilArgs);
	    }
	    $this->throwForbiddenUnless(SecurityUtil::checkPermission($this->name . ':' . ucwords($objectType) . ':', '::', ACCESS_READ), LogUtil::getErrorMsgPermission());
	    $entityClass = $this->name . '_Entity_' . ucwords($objectType);
	    $repository = $this->entityManager->getRepository($entityClass);
	    $repository->setControllerArguments(array());
	    $viewHelper = new MUImage_Util_View($this->serviceManager);
	    
	    // parameter for used sorting field
	    $sort = $this->request->query->filter('sort', '', FILTER_SANITIZE_STRING);
	    if (empty($sort) || !in_array($sort, $repository->getAllowedSortingFields())) {
	        $sort = $repository->getDefaultSortingField();
	    }
	    
	    // parameter for used sort order
	    $sdir = $this->request->query->filter('sortdir', '', FILTER_SANITIZE_STRING);
	    $sdir = strtolower($sdir);
	    if ($sdir != 'asc' && $sdir != 'desc') {
	        $sdir = 'asc';
	    }
	    
	    // convenience vars to make code clearer
	    $currentUrlArgs = array('ot' => $objectType);
	    
	    $where = '';
	    if ($objectType == 'album') {
	        $where = 'tbl.notInFrontend = 0';
	    }
	    
	    $selectionArgs = array(
	            'ot' => $objectType,
	            'where' => $where,
	            'orderBy' => $sort . ' ' . $sdir
	    );
	    
	    $showOwnEntries = (int) $this->request->query->filter('own', $this->getVar('showOnlyOwnEntries', 0), FILTER_VALIDATE_INT);
	    $showAllEntries = (int) $this->request->query->filter('all', 0, FILTER_VALIDATE_INT);
	    
	    if (!$showAllEntries) {
	        $csv = (int) $this->request->query->filter('usecsvext', 0, FILTER_VALIDATE_INT);
	        if ($csv == 1) {
	            $showAllEntries = 1;
	        }
	    }
	    
	    $this->view->assign('showOwnEntries', $showOwnEntries)
	    ->assign('showAllEntries', $showAllEntries);
	    if ($showOwnEntries == 1) {
	        $currentUrlArgs['own'] = 1;
	    }
	    if ($showAllEntries == 1) {
	        $currentUrlArgs['all'] = 1;
	    }
	    
	    // prepare access level for cache id
	    $accessLevel = ACCESS_READ;
	    $component = 'MUImage:' . ucwords($objectType) . ':';
	    $instance = '::';
	    if (SecurityUtil::checkPermission($component, $instance, ACCESS_COMMENT)) {
	        $accessLevel = ACCESS_COMMENT;
	    }
	    if (SecurityUtil::checkPermission($component, $instance, ACCESS_EDIT)) {
	        $accessLevel = ACCESS_EDIT;
	    }
	    
	    $templateFile = $viewHelper->getViewTemplate($this->view, 'user', $objectType, 'view', array());
	    $cacheId = 'view|ot_' . $objectType . '_sort_' . $sort . '_' . $sdir;
	    $resultsPerPage = 0;
	    if ($showAllEntries == 1) {
	        // set cache id
	        $this->view->setCacheId($cacheId . '_all_1_own_' . $showOwnEntries . '_' . $accessLevel);
	    
	        // if page is cached return cached content
	        if ($this->view->is_cached($templateFile)) {
	            return $viewHelper->processTemplate($this->view, 'user', $objectType, 'view', array(), $templateFile);
	        }
	    
	        // retrieve item list without pagination
	        $entities = ModUtil::apiFunc($this->name, 'selection', 'getEntities', $selectionArgs);
	    } else {
	        // the current offset which is used to calculate the pagination
	        $currentPage = (int) $this->request->query->filter('pos', 1, FILTER_VALIDATE_INT);
	    
	        // the number of items displayed on a page for pagination
	        $resultsPerPage = (int) $this->request->query->filter('num', 0, FILTER_VALIDATE_INT);
	        if ($resultsPerPage == 0) {
	            $resultsPerPage = $this->getVar('pageSize', 10);
	        }
	    
	        // set cache id
	        $this->view->setCacheId($cacheId . '_amount_' . $resultsPerPage . '_page_' . $currentPage . '_own_' . $showOwnEntries . '_' . $accessLevel);
	    
	        // if page is cached return cached content
	        if ($this->view->is_cached($templateFile)) {
	            return $viewHelper->processTemplate($this->view, 'user', $objectType, 'view', array(), $templateFile);
	        }
	    
	        // retrieve item list with pagination
	        $selectionArgs['currentPage'] = $currentPage;
	        $selectionArgs['resultsPerPage'] = $resultsPerPage;
	        list($entities, $objectCount) = ModUtil::apiFunc($this->name, 'selection', 'getEntitiesPaginated', $selectionArgs);
	    
	        $this->view->assign('currentPage', $currentPage)
	        ->assign('pager', array('numitems'     => $objectCount,
	                'itemsperpage' => $resultsPerPage));
	    }
	    
	    foreach ($entities as $k => $entity) {
	        $entity->initWorkflow();
	    }
	    
	    // build ModUrl instance for display hooks
	    $currentUrlObject = new Zikula_ModUrl($this->name, 'user', 'view', ZLanguage::getLanguageCode(), $currentUrlArgs);
	    
	    if ($objectType == 'album') {
	        $albumcount = count($entities);
	        $this->view->assign('albumcount', $albumcount);
	    }
	    
	    // assign the object data, sorting information and details for creating the pager
	    $this->view->assign('items', $entities)
	    ->assign('sort', $sort)
	    ->assign('sdir', $sdir)
	    ->assign('pageSize', $resultsPerPage)
	    ->assign('currentUrlObject', $currentUrlObject)
	    ->assign($repository->getAdditionalTemplateParameters('controllerAction', $utilArgs));
	    
	    $modelHelper = new MUImage_Util_Model($this->serviceManager);
	    $this->view->assign('canBeCreated', $modelHelper->canBeCreated($objectType));
	    
	    // fetch and return the appropriate template
	    return $viewHelper->processTemplate($this->view, 'user', $objectType, 'view', array(), $templateFile);
	     
	    //////////////////////////////////////////////////////
	   /*$objectType = (isset($args['ot']) && !empty($args['ot'])) ? $args['ot'] : $this->request->getGet()->filter('ot', 'album', FILTER_SANITIZE_STRING);
			
		if ($objectType == 'album') {
			// DEBUG: permission check aspect starts
			$this->throwForbiddenUnless(SecurityUtil::checkPermission('MUImage:Album:', '::', ACCESS_READ));
			// DEBUG: permission check aspect ends

			$count = MUImage_Util_View::countPictures();
			$count2 = MUImage_Util_View::countAlbums();
			
			$this->view->assign('numpictures', $count);
			$this->view->assign('numalbums', $count2);
			
		}
		if ($objectType == 'picture') {
			// DEBUG: permission check aspect starts
			$this->throwForbiddenUnless(SecurityUtil::checkPermission('MUImage:Picture:', '::', ACCESS_READ));
			// DEBUG: permission check aspect ends
		}

		// no view for pictures in the user area
		if ($objectType == 'picture') {
			$url = ModUtil::url($this->name, 'user', 'view', array('ot' => 'album'));
			return System::redirect($url);
		}

        // DEBUG: permission check aspect starts
        $this->throwForbiddenUnless(SecurityUtil::checkPermission('MUImage::', '::', ACCESS_READ));
        // DEBUG: permission check aspect ends

        // parameter specifying which type of objects we are treating
        $objectType = (isset($args['ot']) && !empty($args['ot'])) ? $args['ot'] : $this->request->getGet()->filter('ot', 'album', FILTER_SANITIZE_STRING);
        $utilArgs = array('controller' => 'user', 'action' => 'view');
        if (!in_array($objectType, MUImage_Util_Controller::getObjectTypes('controllerAction', $utilArgs))) {
            $objectType = MUImage_Util_Controller::getDefaultObjectType('controllerAction', $utilArgs);
        }
        $repository = $this->entityManager->getRepository('MUImage_Entity_' . ucfirst($objectType));

        $tpl = (isset($args['tpl']) && !empty($args['tpl'])) ? $args['tpl'] : $this->request->getGet()->filter('tpl', '', FILTER_SANITIZE_STRING);
        if ($tpl == 'tree') {
            $trees = ModUtil::apiFunc($this->name, 'selection', 'getAllTrees', array('ot' => $objectType));
            $this->view->assign('trees', $trees)
                ->assign($repository->getAdditionalTemplateParameters('controllerAction', $utilArgs));
            // fetch and return the appropriate template
            return MUImage_Util_View::processTemplate($this->view, 'user', $objectType, 'view', $args);
        }

        // parameter for used sorting field
        $sort = (isset($args['sort']) && !empty($args['sort'])) ? $args['sort'] : $this->request->getGet()->filter('sort', '', FILTER_SANITIZE_STRING);
        if (empty($sort) || !in_array($sort, $repository->getAllowedSortingFields())) {
            $sort = $repository->getDefaultSortingField();
        }

        // parameter for used sort order
        $sdir = (isset($args['sortdir']) && !empty($args['sortdir'])) ? $args['sortdir'] : $this->request->getGet()->filter('sortdir', '', FILTER_SANITIZE_STRING);
        $sdir = strtolower($sdir);
        if ($sdir != 'asc' && $sdir != 'desc') {
            $sdir = 'asc';
        }

        // convenience vars to make code clearer
        $currentUrlArgs = array('ot' => $objectType);

        $selectionArgs = array(
            'ot'      => $objectType,
            'where'   => '',
            'orderBy' => $sort . ' ' . $sdir
        );

        $showAllEntries = (int)(isset($args['all']) && !empty($args['all'])) ? $args['all'] : $this->request->getGet()->filter('all', 0, FILTER_VALIDATE_INT);
        $this->view->assign('showAllEntries', $showAllEntries);
        if ($showAllEntries == 1) {
            // item list without pagination
            $entities = ModUtil::apiFunc($this->name, 'selection', 'getEntities', $selectionArgs);
            $objectCount = count($entities);
            $currentUrlArgs['all'] = 1;
        } else {
            // item list with pagination

            // the current offset which is used to calculate the pagination
            $currentPage = (int)(isset($args['pos']) && !empty($args['pos'])) ? $args['pos'] : $this->request->getGet()->filter('pos', 1, FILTER_VALIDATE_INT);

            // the number of items displayed on a page for pagination
            $resultsPerPage = (int)(isset($args['num']) && !empty($args['num'])) ? $args['num'] : $this->request->getGet()->filter('num', 0, FILTER_VALIDATE_INT);
            if ($resultsPerPage == 0) {
                $csv = (int)(isset($args['usecsv']) && !empty($args['usecsv'])) ? $args['usecsv'] : $this->request->getGet()->filter('usecsvext', 0, FILTER_VALIDATE_INT);
                $resultsPerPage = ($csv == 1) ? 999999 : $this->getVar('pagesize', 10);
            }
            
            // we get the pagesize for albums
            if ($objectType == 'album') {
            	$resultsPerPage = ModUtil::getVar($this->name, 'pagesize');
            }

            $selectionArgs['currentPage'] = $currentPage;
            $selectionArgs['resultsPerPage'] = $resultsPerPage;
            // get a ist of parent entities
            list($entities, $objectCount) = ModUtil::apiFunc($this->name, 'selection', 'getParentEntitiesPaginated', $selectionArgs);

            $this->view->assign('currentPage', $currentPage)
                ->assign('pager', array('numitems'     => $objectCount,
                    'itemsperpage' => $resultsPerPage));
        }

        // build ModUrl instance for display hooks
        $currentUrlObject = new Zikula_ModUrl($this->name, 'user', 'view', ZLanguage::getLanguageCode(), $currentUrlArgs);

        if ($objectType == 'album') {
        	$albumcount = count($entities);
        	$this->view->assign('albumcount', $albumcount);
        }
        
        // assign the object data, sorting information and details for creating the pager
        $this->view->assign('items', $entities)
            ->assign('sort', $sort)
            ->assign('sdir', $sdir)
            ->assign('currentUrlObject', $currentUrlObject)
            ->assign($repository->getAdditionalTemplateParameters('controllerAction', $utilArgs));

        // fetch and return the appropriate template
        return MUImage_Util_View::processTemplate($this->view, 'user', $objectType, 'view', $args);
		*/
	//}

	/**
	 * This is a custom method. Documentation for this will be improved in later versions.
	 *
	 * @return mixed Output.
	 */
	public function zipUpload()
	{
		// DEBUG: permission check aspect starts
		$this->throwForbiddenUnless(SecurityUtil::checkPermission('MUImage::', '::', ACCESS_ADD));
		// DEBUG: permission check aspect ends

		// parameter specifying which type of objects we are treating
		$objectType = (isset($args['ot']) && !empty($args['ot'])) ? $args['ot'] : $this->request->getGet()->filter('ot', 'picture', FILTER_SANITIZE_STRING);
		$utilArgs = array('controller' => 'user', 'action' => 'multiUpload');
		if (!in_array($objectType, MUImage_Util_Controller::getObjectTypes('controllerAction', $utilArgs))) {
			$objectType = MUImage_Util_Controller::getDefaultObjectType('controllerAction', $utilArgs);
		}
		// create new Form reference
		$view = FormUtil::newForm($this->name, $this);

		// build form handler class name
		$handlerClass = 'MUImage_Form_Handler_User_' . ucfirst($objectType) . '_ZipUpload';

		// execute form using supplied template and page event handler
		return $view->execute('user/' . $objectType . '/zipUpload.tpl', new $handlerClass());
	}

	/**
	 * This is a custom method. Documentation for this will be improved in later versions.
	 *
	 * @return mixed Output.
	 */
	public function multiUpload()
	{
		// DEBUG: permission check aspect starts
		$this->throwForbiddenUnless(SecurityUtil::checkPermission('MUImage::', '::', ACCESS_EDIT));
		// DEBUG: permission check aspect ends
		// parameter specifying which type of objects we are treating
		$objectType = (isset($args['ot']) && !empty($args['ot'])) ? $args['ot'] : $this->request->getGet()->filter('ot', 'picture', FILTER_SANITIZE_STRING);
		$utilArgs = array('controller' => 'user', 'action' => 'multiUpload');
		if (!in_array($objectType, MUImage_Util_Controller::getObjectTypes('controllerAction', $utilArgs))) {
			$objectType = MUImage_Util_Controller::getDefaultObjectType('controllerAction', $utilArgs);
		}
		// create new Form reference
		$view = FormUtil::newForm($this->name, $this);

		// build form handler class name
		$handlerClass = 'MUImage_Form_Handler_' . ucfirst($objectType) . '_MultiUpload';

		// execute form using supplied template and page event handler
		return $view->execute($objectType . '/multiUpload.tpl', new $handlerClass());
	}

	/**
	 * This is a custom method. Documentation for this will be improved in later versions.
	 *
	 * @return mixed Output.
	 */
	public function editMulti()
	{
		// DEBUG: permission check aspect starts
		$this->throwForbiddenUnless(SecurityUtil::checkPermission('MUImage::', '::', ACCESS_EDIT));
		// DEBUG: permission check aspect ends
		// parameter specifying which type of objects we are treating
		$objectType = (isset($args['ot']) && !empty($args['ot'])) ? $args['ot'] : $this->request->getGet()->filter('ot', 'picture', FILTER_SANITIZE_STRING);
		$utilArgs = array('controller' => 'user', 'action' => 'editMulti');
		if (!in_array($objectType, MUImage_Util_Controller::getObjectTypes('controllerAction', $utilArgs))) {
			$objectType = MUImage_Util_Controller::getDefaultObjectType('controllerAction', $utilArgs);
		}
		// create new Form reference
		$view = FormUtil::newForm($this->name, $this);

		// build form handler class name
		$handlerClass = 'MUImage_Form_Handler_' . ucfirst($objectType) . '_EditMulti';

		// execute form using supplied template and page event handler
		return $view->execute($objectType . '/editMulti.tpl', new $handlerClass());

	}

	/**
	 * This method provides a generic handling of simple delete requests.
	 *
	 * @param string  $ot           Treated object type.
	 * @param int     $id           Identifier of entity to be deleted.
	 * @param boolean $confirmation Confirm the deletion, else a confirmation page is displayed.
	 * @param string  $tpl          Name of alternative template (for alternative display options, feeds and xml output)
	 * @param boolean $raw          Optional way to display a template instead of fetching it (needed for standalone output)
	 * @return mixed Output.
	 */
	public function delete()
	{
		$id = $this->request->getGet()->filter('id' , 0, FILTER_SANITIZE_NUMBER_INT);
		$ot = $this->request->getGet()->filter('ot' , 'album', FILTER_SANITIZE_STRING);

		// we get the usergroups for the calling user
		$usergroups = (UserUtil::getGroupsForUser(UserUtil::getVar('uid')));

		if ($id > 0) {
			if ($ot == 'album') {
				$albumrepository = MUImage_Util_Model::getAlbumRepository();
				$album = $albumrepository->selectById($id);
				if ($album->getCreatedUserId() == UserUtil::getVar('uid')) {
					// nothing to do
				}
				else {
					// if user is no admin
					if (!in_array(2, $usergroups)) {
						$url = ModUtil::url($this->name, 'user' , 'display', array('ot' => 'album', 'id' => $id));
						LogUtil::registerError($this->__('You have no permissions to delete this album!'));
						return System::redirect($url);
					}
				}
			}

			if ($ot == 'picture') {
				$picturerepository = MUImage_Util_Model::getPictureRepository();
				$picture = $picturerepository->selectById($id);
				if ($picture->getCreatedUserId() == UserUtil::getVar('uid')) {
					$album = $picture->getAlbum();
					$albumid = $album->getId();
					$this->view->assign('albumid', $albumid);
				}
				else {
					// if user is no admin
					if (!in_array(2, $usergroups) && ModUtil::getVar($this->name, 'userDeletePictures') == 0) {
						$url = ModUtil::url($this->name, 'user' , 'display', array('ot' => 'piture', 'id' => $id));
						LogUtil::registerError($this->__('You have no permissions to delete this picture!'));
						return System::redirect($url);
					}
				}
			}
		}

		// TODO in next version MUImage_Util_View::checkForBlocksAndContent($id);

		return parent::delete($args);
	}
	
	/**
	 * This method provides to change the template in the user view of an album
	 * return to album
	 */
	public function template()
	{
	    // we get the id of the album
	    $id = $this->request->query->filter('id', 0, FILTER_SANITIZE_NUMBER_INT);
	    $template = $this->request->getPost()->filter('template', 1, FILTER_SANITIZE_NUMBER_INT);
	    ModUtil::apiFunc($this->name, 'user', 'template', array('template' => $template)); 
	    $url = ModUtil::url($this->name, 'user', 'display', array('ot' => 'album', 'id' => $id));
	    return System::redirect($url);  
	}

}
