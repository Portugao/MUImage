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
 * @version Generated by ModuleStudio 0.5.4 (http://modulestudio.de) at Fri Feb 17 18:57:24 CET 2012.
 */

/**
 * The muimageBreadcrump method returns a breadcrump code to the template
 *
 * @return string
 */
function smarty_function_muimageBreadcrumb($params, $view)
{
    $albumId = $params['albumId'];
    $repository = MUImage_Util_Model::getAlbumRepository();
    $album = $repository->selectById($albumId);
    
    if (is_object($album) && $album['parent_id'] != NULL) {
        $parentAlbum = $album->getParent();
        $params['out'] .= '>> ' . $parentAlbum['title'] . $params['out'];
        $params['albumId'] = $album['parent_id'];
        smarty_function_muimageBreadcrumb($params, $view);
        
    } else {
        $out = 'Album Overview' . $params['out'];
    }

    if (array_key_exists('assign', $params)) {
        $view->assign($params['assign'], $out);
        return;
    }
    return $out;
}
