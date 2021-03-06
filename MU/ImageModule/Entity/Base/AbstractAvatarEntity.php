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
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Zikula\Core\Doctrine\EntityAccess;
use MU\ImageModule\Traits\StandardFieldsTrait;
use MU\ImageModule\Validator\Constraints as ImageAssert;

/**
 * Entity class that defines the entity structure and behaviours.
 *
 * This is the base entity class for avatar entities.
 * The following annotation marks it as a mapped superclass so subclasses
 * inherit orm properties.
 *
 * @ORM\MappedSuperclass
 */
abstract class AbstractAvatarEntity extends EntityAccess
{
    /**
     * Hook standard fields behaviour embedding createdBy, updatedBy, createdDate, updatedDate fields.
     */
    use StandardFieldsTrait;

    /**
     * @var string The tablename this object maps to
     */
    protected $_objectType = 'avatar';
    
    /**
     * @var string Path to upload base folder
     */
    protected $_uploadBasePath = '';
    
    /**
     * @var string Base URL to upload files
     */
    protected $_uploadBaseUrl = '';
    
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", unique=true)
     * @Assert\Type(type="integer")
     * @Assert\NotNull()
     * @Assert\LessThan(value=1000000000)
     * @var integer $id
     */
    protected $id = 0;
    
    /**
     * the current workflow state
     *
     * @ORM\Column(length=20)
     * @Assert\NotBlank()
     * @ImageAssert\ListEntry(entityName="avatar", propertyName="workflowState", multiple=false)
     * @var string $workflowState
     */
    protected $workflowState = 'initial';
    
    /**
     * @ORM\Column(length=255)
     * @Assert\NotBlank()
     * @Assert\Length(min="0", max="255")
     * @var string $title
     */
    protected $title = '';
    
    /**
     * Here you can enter, for which use cases this avatar is.
     *
     * @ORM\Column(type="text", length=2000, nullable=true)
     * @Assert\Length(min="0", max="2000")
     * @var text $description
     */
    protected $description = '';
    
    /**
     * Avatar upload meta data array.
     *
     * @ORM\Column(type="array")
     * @Assert\Type(type="array")
     * @var array $avatarUploadMeta
     */
    protected $avatarUploadMeta = [];
    
    /**
     * @ORM\Column(name="avatarUpload", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(min="0", max="255")
     * @var string $avatarUploadFileName
     */
    protected $avatarUploadFileName = null;
    
    /**
     * Full avatar upload path as url.
     *
     * @Assert\Type(type="string")
     * @var string $avatarUploadUrl
     */
    protected $avatarUploadUrl = '';
    
    /**
     * Avatar upload file object.
     *
     * @Assert\File(
     *    maxSize = "100k",
     *    mimeTypes = {"image/*"}
     * )
     * @Assert\Image(
     *    minWidth = 200,
     *    maxWidth = 600,
     *    minHeight = 200,
     *    maxHeight = 600,
     *    allowLandscape = false,
     *    allowPortrait = false,
     *    detectCorrupted = true
     * )
     * @var File $avatarUpload
     */
    protected $avatarUpload = null;
    
    /**
     * Be sure that you set the supported module in a logic way!
     *
     * @ORM\Column(length=255)
     * @Assert\NotBlank()
     * @ImageAssert\ListEntry(entityName="avatar", propertyName="supportedModules", multiple=true)
     * @var string $supportedModules
     */
    protected $supportedModules = '';
    
    
    /**
     * @ORM\OneToMany(targetEntity="\MU\ImageModule\Entity\AvatarCategoryEntity", 
     *                mappedBy="entity", cascade={"all"}, 
     *                orphanRemoval=true)
     * @var \MU\ImageModule\Entity\AvatarCategoryEntity
     */
    protected $categories = null;
    
    
    /**
     * AvatarEntity constructor.
     *
     * Will not be called by Doctrine and can therefore be used
     * for own implementation purposes. It is also possible to add
     * arbitrary arguments as with every other class method.
     */
    public function __construct()
    {
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
     * Returns the _upload base path.
     *
     * @return string
     */
    public function get_uploadBasePath()
    {
        return $this->_uploadBasePath;
    }
    
    /**
     * Sets the _upload base path.
     *
     * @param string $_uploadBasePath
     *
     * @return void
     */
    public function set_uploadBasePath($_uploadBasePath)
    {
        if ($this->_uploadBasePath != $_uploadBasePath) {
            $this->_uploadBasePath = isset($_uploadBasePath) ? $_uploadBasePath : '';
        }
    }
    
    /**
     * Returns the _upload base url.
     *
     * @return string
     */
    public function get_uploadBaseUrl()
    {
        return $this->_uploadBaseUrl;
    }
    
    /**
     * Sets the _upload base url.
     *
     * @param string $_uploadBaseUrl
     *
     * @return void
     */
    public function set_uploadBaseUrl($_uploadBaseUrl)
    {
        if ($this->_uploadBaseUrl != $_uploadBaseUrl) {
            $this->_uploadBaseUrl = isset($_uploadBaseUrl) ? $_uploadBaseUrl : '';
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
            $this->description = $description;
        }
    }
    
    /**
     * Returns the avatar upload.
     *
     * @return File
     */
    public function getAvatarUpload()
    {
        if (null !== $this->avatarUpload) {
            return $this->avatarUpload;
        }
    
        $fileName = $this->avatarUploadFileName;
        if (!empty($fileName) && !$this->_uploadBasePath) {
            throw new \RuntimeException('Invalid upload base path in ' . get_class($this) . '#getAvatarUpload().');
        }
    
        $filePath = $this->_uploadBasePath . 'avatarupload/' . $fileName;
        if (!empty($fileName) && file_exists($filePath)) {
            $this->avatarUpload = new File($filePath);
            $this->setAvatarUploadUrl($this->_uploadBaseUrl . '/' . $filePath);
        } else {
            $this->setAvatarUploadFileName('');
            $this->setAvatarUploadUrl('');
            $this->setAvatarUploadMeta([]);
        }
    
        return $this->avatarUpload;
    }
    
    /**
     * Sets the avatar upload.
     *
     * @param File $avatarUpload
     *
     * @return void
     */
    public function setAvatarUpload(File $avatarUpload)
    {
        if (null === $this->avatarUpload && null === $avatarUpload) {
            return;
        }
        if (null !== $this->avatarUpload && null !== $avatarUpload && $this->avatarUpload->getRealPath() === $avatarUpload->getRealPath()) {
            return;
        }
        $this->avatarUpload = isset($avatarUpload) ? $avatarUpload : '';
    
        if (null === $this->avatarUpload) {
            $this->setAvatarUploadFileName('');
            $this->setAvatarUploadUrl('');
            $this->setAvatarUploadMeta([]);
        } else {
            $this->setAvatarUploadFileName($this->avatarUpload->getFilename());
        }
    }
    
    /**
     * Returns the avatar upload file name.
     *
     * @return string
     */
    public function getAvatarUploadFileName()
    {
        return $this->avatarUploadFileName;
    }
    
    /**
     * Sets the avatar upload file name.
     *
     * @param string $avatarUploadFileName
     *
     * @return void
     */
    public function setAvatarUploadFileName($avatarUploadFileName)
    {
        if ($this->avatarUploadFileName !== $avatarUploadFileName) {
            $this->avatarUploadFileName = isset($avatarUploadFileName) ? $avatarUploadFileName : '';
        }
    }
    
    /**
     * Returns the avatar upload url.
     *
     * @return string
     */
    public function getAvatarUploadUrl()
    {
        return $this->avatarUploadUrl;
    }
    
    /**
     * Sets the avatar upload url.
     *
     * @param string $avatarUploadUrl
     *
     * @return void
     */
    public function setAvatarUploadUrl($avatarUploadUrl)
    {
        if ($this->avatarUploadUrl !== $avatarUploadUrl) {
            $this->avatarUploadUrl = isset($avatarUploadUrl) ? $avatarUploadUrl : '';
        }
    }
    
    /**
     * Returns the avatar upload meta.
     *
     * @return array
     */
    public function getAvatarUploadMeta()
    {
        return $this->avatarUploadMeta;
    }
    
    /**
     * Sets the avatar upload meta.
     *
     * @param array $avatarUploadMeta
     *
     * @return void
     */
    public function setAvatarUploadMeta($avatarUploadMeta = [])
    {
        if ($this->avatarUploadMeta !== $avatarUploadMeta) {
            $this->avatarUploadMeta = isset($avatarUploadMeta) ? $avatarUploadMeta : '';
        }
    }
    
    /**
     * Returns the supported modules.
     *
     * @return string
     */
    public function getSupportedModules()
    {
        return $this->supportedModules;
    }
    
    /**
     * Sets the supported modules.
     *
     * @param string $supportedModules
     *
     * @return void
     */
    public function setSupportedModules($supportedModules)
    {
        if ($this->supportedModules !== $supportedModules) {
            $this->supportedModules = isset($supportedModules) ? $supportedModules : '';
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
     * @param \MU\ImageModule\Entity\AvatarCategoryEntity $element Element to search for
     *
     * @return bool|int
     */
    private function collectionContains(ArrayCollection $collection, \MU\ImageModule\Entity\AvatarCategoryEntity $element)
    {
        foreach ($collection as $key => $category) {
            /** @var \MU\ImageModule\Entity\AvatarCategoryEntity $category */
            if ($category->getCategoryRegistryId() == $element->getCategoryRegistryId()
                && $category->getCategory() == $element->getCategory()
            ) {
                return $key;
            }
        }
    
        return false;
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
        return false;
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
        return 'Avatar ' . $this->getKey() . ': ' . $this->getTitle();
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
    
        // reset upload fields
        $this->setAvatarUpload(null);
    
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
