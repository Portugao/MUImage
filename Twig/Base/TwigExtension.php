<?php
/**
 * MUImage.
 *
 * @copyright Michael Ueberschaer (MU)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Michael Ueberschaer <kontakt@webdesign-in-bremen.com>.
 * @link http://www.webdesign-in-bremen.com
 * @link http://zikula.org
 * @version Generated by ModuleStudio (http://modulestudio.de).
 */

namespace MU\MUImageModule\Twig\Base;

use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Common\Translator\TranslatorTrait;
use Zikula\ExtensionsModule\Api\VariableApi;
use MU\MUImageModule\Helper\WorkflowHelper;
use MU\MUImageModule\Helper\ViewHelper;
use MU\MUImageModule\Helper\ListEntriesHelper;

/**
 * Twig extension base class.
 */
class TwigExtension extends \Twig_Extension
{
    use TranslatorTrait;
    
    /**
     * @var VariableApi
     */
    protected $variableApi;
    
    /**
     * @var WorkflowHelper
     */
    protected $workflowHelper;
    
    /**
     * @var ViewHelper
     */
    protected $viewHelper;
    
    /**
     * @var ListEntriesHelper
     */
    protected $listHelper;
    
    /**
     * Constructor.
     * Initialises member vars.
     *
     * @param TranslatorInterface $translator     Translator service instance
     * @param VariableApi         $variableApi    VariableApi service instance
     * @param WorkflowHelper      $workflowHelper WorkflowHelper service instance
     * @param ViewHelper          $viewHelper     ViewHelper service instance
     * @param ListEntriesHelper   $listHelper     ListEntriesHelper service instance
     */
    public function __construct(TranslatorInterface $translator, VariableApi $variableApi, WorkflowHelper $workflowHelper, ViewHelper $viewHelper, ListEntriesHelper $listHelper)
    {
        $this->setTranslator($translator);
        $this->variableApi = $variableApi;
        $this->workflowHelper = $workflowHelper;
        $this->viewHelper = $viewHelper;
        $this->listHelper = $listHelper;
    }
    
    /**
     * Sets the translator.
     *
     * @param TranslatorInterface $translator Translator service instance
     */
    public function setTranslator(/*TranslatorInterface */$translator)
    {
        $this->translator = $translator;
    }
    
    /**
     * Returns a list of custom Twig functions.
     *
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('mumuimagemodule_templateHeaders', [$this, 'templateHeaders']),
            new \Twig_SimpleFunction('mumuimagemodule_objectTypeSelector', [$this, 'getObjectTypeSelector']),
            new \Twig_SimpleFunction('mumuimagemodule_templateSelector', [$this, 'getTemplateSelector']),
            new \Twig_SimpleFunction('mumuimagemodule_userVar', [$this, 'getUserVar']),
            new \Twig_SimpleFunction('mumuimagemodule_userAvatar', [$this, 'getUserAvatar']),
            new \Twig_SimpleFunction('mumuimagemodule_thumb', [$this, 'getImageThumb'])
        ];
    }
    
    /**
     * Returns a list of custom Twig filters.
     *
     * @return array
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('mumuimagemodule_actionUrl', [$this, 'buildActionUrl']),
            new \Twig_SimpleFilter('mumuimagemodule_objectState', [$this, 'getObjectState']),
            new \Twig_SimpleFilter('mumuimagemodule_fileSize', [$this, 'getFileSize']),
            new \Twig_SimpleFilter('mumuimagemodule_listEntry', [$this, 'getListEntry']),
            new \Twig_SimpleFilter('mumuimagemodule_profileLink', [$this, 'profileLink'])
        ];
    }
    
    /**
     * The mumuimagemodule_actionUrl filter creates the URL for a given action.
     *
     * @param string $urlType      The url type (admin, user, etc.)
     * @param string $urlFunc      The url func (view, display, edit, etc.)
     * @param array  $urlArguments The argument array containing ids and other additional parameters
     *
     * @return string Desired url in encoded form
     */
    public function buildActionUrl($urlType, $urlFunc, $urlArguments)
    {
        return \DataUtil::formatForDisplay(\ModUtil::url('MUMUImageModule', $urlType, $urlFunc, $urlArguments));
    }
    
    
    /**
     * The mumuimagemodule_objectState filter displays the name of a given object's workflow state.
     * Examples:
     *    {{ item.workflowState|mumuimagemodule_objectState }}        {# with visual feedback #}
     *    {{ item.workflowState|mumuimagemodule_objectState(false) }} {# no ui feedback #}
     *
     * @param string  $state      Name of given workflow state
     * @param boolean $uiFeedback Whether the output should include some visual feedback about the state
     *
     * @return string Enriched and translated workflow state ready for display
     */
    public function getObjectState($state = 'initial', $uiFeedback = true)
    {
        $stateInfo = $this->workflowHelper->getStateInfo($state);
    
        $result = $stateInfo['text'];
        if ($uiFeedback === true) {
            $result = '<span class="label label-' . $stateInfo['ui'] . '">' . $result . '</span>';
        }
    
        return $result;
    }
    
    
    /**
     * The mumuimagemodule_templateHeaders function performs header() operations
     * to change the content type provided to the user agent.
     *
     * Available parameters:
     *   - contentType:  Content type for corresponding http header.
     *   - asAttachment: If set to true the file will be offered for downloading.
     *   - fileName:     Name of download file.
     *
     * @return boolean false
     */
    public function templateHeaders($contentType, $asAttachment = false, $fileName = '')
    {
        // apply header
        header('Content-Type: ' . $contentType);
    
        // if desired let the browser offer the given file as a download
        if ($asAttachment && !empty($fileName)) {
            header('Content-Disposition: attachment; filename=' . $fileName);
        }
    
        return;
    }
    
    
    /**
     * The mumuimagemodule_fileSize filter displays the size of a given file in a readable way.
     * Example:
     *     {{ 12345|mumuimagemodule_fileSize }}
     *
     * @param integer $size     File size in bytes
     * @param string  $filepath The input file path including file name (if file size is not known)
     * @param boolean $nodesc   If set to true the description will not be appended
     * @param boolean $onlydesc If set to true only the description will be returned
     *
     * @return string File size in a readable form
     */
    public function getFileSize($size = 0, $filepath = '', $nodesc = false, $onlydesc = false)
    {
        if (!is_numeric($size)) {
            $size = (int) $size;
        }
        if (!$size) {
            if (empty($filepath) || !file_exists($filepath)) {
                return '';
            }
            $size = filesize($filepath);
        }
        if (!$size) {
            return '';
        }
    
        return $this->viewHelper->getReadableFileSize($size, $nodesc, $onlydesc);
    }
    
    
    /**
     * The mumuimagemodule_listEntry filter displays the name
     * or names for a given list item.
     * Example:
     *     {{ entity.listField|mumuimagemodule_listEntry('entityName', 'fieldName') }}
     *
     * @param string $value      The dropdown value to process
     * @param string $objectType The treated object type
     * @param string $fieldName  The list field's name
     * @param string $delimiter  String used as separator for multiple selections
     *
     * @return string List item name
     */
    public function getListEntry($value, $objectType = '', $fieldName = '', $delimiter = ', ')
    {
        if ((empty($value) && $value != '0') || empty($objectType) || empty($fieldName)) {
            return $value;
        }
    
        return $this->listHelper->resolve($value, $objectType, $fieldName, $delimiter);
    }
    
    
    /**
     * The mumuimagemodule_objectTypeSelector function provides items for a dropdown selector.
     *
     * @return string The output of the plugin
     */
    public function getObjectTypeSelector()
    {
        $result = [];
    
        $result[] = ['text' => $this->__('Albums'), 'value' => 'album'];
        $result[] = ['text' => $this->__('Pictures'), 'value' => 'picture'];
    
        return $result;
    }
    
    
    /**
     * The mumuimagemodule_templateSelector function provides items for a dropdown selector.
     *
     * @return string The output of the plugin
     */
    public function getTemplateSelector()
    {
        $result = [];
    
        $result[] = ['text' => $this->__('Only item titles'), 'value' => 'itemlist_display.html.twig'];
        $result[] = ['text' => $this->__('With description'), 'value' => 'itemlist_display_description.html.twig'];
        $result[] = ['text' => $this->__('Custom template'), 'value' => 'custom'];
    
        return $result;
    }
    
    /**
     * Returns the value of a user variable.
     *
     * @param string     $name    Name of desired property
     * @param int        $uid     The user's id
     * @param string|int $default The default value
     *
     * @return string
     */
    public function getUserVar($name, $uid = -1, $default = '')
    {
        if (!$uid) {
            $uid = -1;
        }
    
        $result = \UserUtil::getVar($name, $uid, $default);
    
        return $result;
    }
    
    /**
     * Display the avatar of a user.
     *
     * @param int    $uid    The user's id
     * @param int    $width  Image width (optional)
     * @param int    $height Image height (optional)
     * @param int    $size   Gravatar size (optional)
     * @param string $rating Gravatar self-rating [g|pg|r|x] see: http://en.gravatar.com/site/implement/images/ (optional)
     *
     * @return string
     */
    public function getUserAvatar($uid, $width = 0, $height = 0, $size = 0, $rating = '')
    {
        $params = ['uid' => $uid];
        if ($width > 0) {
            $params['width'] = $width;
        }
        if ($height > 0) {
            $params['height'] = $height;
        }
        if ($size > 0) {
            $params['size'] = $size;
        }
        if ($rating != '') {
            $params['rating'] = $rating;
        }
    
        include_once 'lib/legacy/viewplugins/function.useravatar.php';
    
        $view = \Zikula_View::getInstance('MUMUImageModule');
        $result = smarty_function_useravatar($params, $view);
    
        return $result;
    }
    
    /**
     * Display an image thumbnail using Imagine system plugin.
     *
     * @param array $params Parameters assigned to bridged Smarty plugin
     *
     * @return string Thumb path
     */
    public function getImageThumb($params)
    {
        include_once 'plugins/Imagine/templates/plugins/function.thumb.php';
    
        $view = \Zikula_View::getInstance('MUMUImageModule');
        $result = smarty_function_thumb($params, $view);
    
        return $result;
    }
    
    /**
     * Returns a link to the user's profile.
     *
     * @param int     $uid       The user's id (optional)
     * @param string  $class     The class name for the link (optional)
     * @param integer $maxLength If set then user names are truncated to x chars
     *
     * @return string
     */
    public function profileLink($uid, $class = '', $maxLength = 0)
    {
        $result = '';
        $image = '';
    
        if ($uid == '') {
            return $result;
        }
    
        if ($this->variableApi->get(VariableApi::CONFIG, 'profilemodule') != '') {
            include_once 'lib/legacy/viewplugins/modifier.profilelinkbyuid.php';
            $result = smarty_modifier_profilelinkbyuid($uid, $class, $image, $maxLength);
        } else {
            $result = \UserUtil::getVar('uname', $uid);
        }
    
        return $result;
    }
    
    /**
     * Returns internal name of this extension.
     *
     * @return string
     */
    public function getName()
    {
        return 'mumuimagemodule_twigextension';
    }
}
