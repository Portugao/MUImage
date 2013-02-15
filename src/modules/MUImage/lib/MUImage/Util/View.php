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
 * @version Generated by ModuleStudio 0.5.4 (http://modulestudio.de) at Sat Feb 18 18:42:39 CET 2012.
 */

/**
 * Utility implementation class for view helper methods.
 */
class MUImage_Util_View extends MUImage_Util_Base_View
{

	/*
	 * this function checks if an user is in the admin group
	* return boolean
	*/
	public static function isAdmin() {
		$uid = UserUtil::getVar('uid');
		$gid = UserUtil::getGroupsForUser($uid);
		if (in_array(2, $gid)) {
			return true;
		}
		else {
			return false;
		}
	}
	/**
	 * $param int $id     id of the item
	 * $param int $kind   kind of item, 1 = album, other = picture
	 * Returning the albums for dropdownlist in edit mode
	 */

	public static function getAlbums($id, $kind = 1) {

		$uid = UserUtil::getVar('uid');
		$repository = MUImage_Util_Model::getAlbumRepository();
		if ($kind == 1) {
			$thisAlbum = $repository->selectById($id);
			if ($thisAlbum) {
				$thisParent = $thisAlbum->getParent();
			}
			if ($thisParent) {
				$thisParentId = $thisParent->getId();
			}
			else {
				$thisParentId = NULL;
			}

			$childrenIds = self::getSubAlbums($thisAlbum);

			if ($childrenIds != false) {

				$where = 'tbl.id NOT IN (' . implode(', ', $childrenIds) . ')';

			}

			if ($where != '') {
				$where .= ' AND ';
				$where .= 'tbl.id != \'' . DataUtil::formatForStore($id) . '\'';
			}
			else {
				$where = 'tbl.id != \'' . DataUtil::formatForStore($id) . '\'';
			}
		}

		if (MUImage_Util_View::isAdmin() === false) {
			if ($where != '') {

				$where .= ' AND ';
				$where .= 'tbl.createdUserId = \'' . DataUtil::formatForStore($uid) . '\'';
			}
			else {
				$where = 'tbl.createdUserId = \'' . DataUtil::formatForStore($uid) . '\'';
			}
		}

		if ($thisParentId != NULL && $thisParent['createdUserId'] == $uid) {
			$albums[] = $thisParent;
		}

		$orderBy = 'title ASC';

		$albums = $repository->selectWhere($where, $orderBy);

		return $albums;
	}

	/**
	 *
	 * @param object $album
	 * @param array $childrenIds
	 * @return boolean|array
	 */
	public static function getSubAlbums($album, $childrenIds = array(), $start = 0) {

		$childrenIds = SessionUtil::getVar('muimagechildrenids', false);

		if ($start == 0) {
			$childrenIds[] = $album['id'];
			$start++;
		}

		// we get he children albums of this album
		$children = $album->getChildren();
		// to array
		$children = $children->toArray();

		foreach ($children as $child) {
				
			if ($child['id'] > 0) {
				$childrenIds[] = $child['id'];
				SessionUtil::setVar('muimagechildrenids', $childrenIds);
			}
			else {
				// nothing to do
					
			}
		}
		foreach ($children as $child) {
			$childrenIds = SessionUtil::getVar('muimagechildrenids');
			self::getSubAlbums($child, $childrenIds, $start);
		}

		if ($childrenIds && count($childrenIds > 0)) {
			return $childrenIds;
		}
		else
		{
			return false;
		}

	}

	/**
	 * Counting pictures of an album
	 */

	public static function countAlbumPictures($albumid) {

		$view = new Zikula_Request_Http();
		$id = (int) $view->getGet()->filter('id', 0, FILTER_SANITIZE_STRING);
		$where = 'tbl.id = \'' . DataUtil::formatForStore($albumid) . '\'';

		$repository = MUImage_Util_Model::getAlbumRepository();
		$album = $repository->selectById($albumid);

		$count = 0;

		$pictures = $album->getPicture();
		$count = count($pictures);

		return $count;
	}

	/**
	 *
	 * Counting of total pictures
	 */
	public static function countPictures()
	{
		$repository = MUImage_Util_Model::getPictureRepository();
		$count = $repository->selectCount();

		return $count;
	}

	/**
	 *
	 * Counting of total albums
	 */
	public static function countAlbums()
	{
		$repository = MUImage_Util_Model::getAlbumRepository();
		$count = $repository->selectCount();

		return $count;
	}

	/**
	 *this method checks if an user may create another main album
	 * return true or false or the contingent
	 * @param $kind           $kind   kind of check 1 for controlling links, other for get contingent
	 */
	public static function otherUserMainAlbums($kind = 1) {
		$dom = ZLanguage::getModuleDomain('MUImage');
		$numberMainAlbums = ModUtil::getVar('MUImage', 'numberParentAlbums');
		if ($numberMainAlbums != '') {
			$uid = UserUtil::getVar('uid');
			$gid = UserUtil::getGroupsForUser($uid);
			if (in_array(2, $gid)) {
				if ($kind == 1) {
					return true;
				}
				else {
					$out = __('unlimited', $dom);
					return $out;
				}
			}
			else {
				$albumrepository = MUImage_Util_Model::getAlbumRepository();
				$where = 'tbl.createdUserId = \'' . DataUtil::formatForStore($uid) . '\'';
				$where .= ' AND ';
				$where .= 'tbl.parent_id IS NULL';
				$albumcount = $albumrepository->selectCount($where);
				if ($kind == 1) {
					if ($albumcount < $numberMainAlbums) {
						return true;
					}
					else {
						return false;
					}
				}
				else {
					$contingentMainAlbums = $numberMainAlbums - $albumcount;
					if ($contingentMainAlbums > 0) {
						$out = $contingentMainAlbums;
					}
					else {
						$out = 0;
					}
					return $out;
				}
			}
		}
		else {
			if ($kind == 1) {
				return true;
			}
			else {
				$out = __('unlimited', $dom);
				return $out;
			}
		}
	}

	/**
	 * this method checks if an user may create another subalbum
	 * return true or false
	 */
	public static function otherUserSubAlbums($kind = 1) {
		$dom = ZLanguage::getModuleDomain('MUImage');
		$numberSubAlbums = ModUtil::getVar('MUImage', 'numberSubAlbums');
		if ($numberSubAlbums != '') {
			$uid = UserUtil::getVar('uid');
			$gid = UserUtil::getGroupsForUser($uid);
			if (in_array(2, $gid)) {
				if ($kind == 1) {
					return true;
				}
				else {
					$out = __('unlimited', $dom);
					return $out;
				}
			}
			else {
				$albumrepository = MUImage_Util_Model::getAlbumRepository();
				$where2 = 'tbl.createdUserId = \'' . DataUtil::formatForStore($uid) . '\'';
				$where2 .= ' AND ';
				$where2 .= 'tbl.parent_id > 0';
				$subalbumcount = $albumrepository->selectCount($where2);
				if ($kind == 1) {
					if ($subalbumcount < $numberSubAlbums) {
						return true;
					}
					else {
						return false;
					}
				}
				else {
					$contingentSubAlbums = $numberSubAlbums - $subalbumcount;
					if ($contingentSubAlbums > 0) {
						$out = $contingentSubAlbums;
					}
					else {
						$out = 0;
					}
					return $out;
				}
			}
		}
		else {
			if ($kind == 1) {
				return true;
			}
			else {
				$out = __('unlimited', $dom);
				return $out;
			}
		}
	}

	/**
	 * this method checks if an user may create another picture
	 * return true or false or the number of allowed pictures
	 */
	public static function otherUserPictures($kind = 1) {
		$dom = ZLanguage::getModuleDomain('MUImage');
		$numberPictures = ModUtil::getVar('MUImage', 'numberPictures');
		if ($numberPictures != '') {
			$uid = UserUtil::getVar('uid');
			$gid = UserUtil::getGroupsForUser($uid);
			if (in_array(2, $gid)) {
				if ($kind == 1) {
					return true;
				}
				else {
					$out = __('unlimited', $dom);
					return $out;
				}
			}
			else {
				$picturerepository = MUImage_Util_Model::getPictureRepository();
				$where3 = 'tbl.createdUserId = \'' . DataUtil::formatForStore($uid) . '\'';
				$picturecount = $picturerepository->selectCount($where3);
				if ($kind == 1) {
					if ($picturecount < $numberPictures) {
						return true;
					}
					else {
						return false;
					}
				}
				else {
					$contingentPictures = $numberPictures - $picturecount;
					if ($contingentPictures> 0) {
						$out = $contingentPictures;
					}
					else {
						$out = 0;
					}
					return $out;
				}
			}
		}
		else {
			if ($kind == 1) {
				return true;
			}
			else {
				$out = __('unlimited', $dom);
				return $out;
			}
		}
	}

	/**
	 *
	 */
	public static function myPicture($id) {

		$picturerepository = MUImage_Util_Model::getPictureRepository();
		$myPicture = $picturerepository->selectById($id);

		if (in_array(2, UserUtil::getGroupsForUser(UserUtil::getVar('uid')))) {
			return true;
		}
		else {
			if (UserUtil::getVar('uid') == $myPicture->getCreatedUserId()) {
				return true;
			}
			else {
				return false;
			}
		}
	}	/**
	*
	*/
	public static function myAlbums($id) {

		$uid = UserUtil::getVar('uid');
		$albumrepository = MUImage_Util_Model::getAlbumRepository();
		$where = 'tbl.createdUserId = \'' . DataUtil::formatForStore($uid) . '\'';
		$where .= ' AND ';
		$where .= 'tbl.id != \'' . DataUtil::formatForStore($id) . '\'';
		$myAlbums = $albumrepository->selectWhere($where);

		return $myAlbums;
	}

	/**
	 *
	 */
	public static function myAlbum($id) {

		$albumrepository = MUImage_Util_Model::getAlbumRepository();
		$myAlbum = $albumrepository->selectById($id);

		$uid = UserUtil::getVar('uid');

		if (in_array(2, UserUtil::getGroupsForUser($uid))) {
			return true;
		}
		else {
			if ($uid == $myAlbum->getCreatedUserId()) {
				return true;
			}
			else {
				return false;
			}
		}
	}

	/**
	 *
	 */
	public static function contingent() {

		$dom = ZLanguage::getModuleDomain('MUImage');

		if (UserUtil::isLoggedIn() === false) {
			$numberMain = 0;
			$numberSub = 0;
			$numberPictures = 0;
		}
		else {
			$uid = UserUtil::getVar('uid');
			$mainAlbum = ModUtil::getVar('MUImage', 'numberParentAlbums');
			if ($mainAlbum != '') {
				$numberMain = self::otherUserMainAlbums(2);
			}
			else {
				$numberMain = __('unlimited', $dom);
			}

			$subAlbum = ModUtil::getVar('MUImage', 'numberSubAlbums');
			if ($subAlbum != '') {
				$numberSub = self::otherUserSubAlbums(2);
			}
			else {
				$numberSub = __('unlimited', $dom);
			}

			$pictures = ModUtil::getVar('MUImage', 'numberPictures');
			if ($pictures != '') {
				$numberPictures = self::otherUserPictures(2);
			}
			else {
				$numberPictures = __('unlimited', $dom);
			}
		}

		$out = __('Your Quota: ', $dom);
		$out .= __('Main Albums: ', $dom) . $numberMain . ', ' . __('Sub Albums: ', $dom) . $numberSub . ', ' . __('Pictures: ', $dom) . $numberPictures;

		return $out;

	}

	/**
	 * @param   $id          $id  of album or picture
	 * @param   $kind        $kind check for 1 = album or 2 = picture
	 *
	 *  assign to template
	 */
	public static function checkForBlocksAndContent($id = 0, $kind = 1) {
		$serviceManager = ServiceUtil::getManager();
		$view = new Zikula_View($serviceManager);
		if ($id > 0) {
			$block = BlockUtil::getBlockInfo($id, 'content');
			LogUtil::registerStatus($block);
			$view->assign('muimageblock', $block);
		}
	}
}
