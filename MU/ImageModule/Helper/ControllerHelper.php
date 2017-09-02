<?php
/**
 * Image.
 *
 * @copyright Michael Ueberschaer (MU)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Michael Ueberschaer <kontakt@webdesign-in-bremen.com>.
 * @link http://www.webdesign-in-bremen.com
 * @link http://zikula.org
 * @version Generated by ModuleStudio (http://modulestudio.de).
 */

namespace MU\ImageModule\Helper;

use MU\ImageModule\Helper\Base\AbstractControllerHelper;
use LogUtil;
use ModUtil;
use UserUtil;

/**
 * Helper implementation class for controller layer methods.
 */
class ControllerHelper extends AbstractControllerHelper
{
	/**
	 * The muimageCheckGroupMember method is checking if the actual user
	 * is in the same group as user created the relevant item
	 *
	 * @param unknown $createdBy        	
	 */
	public function checkGroupMember($created) {
		if (\UserUtil::isLoggedIn () === false) {
			return false;
		}
		$uid = \UserUtil::getVar('uid');
		if ($uid == $created) {
			return true;
		}
		
		$uidGroups = \UserUtil::getGroupListForUser ( $uid );
		$uidGroups = explode ( ',', $uidGroups );
		
		$createdUserIdGroups = \UserUtil::getGroupListForUser ($created);
		$createdUserIdGroups = explode ( ',', $createdUserIdGroups );
		
		$commonGroup = \ModUtil::getVar ( 'MUImageModule', 'groupForCommonAlbums' );
		
		if ($commonGroup != 'notset') {
			foreach ( $uidGroups as $uidGroup ) {
				if ($uidGroup == 2) {
					return true;
				}
				if (in_array ( $uidGroup, $createdUserIdGroups )) {
					if ($uidGroup > 2) {
						return true;
					}
				}
			}
		} else {
			foreach ( $uidGroups as $uidGroup ) {
				if ($uidGroup == 2) {
					return true;
				}
			}
		}
		
		return false;
	}
	
	
	public function checkAlbumAccess($albumid) 
	{
		// we get the actual user id
		$userid = \UserUtil::getVar('uid');
		
		$albumrepository = $this->entityFactory->getRepository('album');
		
		$thisAlbum = $albumrepository->selectById($albumid);
		
		$groupMember = self::checkGroupMember($thisAlbum['createdBy_id']);
		if ($groupMember == 1) {
			return 1;
		}
		
		if ($thisAlbum['notInFrontend'] == 1 && $thisAlbum['createdBy_id'] != $userid) {
			return 0;
		}
		if ($thisAlbum['albumAccess'] == 'all' ) {
			return 1;
		}
		if ($thisAlbum['albumAccess'] == 'users' && \UserUtil::isLoggedIn() === true) {
			return 1;
		}
		if ($thisAlbum['albumAccess'] == 'friends') {
			 
			if ($thisAlbum['createdBy_id'] == $userid) {
				return 1;
			}
			$friends = explode(',', $thisAlbum['myFriends']);
			if (is_array($friends)) {
				foreach ($friends as $friend) {
					$friendIds[] = \UserUtil::getIdFromName($friend);
				}
			}
			if (is_array($friendIds)) {
				if (in_array($userid, $friendIds)) {
					return 1;
				}
			}
		}
		if ($thisAlbum['albumAccess'] == 'known') {
			$userid = \UserUtil::getVar('uid');
			if ($thisAlbum['createdBy'] == $userid) {
				return 1;
			} else {
				$passwordArray = SessionUtil::getVar('muimagePasswordArray');
				if (is_array($passwordArray)) {
					foreach ($passwordArray as $key => $password) {
						if ($key == $thisAlbum['id'] && $password == $thisAlbum['passwordAccess']) {
							return 1;
						}
					}
					return 2;
		
				} else {
					return 2;
				}
			}
		}
		
		return false;		
	}
	
	/**
	 * 
	 * @param int $albumId
	 * @return unknown|array
	 */
	public function giveImageOfAlbum($albumId)
	{
		$where = '';
		$pictures = '';
		$where = 'tbl.album = ' . \DataUtil::formatForStore($albumId);
		$where .= ' AND ';
		$where .= 'tbl.albumImage = 1';
		$pictureRespository = $this->entityFactory->getRepository('picture');
		$albumpicture = $pictureRespository->selectWhere($where);	
		
		if ($albumpicture) {
		if (!is_array($albumpicture)) {
			$where2 = '';
			$where2 = 'tbl.album = ' . \DataUtil::formatForStore($albumId);
			$pictures = $this->selectionHelper->getEntities('picture', [], $where2);
		} else {
			return $albumpicture[0];
		}
		if (is_array($pictures)) {
		return $pictures[0];
		} else {
			return '';
		}
		} else {
			return '';
		}
	}
	
	public function breadcrumb($albumId, $params = array())
	{
		//$dom = ZLanguage::getModuleDomain('MUImage');
		
		$repository = $this->entityFactory->getRepository('album');
		$album = $repository->selectById($albumId);

		if (!isset($params['out'])) {
			$out = '';
		} else {
			$out = html_entity_decode($params['out']);
		}
		if (!isset($params['loop'])) {
			$loop = 0;
		} else {
			$loop = $params['loop'];
		}
		if ($loop == 0) {
			$thisAlbum = $album;
		} else {
			$thisAlbum = $params['thisAlbum'];
		}

		
		if ($thisAlbum['parent_id'] != NULL) {
			$albumParent = $repository->selectById($album['parent_id']);
			\LogUtil::registerStatus($album['album']['id']);
				$url = ModUtil::url('MUImage', 'user', 'display', array('ot' => 'album', 'id' => $albumParent['id']));
				$out = '<li><a href="' . $url . '">' . $albumParent['title'] . '</a></li>' . $out;
		
				$parentAlbumId = $albumParent['id'];
				$params['out'] = $out;
				$params['loop'] = $loop + 1;
				$params['thisAlbum'] = $thisAlbum;
				\LogUtil::registerStatus('Out: ' . $out);
				\LogUtil::registerStatus('ParentId: ' . $parentAlbumId);
				self::breadcrumb($parentAlbumId, $params);
			} else {
				$url = ModUtil::url('MUImageModule', 'album', 'view');
				$out = '<ol class="breadcrumb">' . '<li><a href="' . $url . '">' . __('Albums') . '</a></li>' . $out . '<li>' . $thisAlbum['title'] . '</li>' . '</ol>';

				return $out;
			}
		}
		
		/**
		 *
		 * @param string $module
		 * @return unknown|array
		 */
		public function avatar($module, $width)
		{
			$where = '';
			$avatar = '';
			$where = $module . 'IN (tbl.supportesModules)';
			$where .= ' OR ';
			$where .= 'tbl.supportedModules = All';

			$avatarRespository = $this->entityFactory->getRepository('avatar');
			$avatar = $avatarRespository->selectWhere($where);
		
			if ($albumpicture) {
				if (!is_array($albumpicture)) {
					$where2 = '';
					$where2 = 'tbl.album = ' . \DataUtil::formatForStore($albumId);
					$pictures = $this->selectionHelper->getEntities('picture', [], $where2);
				} else {
					return $albumpicture[0];
				}
				if (is_array($pictures)) {
					return $pictures[0];
				} else {
					return '';
				}
			} else {
				return '';
			}
		}
}
