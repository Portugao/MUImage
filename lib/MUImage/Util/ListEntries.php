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
 * Utility implementation class for list field entries related methods.
 */
class MUImage_Util_ListEntries extends MUImage_Util_Base_ListEntries
{
    /**
     * Get 'album access' list entries.
     *
     * @return array Array with desired list entries.
     */
    public function getAlbumAccessEntriesForAlbum()
    {
        $states = array();
        $states[] = array('value'   => 'all',
                          'text'    => $this->__('All'),
                          'title'   => '',
                          'image'   => '',
                          'default' => false);
        $states[] = array('value'   => 'users',
                          'text'    => $this->__('Users'),
                          'title'   => $this->__('only registered users'),
                          'image'   => '',
                          'default' => false);
        $states[] = array('value'   => 'friends',
                          'text'    => $this->__('Friends'),
                          'title'   => '',
                          'image'   => '',
                          'default' => false);
        $states[] = array('value'   => 'known',
                          'text'    => $this->__('Known password'),
                          'title'   => '',
                          'image'   => '',
                          'default' => false);
       /* $states[] = array('value'   => 'forme',
                          'text'    => $this->__('Only for me'),
                          'title'   => '',
                          'image'   => '',
                          'default' => false);*/
    
        return $states;
    }
}
