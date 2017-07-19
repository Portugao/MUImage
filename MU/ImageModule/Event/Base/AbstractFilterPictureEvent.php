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

namespace MU\ImageModule\Event\Base;

use Symfony\Component\EventDispatcher\Event;
use MU\ImageModule\Entity\PictureEntity;

/**
 * Event base class for filtering picture processing.
 */
class AbstractFilterPictureEvent extends Event
{
    /**
     * @var PictureEntity Reference to treated entity instance.
     */
    protected $picture;

    /**
     * @var array Entity change set for preUpdate events.
     */
    protected $entityChangeSet = [];

    /**
     * FilterPictureEvent constructor.
     *
     * @param PictureEntity $picture Processed entity
     * @param array $entityChangeSet Change set for preUpdate events
     */
    public function __construct(PictureEntity $picture, $entityChangeSet = [])
    {
        $this->picture = $picture;
        $this->entityChangeSet = $entityChangeSet;
    }

    /**
     * Returns the entity.
     *
     * @return PictureEntity
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * Returns the change set.
     *
     * @return array
     */
    public function getEntityChangeSet()
    {
        return $this->entityChangeSet;
    }
}