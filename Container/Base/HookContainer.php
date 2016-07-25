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

namespace MU\MUImageModule\Container\Base;

use Zikula\Bundle\HookBundle\AbstractHookContainer;
use Zikula\Bundle\HookBundle\Bundle\SubscriberBundle;

/**
 * Base class for hook container methods.
 */
class HookContainer extends AbstractHookContainer
{
    /**
     * Define the hook bundles supported by this module.
     *
     * @return void
     */
    protected function setupHookBundles()
    {
        $bundle = new SubscriberBundle('MUMUImageModule', 'subscriber.mumuimagemodule..ui_hooks.albums', 'ui_hooks', $this->__('mumuimagemodule. Albums Display Hooks'));
        
        // Display hook for view/display templates.
        $bundle->addEvent('display_view', 'mumuimagemodule.ui_hooks.albums.display_view');
        // Display hook for create/edit forms.
        $bundle->addEvent('form_edit', 'mumuimagemodule.ui_hooks.albums.form_edit');
        // Display hook for delete dialogues.
        $bundle->addEvent('form_delete', 'mumuimagemodule.ui_hooks.albums.form_delete');
        // Validate input from an ui create/edit form.
        $bundle->addEvent('validate_edit', 'mumuimagemodule.ui_hooks.albums.validate_edit');
        // Validate input from an ui delete form.
        $bundle->addEvent('validate_delete', 'mumuimagemodule.ui_hooks.albums.validate_delete');
        // Perform the final update actions for a ui create/edit form.
        $bundle->addEvent('process_edit', 'mumuimagemodule.ui_hooks.albums.process_edit');
        // Perform the final delete actions for a ui form.
        $bundle->addEvent('process_delete', 'mumuimagemodule.ui_hooks.albums.process_delete');
        $this->registerHookSubscriberBundle($bundle);
        
        $bundle = new SubscriberBundle('MUMUImageModule', 'subscriber.mumuimagemodule..filter_hooks.albums', 'filter_hooks', $this->__('mumuimagemodule. Albums Filter Hooks'));
        // A filter applied to the given area.
        $bundle->addEvent('filter', 'mumuimagemodule.filter_hooks.albums.filter');
        $this->registerHookSubscriberBundle($bundle);
        
        $bundle = new SubscriberBundle('MUMUImageModule', 'subscriber.mumuimagemodule..ui_hooks.pictures', 'ui_hooks', $this->__('mumuimagemodule. Pictures Display Hooks'));
        
        // Display hook for view/display templates.
        $bundle->addEvent('display_view', 'mumuimagemodule.ui_hooks.pictures.display_view');
        // Display hook for create/edit forms.
        $bundle->addEvent('form_edit', 'mumuimagemodule.ui_hooks.pictures.form_edit');
        // Display hook for delete dialogues.
        $bundle->addEvent('form_delete', 'mumuimagemodule.ui_hooks.pictures.form_delete');
        // Validate input from an ui create/edit form.
        $bundle->addEvent('validate_edit', 'mumuimagemodule.ui_hooks.pictures.validate_edit');
        // Validate input from an ui delete form.
        $bundle->addEvent('validate_delete', 'mumuimagemodule.ui_hooks.pictures.validate_delete');
        // Perform the final update actions for a ui create/edit form.
        $bundle->addEvent('process_edit', 'mumuimagemodule.ui_hooks.pictures.process_edit');
        // Perform the final delete actions for a ui form.
        $bundle->addEvent('process_delete', 'mumuimagemodule.ui_hooks.pictures.process_delete');
        $this->registerHookSubscriberBundle($bundle);
        
        $bundle = new SubscriberBundle('MUMUImageModule', 'subscriber.mumuimagemodule..filter_hooks.pictures', 'filter_hooks', $this->__('mumuimagemodule. Pictures Filter Hooks'));
        // A filter applied to the given area.
        $bundle->addEvent('filter', 'mumuimagemodule.filter_hooks.pictures.filter');
        $this->registerHookSubscriberBundle($bundle);
        
        
        
    }
}
