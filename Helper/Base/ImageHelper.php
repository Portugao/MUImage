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

namespace MU\MUImageModule\Helper\Base;

use SystemPlugin_Imagine_Preset;

/**
 * Utility base class for image helper methods.
 */
class ImageHelper
{
    /**
     * Name of the application.
     *
     * @var string
     */
    protected $name;

    /**
     * Constructor.
     * Initialises member vars.
     */
    public function __construct()
    {
        $this->name = 'MUMUImageModule';
    }

    /**
     * This method returns an Imagine preset for the given arguments.
     *
     * @param string $objectType Currently treated entity type
     * @param string $fieldName  Name of upload field
     * @param string $context    Usage context (allowed values: controllerAction, api, actionHandler, block, contentType)
     * @param array  $args       Additional arguments
     *
     * @return SystemPlugin_Imagine_Preset The selected preset
     */
    public function getPreset($objectType = '', $fieldName = '', $context = '', $args = [])
    {
        if (!in_array($context, ['controllerAction', 'api', 'actionHandler', 'block', 'contentType'])) {
            $context = 'controllerAction';
        }
    
        $presetName = '';
        if ($context == 'controllerAction') {
            if (!isset($args['controller'])) {
                $args['controller'] = 'user';
            }
            if (!isset($args['action'])) {
                $args['action'] = 'index';
            }
    
            if ($args['controller'] == 'ajax' && $args['action'] == 'getItemListAutoCompletion') {
                $presetName = $this->name . '_ajax_autocomplete';
            } else {
                $presetName = $this->name . '_' . $args['controller'] . '_' . $args['action'];
            }
        }
        if (empty($presetName)) {
            $presetName = $this->name . '_default';
        }
    
        $preset = $this->getCustomPreset($objectType, $fieldName, $presetName, $context, $args);
    
        return $preset;
    }

    /**
     * This method returns an Imagine preset for the given arguments.
     *
     * @param string $objectType Currently treated entity type
     * @param string $fieldName  Name of upload field
     * @param string $presetName Name of desired preset
     * @param string $context    Usage context (allowed values: controllerAction, api, actionHandler, block, contentType)
     * @param array  $args       Additional arguments
     *
     * @return SystemPlugin_Imagine_Preset The selected preset
     */
    public function getCustomPreset($objectType = '', $fieldName = '', $presetName = '', $context = '', $args = [])
    {
        $presetData = [
            'width'     => 100,      // thumbnail width in pixels
            'height'    => 100,      // thumbnail height in pixels
            'mode'      => 'inset',  // inset or outbound
            'extension' => null      // file extension for thumbnails (jpg, png, gif; null for original file type)
        ];
    
        if ($presetName == $this->name . '_ajax_autocomplete') {
            $presetData['width'] = 100;
            $presetData['height'] = 80;
        } elseif ($presetName == $this->name . '_relateditem') {
            $presetData['width'] = 50;
            $presetData['height'] = 40;
        } elseif ($context == 'controllerAction') {
            if ($args['action'] == 'view') {
                $presetData['width'] = 32;
                $presetData['height'] = 20;
            } elseif ($args['action'] == 'display') {
                $presetData['width'] = 250;
                $presetData['height'] = 150;
            }
        }
    
        $preset = new SystemPlugin_Imagine_Preset($presetName, $presetData);
    
        return $preset;
    }
}
