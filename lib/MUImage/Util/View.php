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

    /**
     * Determines the view template for a certain method with given parameters.
     *
     * @param Zikula_View $view    Reference to view object.
     * @param string      $type    Current controller (name of currently treated entity).
     * @param string      $func    Current function (main, view, ...).
     * @param array       $args    Additional arguments.
     *
     * @return string name of template file.
     */
    public function getViewTemplate(Zikula_View $view, $type, $func, $args = array())
    {
        // create the base template name
        $template = DataUtil::formatForOS($type . '/' . $func);

        // check for template extension
        $templateExtension = $this->determineExtension($view, $type, $func, $args);

        // check whether a special template is used
        $tpl = (isset($args['tpl']) && !empty($args['tpl'])) ? $args['tpl'] : FormUtil::getPassedValue('tpl', '', 'GETPOST', FILTER_SANITIZE_STRING);

        $templateExtension = '.' . $templateExtension;
        if ($templateExtension != '.tpl') {
            $templateExtension .= '.tpl';
        }

        if (!empty($tpl) && $view->template_exists($template . '_' . DataUtil::formatForOS($tpl) . $templateExtension)) {
            $template .= '_' . DataUtil::formatForOS($tpl);
        }

        $template .= $templateExtension;

        if (ModUtil::getVar($this->name, 'layout') == 'bootstrap') {
            $template = 'bootstrap/' . $template;
        }

        return $template;
    }

    /*
     * this function checks if an user is in the admin group
    * return boolean
    */
    public static function isAdmin()
    {
        $uid = UserUtil::getVar('uid');
        $gid = UserUtil::getGroupsForUser($uid);
        if (in_array(2, $gid)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * $param int $id     id of the item
     * $param int $kind   kind of item, 1 = album, other = picture
     * Returning the albums for dropdownlist in edit mode
     */
    public static function getAlbums($id = 0, $kind = 1)
    {
        $uid = UserUtil::getVar('uid');
        $repository = MUImage_Util_Model::getAlbumRepository();
        $thisAlbum = $repository->selectById($id);
        if ($kind == 1 && $id > 0) {        
            if ($thisAlbum) {
                $thisParent = $thisAlbum->getParent();
            }
            if ($thisParent) {
                $thisParentId = $thisParent->getId();
            } else {
                $thisParentId = NULL;
            }

            $mainAlbumMode = MUImage_Util_Controller::ruleEditMainAlbum($id);

            $childrenIds = self::getSubAlbums($thisAlbum);
             
            SessionUtil::delVar('muimagechildrenids');

            if ($childrenIds != false) {
                $where = 'tbl.id NOT IN (' . implode(', ', $childrenIds) . ')';
            }

            if ($where != '') {
                $where .= ' AND ';
                $where .= 'tbl.id != \'' . DataUtil::formatForStore($id) . '\'';
            } else {
                $where = 'tbl.id != \'' . DataUtil::formatForStore($id) . '\'';
            }
        }

        // we check for group member feature
        $groupMember = self::checkGroupMember($thisAlbum['createdUserId']);
        if ($groupMember == false) {
            // if user is not in admin group only own album will be shown
            if (MUImage_Util_View::isAdmin() === false) {
                if ($where != '') {
                    $where .= ' AND ';
                    $where .= 'tbl.createdUserId = \'' . DataUtil::formatForStore($uid) . '\'';
                } else {
                    $where = 'tbl.createdUserId = \'' . DataUtil::formatForStore($uid) . '\'';
                }
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
    public static function getSubAlbums($album, $childrenIds = array(), $start = 0)
    {
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
            } else {
                // nothing to do
            }
        }
        foreach ($children as $child) {
            $childrenIds = SessionUtil::getVar('muimagechildrenids');
            self::getSubAlbums($child, $childrenIds, $start);
        }

        if ($childrenIds && count($childrenIds > 0)) {
            return $childrenIds;
        } else {
            return false;
        }
    }

    /**
     * Counting pictures of an album
     *
     * @param int $albumid
     * @return number
     */
    public static function countAlbumPictures($albumid)
    {
        $view = new Zikula_Request_Http();
        $id = $view->query->filter('id', 0, FILTER_SANITIZE_STRING);
        $where = 'tbl.id = \'' . DataUtil::formatForStore($albumid) . '\'';

        $repository = MUImage_Util_Model::getAlbumRepository();
        $album = $repository->selectById($albumid);

        $count = 0;

        $pictures = $album->getPicture();
        $count = count($pictures);

        return $count;
    }


    /**
     * * Counting of total pictures
     * @return int $count
     */
    public static function countPictures()
    {
        $repository = MUImage_Util_Model::getPictureRepository();
        $count = $repository->selectCount();

        return $count;
    }


    /**
     * Counting of total albums
     *
     * @return int $count
     */
    public static function countAlbums()
    {
        $repository = MUImage_Util_Model::getAlbumRepository();
        $count = $repository->selectCount();

        return $count;
    }

    /**
     * this method checks if an user may create another main album
     * return true or false or the contingent
     * @param $kind           $kind   kind of check 1 for controlling links, other for get contingent
     */
    public static function otherUserMainAlbums($kind = 1)
    {
        $dom = ZLanguage::getModuleDomain('MUImage');
        $numberMainAlbums = ModUtil::getVar('MUImage', 'numberParentAlbums');
        if ($numberMainAlbums != '') {
            $uid = UserUtil::getVar('uid');
            $gid = UserUtil::getGroupsForUser($uid);
            if (in_array(2, $gid)) {
                if ($kind == 1) {
                    return true;
                } else {
                    $out = __('unlimited', $dom);
                    return $out;
                }
            } else {
                $albumrepository = MUImage_Util_Model::getAlbumRepository();
                $where = 'tbl.createdUserId = \'' . DataUtil::formatForStore($uid) . '\'';
                $where .= ' AND ';
                $where .= 'tbl.parent_id IS NULL';
                $albumcount = $albumrepository->selectCount($where);
                if ($kind == 1) {
                    if ($albumcount < $numberMainAlbums) {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    $contingentMainAlbums = $numberMainAlbums - $albumcount;
                    if ($contingentMainAlbums > 0) {
                        $out = $contingentMainAlbums;
                    } else {
                        $out = 0;
                    }
                    return $out;
                }
            }
        } else {
            if ($kind == 1) {
                return true;
            } else {
                $out = __('unlimited', $dom);
                return $out;
            }
        }
    }


    /**
     *
     * this method checks if an user may create another subalbum
     * @param int $kind
     * return true or false or string
     */
    public static function otherUserSubAlbums($kind = 1)
    {
        $dom = ZLanguage::getModuleDomain('MUImage');
        $numberSubAlbums = ModUtil::getVar('MUImage', 'numberSubAlbums');
        if ($numberSubAlbums != '') {
            $uid = UserUtil::getVar('uid');
            $gid = UserUtil::getGroupsForUser($uid);
            if (in_array(2, $gid)) {
                if ($kind == 1) {
                    return true;
                } else {
                    $out = __('unlimited', $dom);
                    return $out;
                }
            } else {
                $albumrepository = MUImage_Util_Model::getAlbumRepository();
                $where2 = 'tbl.createdUserId = \'' . DataUtil::formatForStore($uid) . '\'';
                $where2 .= ' AND ';
                $where2 .= 'tbl.parent_id > 0';
                $subalbumcount = $albumrepository->selectCount($where2);
                if ($kind == 1) {
                    if ($numberSubAlbums < 0) {
                        return false;
                    }
                    if ($subalbumcount < $numberSubAlbums) {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    if ($numberSubAlbums < 0) {
                        $out = 0;
                        return $out;
                    }
                    $contingentSubAlbums = $numberSubAlbums - $subalbumcount;
                    if ($contingentSubAlbums > 0) {
                        $out = $contingentSubAlbums;
                    } else {
                        $out = 0;
                    }
                    return $out;
                }
            }
        }
        else {
            if ($kind == 1) {
                return true;
            } else {
                $out = __('unlimited', $dom);
                return $out;
            }
        }
    }

    /**
     * this method checks if an user may create another picture
     * @param int $kind
     * return true or false or the number of allowed pictures
     */
    public static function otherUserPictures($kind = 1)
    {
        $dom = ZLanguage::getModuleDomain('MUImage');
        $numberPictures = ModUtil::getVar('MUImage', 'numberPictures');
        if ($numberPictures != '') {
            $uid = UserUtil::getVar('uid');
            $gid = UserUtil::getGroupsForUser($uid);
            if (in_array(2, $gid)) {
                if ($kind == 1) {
                    return true;
                } else {
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
                    } else {
                        return false;
                    }
                } else {
                    $contingentPictures = $numberPictures - $picturecount;
                    if ($contingentPictures> 0) {
                        $out = $contingentPictures;
                    } else {
                        $out = 0;
                    }
                    return $out;
                }
            }
        } else {
            if ($kind == 1) {
                return true;
            } else {
                $out = __('unlimited', $dom);
                return $out;
            }
        }
    }

    /**
     *
     * @return boolean
     */
    public static function myPicture()
    {

        $request = new Zikula_Request_Http();
        $id = $request->query->filter('id', 0, FILTER_SANITIZE_NUMBER_INT);

        $picturerepository = MUImage_Util_Model::getPictureRepository();
        $myPicture = $picturerepository->selectById($id);

        $uid = UserUtil::getVar('uid');
        $groups = UserUtil::getGroupsForUser($uid);
        die($groups);
        if (in_array(2, $groups)) {
            return true;
        } else {
            if ($uid == $myPicture->getCreatedUserId()) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     *
     * @param int $id user id
     * @return array
     */
    public static function myAlbums($id)
    {
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
     * @param int $id user id
     * @return boolean
     */
    public static function myAlbum($id)
    {

        $albumrepository = MUImage_Util_Model::getAlbumRepository();
        $myAlbum = $albumrepository->selectById($id);

        $uid = UserUtil::getVar('uid');

        if (in_array(2, UserUtil::getGroupsForUser($uid))) {
            return true;
        } else {
            if ($uid == $myAlbum->getCreatedUserId()) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     *
     * @return string $out
     */
    public static function contingent()
    {
        $dom = ZLanguage::getModuleDomain('MUImage');

        if (UserUtil::isLoggedIn() === false) {
            $numberMain = 0;
            $numberSub = 0;
            $numberPictures = 0;
        } else {
            $uid = UserUtil::getVar('uid');
            $mainAlbum = ModUtil::getVar('MUImage', 'numberParentAlbums');
            if ($mainAlbum != '') {
                $numberMain = self::otherUserMainAlbums(2);
            } else {
                $numberMain = __('unlimited', $dom);
            }

            $subAlbum = ModUtil::getVar('MUImage', 'numberSubAlbums');
            if ($subAlbum != '') {
                $numberSub = self::otherUserSubAlbums(2);
            } else {
                $numberSub = __('unlimited', $dom);
            }

            $pictures = ModUtil::getVar('MUImage', 'numberPictures');
            if ($pictures != '') {
                $numberPictures = self::otherUserPictures(2);
            } else {
                $numberPictures = __('unlimited', $dom);
            }
        }

        $out = __('Your Quota: ', $dom);
        $out .= __('Main Albums: ', $dom) . $numberMain . ', ' . __('Sub Albums: ', $dom) . $numberSub . ', ' . __('Pictures: ', $dom) . $numberPictures;

        return $out;

    }

    /**
     *
     */
    public static function checkAlbumAccess($albumid)
    {
        // we get the actual user id
        $userid = UserUtil::getVar('uid');
        
        $albumrepository = MUImage_Util_Model::getAlbumRepository();
        $thisAlbum = $albumrepository->selectById($albumid);
        
        $groupMember = self::checkGroupMember($thisAlbum['createdUserId']);
        if ($groupMember == 1) {
            return 1;
        }
        
        if ($thisAlbum['notInFrontend'] == 1 && $thisAlbum['createduserId'] != $userid) {
            return 0;
        }
        if ($thisAlbum['albumAccess'] == 'all' ) {
            return 1;
        }
        if ($thisAlbum['albumAccess'] == 'users' && UserUtil::isLoggedIn() === true) {
            return 1;
        }
        if ($thisAlbum['albumAccess'] == 'friends') {
   
            if ($thisAlbum['createdUserId'] == $userid) {
                return 1;
            }
            $friends = explode(',', $thisAlbum['myFriends']);
            if (is_array($friends)) {
                foreach ($friends as $friend) {
                    $friendIds[] = UserUtil::getIdFromName($friend);
                }
            }
            if (is_array($friendIds)) {
                if (in_array($userid, $friendIds)) {
                    return 1;
                }
            }
        }
        if ($thisAlbum['albumAccess'] == 'known') {
            $userid = UserUtil::getVar('uid');
            if ($thisAlbum['createdUserId'] == $userid) {
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
     *  @param $createdUserId     id of the user created an item
     */
    public static function checkGroupMember($createdUserId)
    {
        if (UserUtil::isLoggedIn() === false) {
            return false;
        }
        $uid = UserUtil::getVar('uid');
        $uidGroups = UserUtil::getGroupListForUser($uid);
        $uidGroups = explode(',', $uidGroups);

        $createdUserIdGroups = UserUtil::getGroupListForUser($createdUserId);
        $createdUserIdGroups = explode(',', $createdUserIdGroups);

        if ($uid == $createdUserId) {
            return true;
        }

        $commonGroup = ModUtil::getVar('MUImage', 'groupForCommonAlbums');

        if ($commonGroup != 'notset') {
            foreach ($uidGroups as $uidGroup) {
                if ($uidGroup == 2) {
                    return true;
                }
                if (in_array($uidGroup, $createdUserIdGroups)) {
                    if ($uidGroup > 2) {
                        return true;
                    }
                }
            }
        } else {
            foreach ($uidGroups as $uidGroup) {
                if ($uidGroup == 2) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param   $id          $id  of album or picture
     * @param   $kind        $kind check for 1 = album or 2 = picture
     *
     * assign to template
     */
    public static function checkForBlocksAndContent($id = 0, $kind = 1)
    {
        $serviceManager = ServiceUtil::getManager();
        $view = new Zikula_View($serviceManager);
        if ($id > 0) {
            $block = BlockUtil::getBlockInfo($id, 'content');
            LogUtil::registerStatus($block);
            $view->assign('muimageblock', $block);
        }
    }
}
