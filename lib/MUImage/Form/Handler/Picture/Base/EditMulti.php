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
 * @version Generated by ModuleStudio 0.5.4 (http://modulestudio.de) at Thu Feb 23 22:43:24 CET 2012.
 */

/**
 * This handler class handles the page events of the Form called by the MUImage_user_edit() function.
 * It aims on the picture object type.
 */
class MUImage_Form_Handler_Picture_Base_EditMulti extends MUImage_Form_Handler_Common_Edit
{
	
   /**
     * Pre-initialise hook.
     *
     * @return void
     */
    public function preInitialize()
    {
        parent::preInitialize();
        
        $this->objectType = 'picture';
        $this->objectTypeCapital = 'Picture';
        $this->objectTypeLower = 'picture';
        $this->objectTypeLowerMultiple = 'pictures';

        $this->hasPageLockSupport = true;
        $this->hasCategories = false;
        // array with upload fields and mandatory flags
        $this->uploadFields = array('imageUpload' => false);        

    }	
	
    /**
     * Initialize form handler.
     *
     * This method takes care of all necessary initialisation of our data and form states.
     *
     * @return boolean False in case of initialization errors, otherwise true.
     */
    public function initialize(Zikula_Form_View $view)
    {  	
    	// array of just created picture ids by session
    	$pictureids = SessionUtil::getVar('muimagepictureids', 'false');
    	if ($pictureids == 'false') {
    		$url = ModUtil::url('MUImage', 'user', 'view', array('ot' => 'album'));
    		LogUtil::registerPermissionError($url);    		
    	}
    	$pictures = unserialize($pictureids);
    	
    	$pictureid = $this->request->query->filter('id', 0);
    	
      /*  if ($pictureid == 0) {
    		$url = ModUtil::url($this->name, 'user', 'display', array('ot' => 'album', 'id' => 30));
    		return LogUtil::registerError(__('Sorry. There is no picture id. Please edit your pictures by visiting the relevant album!'), null, $url);    		
    	}
    	
    	if (!in_array($pictureid, $pictures)) {

    		$url = ModUtil::url($this->name, 'user', 'display', array('ot' => 'album', 'id' => 30));
    		return LogUtil::registerError(__('Sorry. The picture id returns an error. Please edit your pictures by visiting the relevant album!'), null, $url);
    	}*/
    	
    	// count items
    	$number = count($pictures);
    	// if more than 0 item lets start to edit the first picture
    	if ($number > 0) {
    		
    	   	if ($pictureid < $pictures[$number - 1]) {
    			$this->view->assign('nextpicture', 1);
    		}
    		else {
    			$this->view->assign('nextpicture', 0);
    		} 

    	    if ($pictureid > $pictures[0]) {
    			$this->view->assign('previouspicture', 1);
    		}
    		else {
    			$this->view->assign('previouspicture', 0);
    		}    			
    	}
    	// there must be a fault with the session; editing picture by default function
    	else {
    		$url = ModUtil::url($this->name, 'user', 'view', array('ot' => 'picture'));
    		LogUtil::registerArgsError(__('Sorry. There is an error with session var.', $url));
    	}
    	
        $this->inlineUsage = ((UserUtil::getTheme() == 'Printer') ? true : false);
        $this->idPrefix = $this->request->query->filter('idp', '', FILTER_SANITIZE_STRING);

        // initialise redirect goal
        $this->returnTo = $this->request->query->filter('returnTo', null, FILTER_SANITIZE_STRING);
        // store current uri for repeated creations
        $this->repeatReturnUrl = System::getCurrentURI();

        $this->permissionComponent = $this->name . ':' . $this->objectTypeCapital . ':';

        $entityClass = $this->name . '_Entity_' . ucfirst($this->objectType);
        $objectTemp = new $entityClass();
        $this->idFields = ModUtil::apiFunc($this->name, 'selection', 'getIdFields', array('ot' => $this->objectType));

        // retrieve identifier of the object we wish to view
        $controllerHelper = new MUImage_Util_Controller($this->view->getServiceManager());
    
        $this->idValues = $controllerHelper->retrieveIdentifier($this->request, array(), $this->objectType, $this->idFields);
        $hasIdentifier = $controllerHelper->isValidIdentifier($this->idValues);

        $entity = null;
        $this->mode = ($hasIdentifier) ? 'edit' : 'create';

        if ($this->mode == 'edit') {
            if (!SecurityUtil::checkPermission($this->permissionComponent, '::', ACCESS_EDIT)) {
                // set an error message and return false
                return LogUtil::registerPermissionError();
            }

            $entity = $this->initEntityForEdit();

            if ($this->hasPageLockSupport === true && ModUtil::available('PageLock')) {
                // try to guarantee that only one person at a time can be editing this entity
                ModUtil::apiFunc('PageLock', 'user', 'pageLock',
                    array('lockName'  => $this->name . $this->objectTypeCapital . $this->createCompositeIdentifier(),
                    'returnUrl' => $this->getRedirectUrl(null, $entity)));
            }
        } else {
            if (!SecurityUtil::checkPermission($this->permissionComponent, '::', ACCESS_ADD)) {
                return LogUtil::registerPermissionError();
            }

            $entity = $this->initEntityForCreation($entityClass);
        }

        $this->view->assign('mode', $this->mode)
            ->assign('inlineUsage', $this->inlineUsage);

        if ($this->hasCategories === true) {
            $this->initCategoriesForEdit($entity);
        }

        $entityData = $entity->toArray();

        // assign data to template as array (makes translatable support easier)
        $this->view->assign($this->objectTypeLower, $entityData);

        // save entity reference for later reuse
        $this->entityRef = $entity;

        // everything okay, no initialization errors occured
        return true;
    }
    
    /**
     * Post-initialise hook.
     *
     * @return void
     */
    public function postInitialize()
    {
        parent::postInitialize();
    }
    
    /**
     * Get the default redirect url. Required if no returnTo parameter has been supplied.
     * This method is called in handleCommand so we know which command has been performed.
     */
    protected function getDefaultReturnUrl($args, $obj)
    {
        // redirect to the list of pictures
        $viewArgs = array('ot' => $this->objectType);
        $url = ModUtil::url($this->name, 'user', 'view', $viewArgs);

        if ($args['commandName'] != 'delete' && !($this->mode == 'create' && $args['commandName'] == 'cancel')) {

            if ($args['status'] == 'next') {
            	$url = ModUtil::url($this->name, 'user', 'editMulti', array('ot' => 'picture', 'id' => $this->idValues['id']));
            }
            if ($args['status'] == 'previous') {
            	$url = ModUtil::url($this->name, 'user', 'edit', array('ot' => 'picture', 'id' => $this->idValues['id'] - 1));
            }
        }

        return $url;
    }

    /**
     * Get list of allowed redirect codes.
     */
    protected function getRedirectCodes()
    {
        $codes = array();
        // main page of admin area
        $codes[] = 'admin';
        // admin list of entities
        $codes[] = 'adminView';
        // admin display page of treated entity
        $codes[] = 'adminDisplay';
        // main page of user area
        $codes[] = 'user';
        // user list of entities
        $codes[] = 'userView';
        // user display page of treated entity
        $codes[] = 'userDisplay';
        // main page of ajax area
        $codes[] = 'ajax';
        return $codes;
    }    
    
        /**
     * Command event handler.
     *
     * This event handler is called when a command is issued by the user.
     */
    public function handleCommand(Zikula_Form_View $view, &$args)
    { 
        // get the relevant album for redirect
        // with finish editing of just uploaded pictures
    	$albumid = $this->request->query->filter('album', 0, FILTER_SANITIZE_NUMBER_INT);
    	
    	if ($args['commandName'] == 'next') {
    		$args['status'] = 'next';
            $url = ModUtil::url('MUImage', 'user', 'editMulti', array('ot' => 'picture', 'id' => $this->idValues['id'] + 1, 'album' => $albumid));
    	}
    	
    	if ($args['commandName'] == 'previous')  {
            $url = ModUtil::url($this->name, 'user', 'editMulti', array('ot' => 'picture', 'id' => $this->idValues['id'] - 1, 'album' => $albumid));
    	}
    	
        if ($args['commandName'] == 'finish')  {
        	SessionUtil::delVar('muimagepictureids');
            $url = ModUtil::url($this->name, 'user', 'display', array('ot' => 'album', 'id' => $albumid));
    	}
    	
    	if ($args['commandName'] != 'cancel') {
   		
    		$args['commandName'] = 'update';    		
    		
            $result = parent::handleCommand($view, $args);
            if ($result === false) {
                return $result;
            }
            
            return $this->view->redirect($url);
    	}
    	
        if ($args['commandName'] == 'cancel') {
        	
        	SessionUtil::delVar('muimagepictureids');      	
        	$url = ModUtil::url($this->name, 'user', 'view', array('ot' => 'album'));
            return $this->view->redirect($url);
        }  
        	
        	return $this->view->redirect($url);
    }
    
    /**
     * Get success or error message for default operations.
     *
     * @param Array   $args    arguments from handleCommand method.
     * @param Boolean $success true if this is a success, false for default error.
     * @return String desired status or error message.
     */
    protected function getDefaultMessage($args, $success = false)
    {
        if ($success !== true) {
            return parent::getDefaultMessage($args, $success);
        }

        $message = '';
        switch ($args['commandName']) {
            case 'create':
                $message = $this->__('Done! Picture created.');
                break;
            case 'update':
                $message = $this->__('Done! Picture updated.');
                break;
            case 'update':
                $message = $this->__('Done! Picture deleted.');
                break;
        }
        return $message;
    }
         
    /**
     * Get url to redirect to.
     */
    protected function getRedirectUrl($args, $obj, $repeatCreateAction = false)
    {
        if ($this->inlineUsage == true) {
            $urlArgs = array('idp' => $this->idPrefix,
                'com' => $args['commandName']);
            $urlArgs = $this->addIdentifiersToUrlArgs($urlArgs);
            // inline usage, return to special function for closing the Zikula.UI.Window instance
            return ModUtil::url($this->name, 'user', 'handleInlineRedirect', $urlArgs);
        }

        if ($repeatCreateAction) {
            return $this->repeatReturnUrl;
        }

        // normal usage, compute return url from given redirect code
        if (!in_array($this->returnTo, $this->getRedirectCodes())) {
            // invalid return code, so return the default url
            return $this->getDefaultReturnUrl($args, $obj);
        }

        // parse given redirect code and return corresponding url
        switch ($this->returnTo) {
            case 'admin':
                return ModUtil::url($this->name, 'admin', 'main');
            case 'adminView':
                return ModUtil::url($this->name, 'admin', 'view',
                    array('ot' => $this->objectType));
            case 'adminDisplay':
                if ($args['commandName'] != 'delete' && !($this->mode == 'create' && $args['commandName'] == 'cancel')) {
                    return ModUtil::url($this->name, 'admin', $this->addIdentifiersToUrlArgs());
                }
                return $this->getDefaultReturnUrl($args, $obj);
            case 'user':
                return ModUtil::url($this->name, 'user', 'main');
            case 'userView':
                return ModUtil::url($this->name, 'user', 'view',
                    array('ot' => $this->objectType));
            case 'userDisplay':
                if ($args['commandName'] != 'delete' && !($this->mode == 'create' && $args['commandName'] == 'cancel')) {
                    return ModUtil::url($this->name, 'user', $this->addIdentifiersToUrlArgs());
                }
                return $this->getDefaultReturnUrl($args, $obj);
            case 'adminViewAlbum':
                return ModUtil::url($this->name, 'admin', 'view',
                    array('ot' => 'album'));
            case 'adminDisplayAlbum':
                if (!empty($this->album)) {
                    return ModUtil::url($this->name, 'admin', 'display', array('ot' => 'album', 'id' => $this->album));
                }
                return $this->getDefaultReturnUrl($args, $obj);
            case 'userViewAlbum':
                return ModUtil::url($this->name, 'user', 'view',
                    array('ot' => 'album'));
            case 'userDisplayAlbum':
                if (!empty($this->album)) {
                    return ModUtil::url($this->name, 'user', 'display', array('ot' => 'album', 'id' => $this->album));
                }
                return $this->getDefaultReturnUrl($args, $obj);
            default:
                return $this->getDefaultReturnUrl($args, $obj);
        }
    }
}
