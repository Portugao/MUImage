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
 * It aims on the album object type.
 */
class MUImage_Form_Handler_User_Picture_Edit extends MUImage_Form_Handler_User_Picture_Base_Edit
{
	/**
	 * Initialize form handler.
	 *
	 * This method takes care of all necessary initialisation of our data and form states.
	 *
	 * @return boolean False in case of initialization errors, otherwise true.
	 */
	public function initialize(Zikula_Form_View $view)
	{	
		$dom = ZLanguage::getModuleDomain('MUImage');
		$id = $this->request->query->filter('id', 0, FILTER_SANITIZE_NUMBER_INT);
		
		$picturerepository = MUImage_Util_Model::getPictureRepository();
		$albumrepository = MUImage_Util_Model::getAlbumRepository();
		if ($id > 0) {
		$picture = $picturerepository->selectById($id);
		}
		
		$picture->getAlbum();
		//$albumid
		
		
		// we get the allowed filesize	
		$fileSize = MUImage_Util_Controller::maxSize();
		// we check if deleting of pictures is allowed
		$deletePictures = ModUtil::getVar($this->name, 'userDeletePictures');
		// we check for required width for pictures
		$minWidth = MUImage_Util_Controller::minWidth();
		// we check for user is in admin group
		$inAdminGroup = MUImage_Util_View::isAdmin();
		
		// if we want to edit an item
		if ($id > 0) {
			$myAlbums = MUImage_Util_View::getAlbums($id, 2);
		
			$myalbums = array();
		
			if (MUImage_Util_View::isAdmin() === true || MUImage_Util_View::otherUserMainAlbums() === true) {
				$myalbums[] = array('value' => '', 'text' => __('Choose an album'), $dom);
			}
		
			foreach ($myAlbums as $myAlbum) {
				$myalbums[] = array('value' => $myAlbum['id'], 'text' => $myAlbum['title'] . ' - ' . __('Owner:') . ' ' . UserUtil::getVar('uname', $myAlbum['createdUserId']) . ' - ' . __('Main album:') . ' ' . $myAlbum['parent']['title']);
			}
		}
		
		// controlling of albums in edit form
		// of pictures and albums
		$mainalbum = $this->view->get_template_vars('mainalbum');
		$mainalbum['muimageAlbum_AlbumItemListItems'] = $myalbums;
		$this->view->assign('mainalbum', $mainalbum);
			
		$this->view->assign('fileSize', $fileSize)
		           ->assign('minWidth', $minWidth)
		           ->assign('deletePictures', $deletePictures)
		           ->assign('inAdminGroup', $inAdminGroup);
		
		if (MUImage_Util_View::otherUserMainAlbums() == true) {
			$this->view->assign('otherMainAlbum', true);
		}
		else {
			$this->view->assign('otherMainAlbum', false);
		}
			
		parent::initialize($view);
	}

	/**
	 * Get the default redirect url. Required if no returnTo parameter has been supplied.
	 * This method is called in handleCommand so we know which command has been performed.
	 */
	protected function getDefaultReturnUrl($args, $obj)
	{
		$picturerepository = MUImage_Util_Model::getPictureRepository();
		$picture = $picturerepository->selectById($this->idValues['id']);
		if ($picture) {
			$album = $picture->getAlbum();
			$albumid = $album->getId();
		}
		else {
			$viewArgs = array('ot' => 'album');
			$url = ModUtil::url($this->name, 'user', 'view', $viewArgs);
			return $url;
		}
			
		// redirect to the album if existing
		if ($albumid > 0) {
			$viewArgs = array('ot' => 'album', 'id' => $albumid);
			$url = ModUtil::url($this->name, 'user', 'display', $viewArgs);
			return $url;
		}
		else {
			$viewArgs = array('ot' => 'album');
			$url = ModUtil::url($this->name, 'user', 'view', $viewArgs);
			return  $url;
		}


		if ($args['commandName'] != 'delete' && !($this->mode == 'create' && $args['commandName'] == 'cancel')) {
			// redirect to the detail page of treated picture
			$url = ModUtil::url($this->name, 'user', 'display', array('ot' => 'picture', 'id' => $this->idValues['id']));
		}
		return $url;
	}
}
