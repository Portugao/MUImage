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

namespace MU\ImageModule\Base;

use Doctrine\DBAL\Connection;
use RuntimeException;
use Zikula\Core\AbstractExtensionInstaller;
use Zikula\CategoriesModule\Entity\CategoryRegistryEntity;

/**
 * Installer base class.
 */
abstract class AbstractImageModuleInstaller extends AbstractExtensionInstaller
{
    /**
     * Install the MUImageModule application.
     *
     * @return boolean True on success, or false
     *
     * @throws RuntimeException Thrown if database tables can not be created or another error occurs
     */
    public function install()
    {
        $logger = $this->container->get('logger');
        $userName = $this->container->get('zikula_users_module.current_user')->get('uname');
    
        // Check if upload directories exist and if needed create them
        try {
            $container = $this->container;
            $uploadHelper = new \MU\ImageModule\Helper\UploadHelper(
                $container->get('translator.default'),
                $container->get('filesystem'),
                $container->get('session'),
                $container->get('logger'),
                $container->get('zikula_users_module.current_user'),
                $container->get('zikula_extensions_module.api.variable'),
                $container->getParameter('datadir')
            );
            $uploadHelper->checkAndCreateAllUploadFolders();
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            $logger->error('{app}: User {user} could not create upload folders during installation. Error details: {errorMessage}.', ['app' => 'MUImageModule', 'user' => $userName, 'errorMessage' => $exception->getMessage()]);
        
            return false;
        }
        // create all tables from according entity definitions
        try {
            $this->schemaTool->create($this->listEntityClasses());
        } catch (\Exception $exception) {
            $this->addFlash('error', $this->__('Doctrine Exception') . ': ' . $exception->getMessage());
            $logger->error('{app}: Could not create the database tables during installation. Error details: {errorMessage}.', ['app' => 'MUImageModule', 'errorMessage' => $exception->getMessage()]);
    
            return false;
        }
    
        // set up all our vars with initial values
        $this->setVar('supportCategoriesForAlbums', false);
        $this->setVar('supportCategoriesForAvatars', false);
        $this->setVar('supportSubAlbums', false);
        $this->setVar('userDeletePictures', false);
        $this->setVar('slideshow1', false);
        $this->setVar('useAvatars', false);
        $this->setVar('useWatermark', false);
        $this->setVar('useExtendedFeatures', false);
        $this->setVar('slide1Interval', 4000);
        $this->setVar('slide1Speed', 1000);
        $this->setVar('numberParentAlbums', 1);
        $this->setVar('numberSubAlbums', 2);
        $this->setVar('numberPictures', 20);
        $this->setVar('fileSizeForPictures', '200k');
        $this->setVar('fileSizeForAvatars', '200k');
        $this->setVar('fileSizesForZip', '2M');
        $this->setVar('minWidthForPictures', 400);
        $this->setVar('maxWidthForPictures', 0);
        $this->setVar('maxHeightForPictures', 0);
        $this->setVar('minWidthForAvatars', 0);
        $this->setVar('maxWidthForAvatars', 0);
        $this->setVar('maxHeightForAvatars', 0);
        $this->setVar('createSeveralPictures', false);
        $this->setVar('firstWidth', 0);
        $this->setVar('firstHeight', 0);
        $this->setVar('secondWidth', 0);
        $this->setVar('secondHeight', 0);
        $this->setVar('thirdWidth', 0);
        $this->setVar('thirdHeight', 0);
        $this->setVar('numberWidthAndNameOfAvatars', '200,thumb;400,view;600,normal');
        $this->setVar('shrink', false);
        $this->setVar('albumEntriesPerPageInBackend', 10);
        $this->setVar('pictureEntriesPerPageInBackend', 10);
        $this->setVar('avatarEntriesPerPageInBackend', 10);
        $this->setVar('countImageView', false);
        $this->setVar('groupForCommonAlbums', 'None');
        $this->setVar('kindOfShowSubAlbums', 'Panel');
        $this->setVar('breadcrumbsInFrontend', false);
        $this->setVar('endingOfUrl', 'html');
        $this->setVar('watermark', '');
        $this->setVar('bottomOfImage', 0);
        $this->setVar('leftSide', 0);
        $this->setVar('rightSide', 0);
        $this->setVar('topOfImage', 0);
        $this->setVar('albumEntriesPerPage', 10);
        $this->setVar('linkOwnAlbumsOnAccountPage', true);
        $this->setVar('pictureEntriesPerPage', 10);
        $this->setVar('linkOwnPicturesOnAccountPage', true);
        $this->setVar('avatarEntriesPerPage', 10);
        $this->setVar('linkOwnAvatarsOnAccountPage', true);
        $this->setVar('showOnlyOwnEntries', false);
        $this->setVar('enableShrinkingForPictureImageUpload', false);
        $this->setVar('shrinkWidthPictureImageUpload', 800);
        $this->setVar('shrinkHeightPictureImageUpload', 600);
        $this->setVar('thumbnailModePictureImageUpload', 'inset');
        $this->setVar('thumbnailWidthPictureImageUploadView', 32);
        $this->setVar('thumbnailHeightPictureImageUploadView', 24);
        $this->setVar('thumbnailWidthPictureImageUploadDisplay', 240);
        $this->setVar('thumbnailHeightPictureImageUploadDisplay', 180);
        $this->setVar('thumbnailWidthPictureImageUploadEdit', 240);
        $this->setVar('thumbnailHeightPictureImageUploadEdit', 180);
        $this->setVar('enableShrinkingForAvatarAvatarUpload', false);
        $this->setVar('shrinkWidthAvatarAvatarUpload', 800);
        $this->setVar('shrinkHeightAvatarAvatarUpload', 600);
        $this->setVar('thumbnailModeAvatarAvatarUpload', 'inset');
        $this->setVar('thumbnailWidthAvatarAvatarUploadView', 32);
        $this->setVar('thumbnailHeightAvatarAvatarUploadView', 24);
        $this->setVar('thumbnailWidthAvatarAvatarUploadDisplay', 240);
        $this->setVar('thumbnailHeightAvatarAvatarUploadDisplay', 180);
        $this->setVar('thumbnailWidthAvatarAvatarUploadEdit', 240);
        $this->setVar('thumbnailHeightAvatarAvatarUploadEdit', 180);
        $this->setVar('allowModerationSpecificCreatorForAlbum', false);
        $this->setVar('allowModerationSpecificCreationDateForAlbum', false);
        $this->setVar('allowModerationSpecificCreatorForPicture', false);
        $this->setVar('allowModerationSpecificCreationDateForPicture', false);
        $this->setVar('allowModerationSpecificCreatorForAvatar', false);
        $this->setVar('allowModerationSpecificCreationDateForAvatar', false);
        $this->setVar('enabledFinderTypes', 'album###picture###avatar');
    
        // add default entry for category registry (property named Main)
        $categoryHelper = new \MU\ImageModule\Helper\CategoryHelper(
            $this->container->get('translator.default'),
            $this->container->get('request_stack'),
            $logger,
            $this->container->get('zikula_users_module.current_user'),
            $this->container->get('zikula_categories_module.category_registry_repository'),
            $this->container->get('zikula_categories_module.api.category_permission')
        );
        $categoryGlobal = $this->container->get('zikula_categories_module.category_repository')->findOneBy(['name' => 'Global']);
        if ($categoryGlobal) {
            $categoryRegistryIdsPerEntity = [];
            $entityManager = $this->container->get('doctrine.orm.default_entity_manager');
    
            $registry = new CategoryRegistryEntity();
            $registry->setModname('MUImageModule');
            $registry->setEntityname('AlbumEntity');
            $registry->setProperty($categoryHelper->getPrimaryProperty('Album'));
            $registry->setCategory($categoryGlobal);
    
            try {
                $entityManager->persist($registry);
                $entityManager->flush();
            } catch (\Exception $exception) {
                $this->addFlash('warning', $this->__f('Error! Could not create a category registry for the %entity% entity. If you want to use categorisation, register at least one registry in the Categories administration.', ['%entity%' => 'album']));
                $logger->error('{app}: User {user} could not create a category registry for {entities} during installation. Error details: {errorMessage}.', ['app' => 'MUImageModule', 'user' => $userName, 'entities' => 'albums', 'errorMessage' => $exception->getMessage()]);
            }
            $categoryRegistryIdsPerEntity['album'] = $registry->getId();
    
            $registry = new CategoryRegistryEntity();
            $registry->setModname('MUImageModule');
            $registry->setEntityname('AvatarEntity');
            $registry->setProperty($categoryHelper->getPrimaryProperty('Avatar'));
            $registry->setCategory($categoryGlobal);
    
            try {
                $entityManager->persist($registry);
                $entityManager->flush();
            } catch (\Exception $exception) {
                $this->addFlash('warning', $this->__f('Error! Could not create a category registry for the %entity% entity. If you want to use categorisation, register at least one registry in the Categories administration.', ['%entity%' => 'avatar']));
                $logger->error('{app}: User {user} could not create a category registry for {entities} during installation. Error details: {errorMessage}.', ['app' => 'MUImageModule', 'user' => $userName, 'entities' => 'avatars', 'errorMessage' => $exception->getMessage()]);
            }
            $categoryRegistryIdsPerEntity['avatar'] = $registry->getId();
        }
    
        // initialisation successful
        return true;
    }
    
    /**
     * Upgrade the MUImageModule application from an older version.
     *
     * If the upgrade fails at some point, it returns the last upgraded version.
     *
     * @param integer $oldVersion Version to upgrade from
     *
     * @return boolean True on success, false otherwise
     *
     * @throws RuntimeException Thrown if database tables can not be updated
     */
    public function upgrade($oldVersion)
    {
    /*
        $logger = $this->container->get('logger');
    
        // Upgrade dependent on old version number
        switch ($oldVersion) {
            case '1.0.0':
                // do something
                // ...
                // update the database schema
                try {
                    $this->schemaTool->update($this->listEntityClasses());
                } catch (\Exception $exception) {
                    $this->addFlash('error', $this->__('Doctrine Exception') . ': ' . $exception->getMessage());
                    $logger->error('{app}: Could not update the database tables during the upgrade. Error details: {errorMessage}.', ['app' => 'MUImageModule', 'errorMessage' => $exception->getMessage()]);
    
                    return false;
                }
        }
    */
    
        // update successful
        return true;
    }
    
    /**
     * Uninstall MUImageModule.
     *
     * @return boolean True on success, false otherwise
     *
     * @throws RuntimeException Thrown if database tables or stored workflows can not be removed
     */
    public function uninstall()
    {
        $logger = $this->container->get('logger');
    
        try {
            $this->schemaTool->drop($this->listEntityClasses());
        } catch (\Exception $exception) {
            $this->addFlash('error', $this->__('Doctrine Exception') . ': ' . $exception->getMessage());
            $logger->error('{app}: Could not remove the database tables during uninstallation. Error details: {errorMessage}.', ['app' => 'MUImageModule', 'errorMessage' => $exception->getMessage()]);
    
            return false;
        }
    
        // remove all module vars
        $this->delVars();
    
        // remove category registry entries
        $entityManager = $this->container->get('doctrine.orm.default_entity_manager');
        $registries = $this->container->get('zikula_categories_module.category_registry_repository')->findBy(['modname' => 'MUImageModule']);
        foreach ($registries as $registry) {
            $entityManager->remove($registry);
        }
        $entityManager->flush();
    
        // remind user about upload folders not being deleted
        $uploadPath = $this->container->getParameter('datadir') . '/MUImageModule/';
        $this->addFlash('status', $this->__f('The upload directories at "%path%" can be removed manually.', ['%path%' => $uploadPath]));
    
        // uninstallation successful
        return true;
    }
    
    /**
     * Build array with all entity classes for MUImageModule.
     *
     * @return string[] List of class names
     */
    protected function listEntityClasses()
    {
        $classNames = [];
        $classNames[] = 'MU\ImageModule\Entity\AlbumEntity';
        $classNames[] = 'MU\ImageModule\Entity\AlbumTranslationEntity';
        $classNames[] = 'MU\ImageModule\Entity\AlbumCategoryEntity';
        $classNames[] = 'MU\ImageModule\Entity\PictureEntity';
        $classNames[] = 'MU\ImageModule\Entity\PictureTranslationEntity';
        $classNames[] = 'MU\ImageModule\Entity\AvatarEntity';
        $classNames[] = 'MU\ImageModule\Entity\AvatarCategoryEntity';
    
        return $classNames;
    }
}
