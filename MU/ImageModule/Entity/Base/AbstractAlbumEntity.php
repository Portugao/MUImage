<?php
/**
 * Image.
 *
 * @copyright Michael Ueberschaer (MU)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Michael Ueberschaer <info@homepages-mit-zikula.de>.
 * @link https://homepages-mit-zikula.de
 * @link https://ziku.la
 * @version Generated by ModuleStudio (https://modulestudio.de).
 */

namespace MU\ImageModule\Entity\Base;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Zikula\Core\Doctrine\EntityAccess;
use MU\ImageModule\Traits\StandardFieldsTrait;
use MU\ImageModule\Validator\Constraints as ImageAssert;

/**
 * Entity class that defines the entity structure and behaviours.
 *
 * This is the base entity class for album entities.
 * The following annotation marks it as a mapped superclass so subclasses
 * inherit orm properties.
 *
 * @ORM\MappedSuperclass
 */
abstract class AbstractAlbumEntity extends EntityAccess implements Translatable
{
    /**
     * Hook standard fields behaviour embedding createdBy, updatedBy, createdDate, updatedDate fields.
     */
    use StandardFieldsTrait;

    /**
     * @var string The tablename this object maps to
     */
    protected $_objectType = 'album';
    
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", unique=true)
     * @var integer $id
     */
    protected $id = 0;
    
    /**
     * the current workflow state
     *
     * @ORM\Column(length=20)
     * @Assert\NotBlank()
     * @ImageAssert\ListEntry(entityName="album", propertyName="workflowState", multiple=false)
     * @var string $workflowState
     */
    protected $workflowState = 'initial';
    
    /**
     * @Gedmo\Translatable
     * @ORM\Column(length=255, unique=true)
     * @Assert\NotBlank()
     * @Assert\Length(min="0", max="255")
     * @var string $title
     */
    protected $title = '';
    
    /**
     * @Gedmo\Translatable
     * @ORM\Column(type="text", length=2000)
     * @Assert\NotNull()
     * @Assert\Length(min="0", max="2000")
     * @var text $description
     */
    protected $description = '';
    
    /**
     * @ORM\Column(name="parent_id", type="integer", nullable=true)
     * @var integer $parent_id
     */
    protected $parent_id = 0;
    
    /**
     * @ORM\Column(length=255)
     * @Assert\NotBlank()
     * @ImageAssert\ListEntry(entityName="album", propertyName="albumAccess", multiple=false)
     * @var string $albumAccess
     */
    protected $albumAccess = '';
    
    /**
     * @ORM\Column(length=255)
     * @Assert\NotNull()
     * @Assert\Length(min="0", max="255")
     * @var string $passwordAccess
     */
    protected $passwordAccess = '';
    
    /**
     * @ORM\Column(length=255)
     * @Assert\NotNull()
     * @Assert\Length(min="0", max="255")
     * @var string $myFriends
     */
    protected $myFriends = '';
    
    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var boolean $notInFrontend
     */
    protected $notInFrontend = false;
    
    /**
     * @ORM\Column(type="integer")
     * @Assert\Type(type="integer")
     * @Assert\NotBlank()
     * @Assert\NotEqualTo(value=0)
     * @Assert\LessThan(value=100000000000)
     * @var integer $pos
     */
    protected $pos = 1;
    
    
    /**
     * Used locale to override Translation listener's locale.
     * This is not a mapped field of entity metadata, just a simple property.
     *
     * @Assert\Locale()
     * @Gedmo\Locale
     * @var string $locale
     */
    protected $locale;
    
    /**
     * @ORM\OneToMany(targetEntity="\MU\ImageModule\Entity\AlbumCategoryEntity", 
     *                mappedBy="entity", cascade={"all"}, 
     *                orphanRemoval=true)
     * @var \MU\ImageModule\Entity\AlbumCategoryEntity
     */
    protected $categories = null;
    
    /**
     * Bidirectional - Many albums [albums] are linked by one album [album] (OWNING SIDE).
     *
     * @ORM\ManyToOne(targetEntity="MU\ImageModule\Entity\AlbumEntity", inversedBy="albums")
     * @ORM\JoinTable(name="mu_image_album",
     *      joinColumns={@ORM\JoinColumn(name="id", referencedColumnName="id" )},
     *      inverseJoinColumns={@ORM\JoinColumn(name="parent_id", referencedColumnName="id" )}
     * )
     * @Assert\Type(type="MU\ImageModule\Entity\AlbumEntity")
     * @var \MU\ImageModule\Entity\AlbumEntity $album
     */
    protected $album;
    
    /**
     * Bidirectional - One album [album] has many albums [albums] (INVERSE SIDE).
     *
     * @ORM\OneToMany(targetEntity="MU\ImageModule\Entity\AlbumEntity", mappedBy="album")
     * @ORM\JoinTable(name="mu_image_albumalbums",
     *      joinColumns={@ORM\JoinColumn(name="parent_id", referencedColumnName="id" )},
     *      inverseJoinColumns={@ORM\JoinColumn(name="id", referencedColumnName="id" )}
     * )
     * @var \MU\ImageModule\Entity\AlbumEntity[] $albums
     */
    protected $albums = null;
    
    /**
     * Bidirectional - One album [album] has many pictures [pictures] (INVERSE SIDE).
     *
     * @ORM\OneToMany(targetEntity="MU\ImageModule\Entity\PictureEntity", mappedBy="album")
     * @ORM\JoinTable(name="mu_image_albumpictures")
     * @ORM\OrderBy({"pos" = "ASC"})
     * @var \MU\ImageModule\Entity\PictureEntity[] $pictures
     */
    protected $pictures = null;
    
    
    /**
     * AlbumEntity constructor.
     *
     * Will not be called by Doctrine and can therefore be used
     * for own implementation purposes. It is also possible to add
     * arbitrary arguments as with every other class method.
     */
    public function __construct()
    {
        $this->albums = new ArrayCollection();
        $this->pictures = new ArrayCollection();
        $this->categories = new ArrayCollection();
    }
    
    /**
     * Returns the _object type.
     *
     * @return string
     */
    public function get_objectType()
    {
        return $this->_objectType;
    }
    
    /**
     * Sets the _object type.
     *
     * @param string $_objectType
     *
     * @return void
     */
    public function set_objectType($_objectType)
    {
        if ($this->_objectType != $_objectType) {
            $this->_objectType = isset($_objectType) ? $_objectType : '';
        }
    }
    
    
    /**
     * Returns the id.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Sets the id.
     *
     * @param integer $id
     *
     * @return void
     */
    public function setId($id)
    {
        if (intval($this->id) !== intval($id)) {
            $this->id = intval($id);
        }
    }
    
    /**
     * Returns the workflow state.
     *
     * @return string
     */
    public function getWorkflowState()
    {
        return $this->workflowState;
    }
    
    /**
     * Sets the workflow state.
     *
     * @param string $workflowState
     *
     * @return void
     */
    public function setWorkflowState($workflowState)
    {
        if ($this->workflowState !== $workflowState) {
            $this->workflowState = isset($workflowState) ? $workflowState : '';
        }
    }
    
    /**
     * Returns the title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
    
    /**
     * Sets the title.
     *
     * @param string $title
     *
     * @return void
     */
    public function setTitle($title)
    {
        if ($this->title !== $title) {
            $this->title = isset($title) ? $title : '';
        }
    }
    
    /**
     * Returns the description.
     *
     * @return text
     */
    public function getDescription()
    {
        return $this->description;
    }
    
    /**
     * Sets the description.
     *
     * @param text $description
     *
     * @return void
     */
    public function setDescription($description)
    {
        if ($this->description !== $description) {
            $this->description = isset($description) ? $description : '';
        }
    }
    
    /**
     * Returns the parent_id.
     *
     * @return integer
     */
    public function getParent_id()
    {
        return $this->parent_id;
    }
    
    /**
     * Sets the parent_id.
     *
     * @param integer $parent_id
     *
     * @return void
     */
    public function setParent_id($parent_id)
    {
        if (intval($this->parent_id) !== intval($parent_id)) {
            $this->parent_id = intval($parent_id);
        }
    }
    
    /**
     * Returns the album access.
     *
     * @return string
     */
    public function getAlbumAccess()
    {
        return $this->albumAccess;
    }
    
    /**
     * Sets the album access.
     *
     * @param string $albumAccess
     *
     * @return void
     */
    public function setAlbumAccess($albumAccess)
    {
        if ($this->albumAccess !== $albumAccess) {
            $this->albumAccess = isset($albumAccess) ? $albumAccess : '';
        }
    }
    
    /**
     * Returns the password access.
     *
     * @return string
     */
    public function getPasswordAccess()
    {
        return $this->passwordAccess;
    }
    
    /**
     * Sets the password access.
     *
     * @param string $passwordAccess
     *
     * @return void
     */
    public function setPasswordAccess($passwordAccess)
    {
        if ($this->passwordAccess !== $passwordAccess) {
            $this->passwordAccess = isset($passwordAccess) ? $passwordAccess : '';
        }
    }
    
    /**
     * Returns the my friends.
     *
     * @return string
     */
    public function getMyFriends()
    {
        return $this->myFriends;
    }
    
    /**
     * Sets the my friends.
     *
     * @param string $myFriends
     *
     * @return void
     */
    public function setMyFriends($myFriends)
    {
        if ($this->myFriends !== $myFriends) {
            $this->myFriends = isset($myFriends) ? $myFriends : '';
        }
    }
    
    /**
     * Returns the not in frontend.
     *
     * @return boolean
     */
    public function getNotInFrontend()
    {
        return $this->notInFrontend;
    }
    
    /**
     * Sets the not in frontend.
     *
     * @param boolean $notInFrontend
     *
     * @return void
     */
    public function setNotInFrontend($notInFrontend)
    {
        if (boolval($this->notInFrontend) !== boolval($notInFrontend)) {
            $this->notInFrontend = boolval($notInFrontend);
        }
    }
    
    /**
     * Returns the pos.
     *
     * @return integer
     */
    public function getPos()
    {
        return $this->pos;
    }
    
    /**
     * Sets the pos.
     *
     * @param integer $pos
     *
     * @return void
     */
    public function setPos($pos)
    {
        if (intval($this->pos) !== intval($pos)) {
            $this->pos = intval($pos);
        }
    }
    
    /**
     * Returns the locale.
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }
    
    /**
     * Sets the locale.
     *
     * @param string $locale
     *
     * @return void
     */
    public function setLocale($locale)
    {
        if ($this->locale != $locale) {
            $this->locale = $locale;
        }
    }
    
    /**
     * Returns the categories.
     *
     * @return ArrayCollection[]
     */
    public function getCategories()
    {
        return $this->categories;
    }
    
    
    /**
     * Sets the categories.
     *
     * @param ArrayCollection $categories List of categories
     *
     * @return void
     */
    public function setCategories(ArrayCollection $categories)
    {
        foreach ($this->categories as $category) {
            if (false === $key = $this->collectionContains($categories, $category)) {
                $this->categories->removeElement($category);
            } else {
                $categories->remove($key);
            }
        }
        foreach ($categories as $category) {
            $this->categories->add($category);
        }
    }
    
    /**
     * Checks if a collection contains an element based only on two criteria (categoryRegistryId, category).
     *
     * @param ArrayCollection $collection Given collection
     * @param \MU\ImageModule\Entity\AlbumCategoryEntity $element Element to search for
     *
     * @return bool|int
     */
    private function collectionContains(ArrayCollection $collection, \MU\ImageModule\Entity\AlbumCategoryEntity $element)
    {
        foreach ($collection as $key => $category) {
            /** @var \MU\ImageModule\Entity\AlbumCategoryEntity $category */
            if ($category->getCategoryRegistryId() == $element->getCategoryRegistryId()
                && $category->getCategory() == $element->getCategory()
            ) {
                return $key;
            }
        }
    
        return false;
    }
    
    /**
     * Returns the album.
     *
     * @return \MU\ImageModule\Entity\AlbumEntity
     */
    public function getAlbum()
    {
        return $this->album;
    }
    
    /**
     * Sets the album.
     *
     * @param \MU\ImageModule\Entity\AlbumEntity $album
     *
     * @return void
     */
    public function setAlbum($album = null)
    {
        $this->album = $album;
    }
    
    /**
     * Returns the albums.
     *
     * @return \MU\ImageModule\Entity\AlbumEntity[]
     */
    public function getAlbums()
    {
        return $this->albums;
    }
    
    /**
     * Sets the albums.
     *
     * @param \MU\ImageModule\Entity\AlbumEntity[] $albums
     *
     * @return void
     */
    public function setAlbums($albums)
    {
        foreach ($this->albums as $albumSingle) {
            $this->removeAlbums($albumSingle);
        }
        foreach ($albums as $albumSingle) {
            $this->addAlbums($albumSingle);
        }
    }
    
    /**
     * Adds an instance of \MU\ImageModule\Entity\AlbumEntity to the list of albums.
     *
     * @param \MU\ImageModule\Entity\AlbumEntity $album The instance to be added to the collection
     *
     * @return void
     */
    public function addAlbums(\MU\ImageModule\Entity\AlbumEntity $album)
    {
        $this->albums->add($album);
        $album->setAlbum($this);
    }
    
    /**
     * Removes an instance of \MU\ImageModule\Entity\AlbumEntity from the list of albums.
     *
     * @param \MU\ImageModule\Entity\AlbumEntity $album The instance to be removed from the collection
     *
     * @return void
     */
    public function removeAlbums(\MU\ImageModule\Entity\AlbumEntity $album)
    {
        $this->albums->removeElement($album);
        $album->setAlbum(null);
    }
    
    /**
     * Returns the pictures.
     *
     * @return \MU\ImageModule\Entity\PictureEntity[]
     */
    public function getPictures()
    {
        return $this->pictures;
    }
    
    /**
     * Sets the pictures.
     *
     * @param \MU\ImageModule\Entity\PictureEntity[] $pictures
     *
     * @return void
     */
    public function setPictures($pictures)
    {
        foreach ($this->pictures as $pictureSingle) {
            $this->removePictures($pictureSingle);
        }
        foreach ($pictures as $pictureSingle) {
            $this->addPictures($pictureSingle);
        }
    }
    
    /**
     * Adds an instance of \MU\ImageModule\Entity\PictureEntity to the list of pictures.
     *
     * @param \MU\ImageModule\Entity\PictureEntity $picture The instance to be added to the collection
     *
     * @return void
     */
    public function addPictures(\MU\ImageModule\Entity\PictureEntity $picture)
    {
        $this->pictures->add($picture);
        $picture->setAlbum($this);
    }
    
    /**
     * Removes an instance of \MU\ImageModule\Entity\PictureEntity from the list of pictures.
     *
     * @param \MU\ImageModule\Entity\PictureEntity $picture The instance to be removed from the collection
     *
     * @return void
     */
    public function removePictures(\MU\ImageModule\Entity\PictureEntity $picture)
    {
        $this->pictures->removeElement($picture);
        $picture->setAlbum(null);
    }
    
    
    
    /**
     * Creates url arguments array for easy creation of display urls.
     *
     * @return array List of resulting arguments
     */
    public function createUrlArgs()
    {
        return [
            'id' => $this->getId()
        ];
    }
    
    /**
     * Returns the primary key.
     *
     * @return integer The identifier
     */
    public function getKey()
    {
        return $this->getId();
    }
    
    /**
     * Determines whether this entity supports hook subscribers or not.
     *
     * @return boolean
     */
    public function supportsHookSubscribers()
    {
        return true;
    }
    
    /**
     * Return lower case name of multiple items needed for hook areas.
     *
     * @return string
     */
    public function getHookAreaPrefix()
    {
        return 'muimagemodule.ui_hooks.albums';
    }
    
    /**
     * Returns an array of all related objects that need to be persisted after clone.
     * 
     * @param array $objects Objects that are added to this array
     * 
     * @return array List of entity objects
     */
    public function getRelatedObjectsToPersist(&$objects = [])
    {
        return [];
    }
    
    /**
     * ToString interceptor implementation.
     * This method is useful for debugging purposes.
     *
     * @return string The output string for this entity
     */
    public function __toString()
    {
        return 'Album ' . $this->getKey() . ': ' . $this->getTitle();
    }
    
    /**
     * Clone interceptor implementation.
     * This method is for example called by the reuse functionality.
     * Performs a quite simple shallow copy.
     *
     * See also:
     * (1) http://docs.doctrine-project.org/en/latest/cookbook/implementing-wakeup-or-clone.html
     * (2) http://www.php.net/manual/en/language.oop5.cloning.php
     * (3) http://stackoverflow.com/questions/185934/how-do-i-create-a-copy-of-an-object-in-php
     */
    public function __clone()
    {
        // if the entity has no identity do nothing, do NOT throw an exception
        if (!$this->id) {
            return;
        }
    
        // otherwise proceed
    
        // unset identifier
        $this->setId(0);
    
        // reset workflow
        $this->setWorkflowState('initial');
    
        $this->setCreatedBy(null);
        $this->setCreatedDate(null);
        $this->setUpdatedBy(null);
        $this->setUpdatedDate(null);
    
    
        // clone categories
        $categories = $this->categories;
        $this->categories = new ArrayCollection();
        foreach ($categories as $c) {
            $newCat = clone $c;
            $this->categories->add($newCat);
            $newCat->setEntity($this);
        }
    }
}
