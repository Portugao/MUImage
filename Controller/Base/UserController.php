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

namespace MU\MUImageModule\Controller\Base;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use ModUtil;
use RuntimeException;
use System;
use ZLanguage;
use Zikula\Core\Controller\AbstractController;
use Zikula\Core\RouteUrl;
use Zikula\Core\Response\PlainResponse;

/**
 * User controller class.
 */
class UserController extends AbstractController
{

    /**
     * This is the default action handling the main area called without defining arguments.
     *
     * @param Request  $request      Current request instance
     *
     * @return mixed Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    public function indexAction(Request $request)
    {
        // parameter specifying which type of objects we are treating
        $objectType = $request->query->getAlnum('ot', 'album');
        
        $permLevel = ACCESS_OVERVIEW;
        if (!$this->hasPermission($this->name . '::', '::', $permLevel)) {
            throw new AccessDeniedException();
        }
        
        // redirect to view action
        $routeArea = '';
        
        return $this->redirectToRoute('mumuimagemodule_' . strtolower($objectType) . '_' . $routeArea . 'view');
    }

    /**
     * This is a custom action.
     *
     * @param Request  $request      Current request instance
     *
     * @return mixed Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    public function zipUploadAction(Request $request)
    {
        $controllerHelper = $this->get('mu_muimage_module.controller_helper');
        
        // parameter specifying which type of objects we are treating
        $objectType = $request->query->getAlnum('ot', 'album');
        $utilArgs = ['controller' => 'user', 'action' => 'zipUpload'];
        if (!in_array($objectType, $controllerHelper->getObjectTypes('controllerAction', $utilArgs))) {
            $objectType = $controllerHelper->getDefaultObjectType('controllerAction', $utilArgs);
        }
        $permLevel = ACCESS_OVERVIEW;
        if (!$this->hasPermission($this->name . ':' . ucfirst($objectType) . ':', '::', $permLevel)) {
            throw new AccessDeniedException();
        }
        /** TODO: custom logic */
        
        // return template
        return $this->render('@MUMUImageModule/User/zipUpload.html.twig');
    }

    /**
     * This is a custom action.
     *
     * @param Request  $request      Current request instance
     *
     * @return mixed Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    public function multiUploadAction(Request $request)
    {
        $controllerHelper = $this->get('mu_muimage_module.controller_helper');
        
        // parameter specifying which type of objects we are treating
        $objectType = $request->query->getAlnum('ot', 'album');
        $utilArgs = ['controller' => 'user', 'action' => 'multiUpload'];
        if (!in_array($objectType, $controllerHelper->getObjectTypes('controllerAction', $utilArgs))) {
            $objectType = $controllerHelper->getDefaultObjectType('controllerAction', $utilArgs);
        }
        $permLevel = ACCESS_OVERVIEW;
        if (!$this->hasPermission($this->name . ':' . ucfirst($objectType) . ':', '::', $permLevel)) {
            throw new AccessDeniedException();
        }
        /** TODO: custom logic */
        
        // return template
        return $this->render('@MUMUImageModule/User/multiUpload.html.twig');
    }

    /**
     * This is a custom action.
     *
     * @param Request  $request      Current request instance
     *
     * @return mixed Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    public function externalAction(Request $request)
    {
        $controllerHelper = $this->get('mu_muimage_module.controller_helper');
        
        // parameter specifying which type of objects we are treating
        $objectType = $request->query->getAlnum('ot', 'album');
        $utilArgs = ['controller' => 'user', 'action' => 'external'];
        if (!in_array($objectType, $controllerHelper->getObjectTypes('controllerAction', $utilArgs))) {
            $objectType = $controllerHelper->getDefaultObjectType('controllerAction', $utilArgs);
        }
        $permLevel = ACCESS_OVERVIEW;
        if (!$this->hasPermission($this->name . ':' . ucfirst($objectType) . ':', '::', $permLevel)) {
            throw new AccessDeniedException();
        }
        /** TODO: custom logic */
        
        // return template
        return $this->render('@MUMUImageModule/User/external.html.twig');
    }

    /**
     * This is a custom action.
     *
     * @param Request  $request      Current request instance
     *
     * @return mixed Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    public function editMultiAction(Request $request)
    {
        $controllerHelper = $this->get('mu_muimage_module.controller_helper');
        
        // parameter specifying which type of objects we are treating
        $objectType = $request->query->getAlnum('ot', 'album');
        $utilArgs = ['controller' => 'user', 'action' => 'editMulti'];
        if (!in_array($objectType, $controllerHelper->getObjectTypes('controllerAction', $utilArgs))) {
            $objectType = $controllerHelper->getDefaultObjectType('controllerAction', $utilArgs);
        }
        $permLevel = ACCESS_OVERVIEW;
        if (!$this->hasPermission($this->name . ':' . ucfirst($objectType) . ':', '::', $permLevel)) {
            throw new AccessDeniedException();
        }
        /** TODO: custom logic */
        
        // return template
        return $this->render('@MUMUImageModule/User/editMulti.html.twig');
    }

}
