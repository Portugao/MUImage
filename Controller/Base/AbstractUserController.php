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

namespace MU\ImageModule\Controller\Base;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use ModUtil;
use RuntimeException;
use System;
use Zikula\Core\Controller\AbstractController;
use Zikula\Core\RouteUrl;
use Zikula\Core\Response\PlainResponse;
use MU\ImageModule\Helper\FeatureActivationHelper;

/**
 * User controller class.
 */
abstract class AbstractUserController extends AbstractController
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
        
        return $this->redirectToRoute('muimagemodule_' . strtolower($objectType) . '_' . $routeArea . 'view');
    }

}
