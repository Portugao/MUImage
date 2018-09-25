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

namespace MU\ImageModule\Container\Base;

use Symfony\Component\Routing\RouterInterface;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Common\Translator\TranslatorTrait;
use Zikula\Core\Doctrine\EntityAccess;
use Zikula\Core\LinkContainer\LinkContainerInterface;
use Zikula\ExtensionsModule\Api\ApiInterface\VariableApiInterface;
use MU\ImageModule\Helper\ControllerHelper;
use MU\ImageModule\Helper\PermissionHelper;

/**
 * This is the link container service implementation class.
 */
abstract class AbstractLinkContainer implements LinkContainerInterface
{
    use TranslatorTrait;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var VariableApiInterface
     */
    protected $variableApi;

    /**
     * @var ControllerHelper
     */
    protected $controllerHelper;

    /**
     * @var PermissionHelper
     */
    protected $permissionHelper;

    /**
     * LinkContainer constructor.
     *
     * @param TranslatorInterface  $translator       Translator service instance
     * @param Routerinterface      $router           Router service instance
     * @param VariableApiInterface $variableApi      VariableApi service instance
     * @param ControllerHelper     $controllerHelper ControllerHelper service instance
     * @param PermissionHelper     $permissionHelper PermissionHelper service instance
     */
    public function __construct(
        TranslatorInterface $translator,
        RouterInterface $router,
        VariableApiInterface $variableApi,
        ControllerHelper $controllerHelper,
        PermissionHelper $permissionHelper
    ) {
        $this->setTranslator($translator);
        $this->router = $router;
        $this->variableApi = $variableApi;
        $this->controllerHelper = $controllerHelper;
        $this->permissionHelper = $permissionHelper;
    }

    /**
     * Sets the translator.
     *
     * @param TranslatorInterface $translator Translator service instance
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Returns available header links.
     *
     * @param string $type The type to collect links for
     *
     * @return array List of header links
     */
    public function getLinks($type = LinkContainerInterface::TYPE_ADMIN)
    {
        $contextArgs = ['api' => 'linkContainer', 'action' => 'getLinks'];
        $allowedObjectTypes = $this->controllerHelper->getObjectTypes('api', $contextArgs);

        $permLevel = LinkContainerInterface::TYPE_ADMIN == $type ? ACCESS_ADMIN : ACCESS_READ;

        // Create an array of links to return
        $links = [];

        if (LinkContainerInterface::TYPE_ACCOUNT == $type) {
            if (!$this->permissionHelper->hasPermission(ACCESS_OVERVIEW)) {
                return $links;
            }

            if (true === $this->variableApi->get('MUImageModule', 'linkOwnAlbumsOnAccountPage', true)) {
                $objectType = 'album';
                if ($this->permissionHelper->hasComponentPermission($objectType, ACCESS_READ)) {
                    $links[] = [
                        'url' => $this->router->generate('muimagemodule_' . strtolower($objectType) . '_view', ['own' => 1]),
                        'text' => $this->__('My albums', 'muimagemodule'),
                        'icon' => 'list-alt'
                    ];
                }
            }

            if (true === $this->variableApi->get('MUImageModule', 'linkOwnPicturesOnAccountPage', true)) {
                $objectType = 'picture';
                if ($this->permissionHelper->hasComponentPermission($objectType, ACCESS_READ)) {
                    $links[] = [
                        'url' => $this->router->generate('muimagemodule_' . strtolower($objectType) . '_view', ['own' => 1]),
                        'text' => $this->__('My pictures', 'muimagemodule'),
                        'icon' => 'list-alt'
                    ];
                }
            }

            if (true === $this->variableApi->get('MUImageModule', 'linkOwnAvatarsOnAccountPage', true)) {
                $objectType = 'avatar';
                if ($this->permissionHelper->hasComponentPermission($objectType, ACCESS_READ)) {
                    $links[] = [
                        'url' => $this->router->generate('muimagemodule_' . strtolower($objectType) . '_view', ['own' => 1]),
                        'text' => $this->__('My avatars', 'muimagemodule'),
                        'icon' => 'list-alt'
                    ];
                }
            }

            if ($this->permissionHelper->hasPermission(ACCESS_ADMIN)) {
                $links[] = [
                    'url' => $this->router->generate('muimagemodule_album_adminindex'),
                    'text' => $this->__('Image Backend', 'muimagemodule'),
                    'icon' => 'wrench'
                ];
            }


            return $links;
        }

        $routeArea = LinkContainerInterface::TYPE_ADMIN == $type ? 'admin' : '';
        if (LinkContainerInterface::TYPE_ADMIN == $type) {
            if ($this->permissionHelper->hasPermission(ACCESS_READ)) {
                $links[] = [
                    'url' => $this->router->generate('muimagemodule_album_index'),
                    'text' => $this->__('Frontend', 'muimagemodule'),
                    'title' => $this->__('Switch to user area.', 'muimagemodule'),
                    'icon' => 'home'
                ];
            }
        } else {
            if ($this->permissionHelper->hasPermission(ACCESS_ADMIN)) {
                $links[] = [
                    'url' => $this->router->generate('muimagemodule_album_adminindex'),
                    'text' => $this->__('Backend', 'muimagemodule'),
                    'title' => $this->__('Switch to administration area.', 'muimagemodule'),
                    'icon' => 'wrench'
                ];
            }
        }
        
        if (in_array('album', $allowedObjectTypes)
            && $this->permissionHelper->hasComponentPermission('album', $permLevel)) {
            $links[] = [
                'url' => $this->router->generate('muimagemodule_album_' . $routeArea . 'view'),
                'text' => $this->__('Albums', 'muimagemodule'),
                'title' => $this->__('Albums list', 'muimagemodule')
            ];
        }
        if (in_array('picture', $allowedObjectTypes)
            && $this->permissionHelper->hasComponentPermission('picture', $permLevel)) {
            $links[] = [
                'url' => $this->router->generate('muimagemodule_picture_' . $routeArea . 'view'),
                'text' => $this->__('Pictures', 'muimagemodule'),
                'title' => $this->__('Pictures list', 'muimagemodule')
            ];
        }
        if (in_array('avatar', $allowedObjectTypes)
            && $this->permissionHelper->hasComponentPermission('avatar', $permLevel)) {
            $links[] = [
                'url' => $this->router->generate('muimagemodule_avatar_' . $routeArea . 'view'),
                'text' => $this->__('Avatars', 'muimagemodule'),
                'title' => $this->__('Avatars list', 'muimagemodule')
            ];
        }
        if ($routeArea == 'admin' && $this->permissionHelper->hasPermission(ACCESS_ADMIN)) {
            $links[] = [
                'url' => $this->router->generate('muimagemodule_config_config'),
                'text' => $this->__('Settings', 'muimagemodule'),
                'title' => $this->__('Manage settings for this application', 'muimagemodule'),
                'icon' => 'wrench'
            ];
        }

        return $links;
    }

    /**
     * Returns the name of the providing bundle.
     *
     * @return string The bundle name
     */
    public function getBundleName()
    {
        return 'MUImageModule';
    }
}
