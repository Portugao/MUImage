<?php
/**
 * MUImage.
 *
 * @copyright Michael Ueberschaer
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @package MUImage
 * @author Michael Ueberschaer <kontakt@webdesign-in-bremen.com>.
 * @link http://www.webdesign-in-bremen.com
 * @link http://zikula.org
 * @version Generated by ModuleStudio 0.5.4 (http://modulestudio.de) at Thu Feb 23 22:43:24 CET 2012.
 */

/**
 * Installer implementation class
 */
class MUImage_Installer extends MUImage_Base_Installer
{
    /**
     * Install the MUImage application.
     *
     * @return boolean True on success, or false.
     */
    public function install()
    {
        // Check if upload directories exist and if needed create them
        try {
            $controllerHelper = new MUImage_Util_Controller($this->serviceManager);
            $controllerHelper->checkAndCreateAllUploadFolders();
        } catch (\Exception $e) {
            return LogUtil::registerError($e->getMessage());
        }
        // create all tables from according entity definitions
        try {
            DoctrineHelper::createSchema($this->entityManager, $this->listEntityClasses());
        } catch (\Exception $e) {
            if (System::isDevelopmentMode()) {
                return LogUtil::registerError($this->__('Doctrine Exception: ') . $e->getMessage());
            }
            $returnMessage = $this->__f('An error was encountered while creating the tables for the %s extension.', array($this->name));
            if (!System::isDevelopmentMode()) {
                $returnMessage .= ' ' . $this->__('Please enable the development mode by editing the /config/config.php file in order to reveal the error details.');
            }
            return LogUtil::registerError($returnMessage);
        }

        // set up all our vars with initial values
        $this->setVar('pagesize', 10);
        $this->setVar('pageSizeAdminAlbums', 10);
        $this->setVar('pageSizeAdminPictures', 10);
        $this->setVar('showTitle', false);
        $this->setVar('showDescription', false);
        $this->setVar('countImageView', false);
        $this->setVar('numberParentAlbums', 1);
        $this->setVar('numberSubAlbums', 2);
        $this->setVar('numberPictures', 20);
        $this->setVar('fileSize', '');
        $this->setVar('zipSize', '');
        $this->setVar('minWidth', 400);
        $this->setVar('maxWidth', 0);
        $this->setVar('maxHeight', 0);
        $this->setVar('layout',  'bootstrap' );
        $this->setVar('groupForCommonAlbums',  'notset' );
        $this->setVar('shrinkPictures', false);
        $this->setVar('supportCategories', false);
        $this->setVar('supportSubAlbums', false);
        $this->setVar('breadcrumbInFrontend', false);
        $this->setVar('kindOfShowSubAlbums', 'panel');
        $this->setVar('orderAlbums', false);
        $this->setVar('ending', 'html');
        $this->setVar('userDeletePictures', false);
        $this->setVar('fileNameForTitle', false);
        $this->setVar('createSeveralPictureSizes', true);
        $this->setVar('widthFirst', 400);
        $this->setVar('heightFirst', 400);
        $this->setVar('widthSecond', 600);
        $this->setVar('heightSecond', 600);
        $this->setVar('widthThird', 800);
        $this->setVar('heightThird', 800);
        $this->setVar('slideshow1', false);
        $this->setVar('slide1Interval', 4000);
        $this->setVar('slide1Speed', 1000);

        $categoryRegistryIdsPerEntity = array();

        // add default entry for category registry (property named Main)
        include_once 'modules/MUImage/lib/MUImage/Api/Base/Category.php';
        include_once 'modules/MUImage/lib/MUImage/Api/Category.php';
        $categoryApi = new MUImage_Api_Category($this->serviceManager);
        $categoryModules = CategoryUtil::getCategoryByPath('/__SYSTEM__/Modules');
        
        CategoryUtil::createCategory($categoryModules, 'MUImage'); 
        $categoryMUImage = CategoryUtil::getCategoryByPath('/__SYSTEM__/Modules/MUImage');

        $registryData = array();
        $registryData['modname'] = $this->name;
        $registryData['table'] = 'Album';
        $registryData['property'] = $categoryApi->getPrimaryProperty(array('ot' => 'Album'));
        $registryData['category_id'] = $categoryMUImage['id'];
        $registryData['id'] = false;
        if (!DBUtil::insertObject($registryData, 'categories_registry')) {
            LogUtil::registerError($this->__f('Error! Could not create a category registry for the %s entity.', array('album')));
        }
        $categoryRegistryIdsPerEntity['album'] = $registryData['id'];

        // create the default data
        $this->createDefaultData($categoryRegistryIdsPerEntity);

        // register persistent event handlers
        $this->registerPersistentEventHandlers();

        // register hook subscriber bundles
        HookUtil::registerSubscriberBundles($this->version->getHookSubscriberBundles());

        // Set up module hooks
        //HookUtil::registerProviderBundles($this->version->getHookProviderBundles()); TODO maybe in 1.4.0

        // initialisation successful
        return true;

    }

    /**
     * Upgrade the MUImage application from an older version.
     *
     * If the upgrade fails at some point, it returns the last upgraded version.
     *
     * @param integer $oldversion Version to upgrade from.
     *
     * @return boolean True on success, false otherwise.
     */
    public function upgrade($oldversion)
    {

        // Upgrade dependent on old version number
        switch ($oldversion) {
            case '1.0.0':
                $this->setVar('ending', 'html');
                $this->setVar('deleteUserPictures', false);
                $this->setVar('minWidth', 400);

            case '1.1.0':
                $this->setVar('pageSizeAdminAlbums', 10);
                $this->setVar('pageSizeAdminPictures', 10);

            case '1.1.1':
                $this->setVar('slideshow1', false);
                $this->setVar('slide1Interval', 4000);
                $this->setVar('slide1Speed', 1000);

            case '1.2.0':
                $this->setVar('shrinkPictures', false);
                $this->setVar('zipSize', '');
                $this->setVar('layout',  'normal' );
                $this->setVar('groupForCommonAlbums',  'notset' );
                $this->setVar('fileNameForTitle', false);
                $this->setVar('supportCategories', true);
                $this->setVar('supportSubAlbums', false);
                $this->setVar('breadcrumbInFrontend', false);
                $this->setVar('kindOfShowSubAlbums', 'panel');
                $this->setVar('orderAlbums', false);
                $this->setVar('createSeveralPictureSizes', false);
                $this->setVar('widthFirst', 400);
                $this->setVar('heightFirst', 400);
                $this->setVar('widthSecond', 600);
                $this->setVar('heightSecond', 600);
                $this->setVar('widthThird', 800);
                $this->setVar('heightThird', 800);
                 
                // update the database schema
                try {
                    DoctrineHelper::updateSchema($this->entityManager, $this->listEntityClasses());
                } catch (Exception $e) {
                    if (System::isDevelopmentMode()) {
                        LogUtil::registerError($this->__('Doctrine Exception: ') . $e->getMessage());
                    }
                    return LogUtil::registerError($this->__f('An error was encountered while dropping the tables for the %s module.', array($this->getName())));
                }
                 
                // we get serviceManager
                $serviceManager = ServiceUtil::getManager();
                // we get entityManager
                $entityManager = $serviceManager->getService('doctrine.entitymanager');

                $selectionHelper = new MUImage_Api_Selection($serviceManager);
                // we get a repository for albums
                $albumrepository = $this->getEntityManager()->getRepository('MUImage_Entity_Album');
                // we get a repository for pictures
                $picturerepository = $this->getEntityManager()->getRepository('MUImage_Entity_Picture');
                 
                // we get a workflow helper
                $workflowHelper = new Zikula_Workflow('none', 'MUImage');
                 
                // we get all albums
                $result = DBUtil::executeSQL('SELECT * FROM `muimage_album`');
                $albums = $result->fetchAll(Doctrine::FETCH_ASSOC);
                 
                // we set each album to approved and all
                foreach ($albums as $album) {
                    $thisalbum = $albumrepository->findOneBy(array('id' => $album['id']));
                    $thisalbum->setWorkflowState('approved');
                    $thisalbum->setAlbumAccess('all');
                    $entityManager->flush();
                     
                    // we set the datas into the workflow table
                    $obj['__WORKFLOW__']['obj_table'] = 'album';
                    $obj['__WORKFLOW__']['obj_idcolumn'] = 'id';
                    $obj['id'] = $album['id'];
                    $workflowHelper->registerWorkflow($obj, 'approved');
                }
                 
                // we get all pictures
                $pictures = $picturerepository->selectWhere();

                $result2 = DBUtil::executeSQL('SELECT * FROM `muimage_picture`');
                $pictures = $result2->fetchAll(Doctrine::FETCH_ASSOC);
                 
                // we set each picture to approved
                foreach ($pictures as $picture) {
                    $thispicture = $picturerepository->findOneBy(array('id' => $picture['id']));
                    $thispicture->setWorkflowState('approved');
                    $entityManager->flush();
                    // we set the datas into the workflow table
                    $obj['__WORKFLOW__']['obj_table'] = 'picture';
                    $obj['__WORKFLOW__']['obj_idcolumn'] = 'id';
                    $obj['id'] = $picture['id'];
                    $workflowHelper->registerWorkflow($obj, 'approved');
                }
                 
                // unregister and register hook providers
                HookUtil::unregisterProviderBundles($this->version->getHookProviderBundles());
                //HookUtil::registerProviderBundles($this->version->getHookProviderBundles()); TODO maybe in 1.4.0
                 
                EventUtil::registerPersistentModuleHandler('MUImage', 'module.scribite.editorhelpers', array('MUImage_Listener_ThirdParty', 'getEditorHelpers'));
                EventUtil::registerPersistentModuleHandler('MUImage', 'moduleplugin.tinymce.externalplugins', array('MUImage_Listener_ThirdParty', 'getTinyMcePlugins'));
                EventUtil::registerPersistentModuleHandler('MUImage', 'moduleplugin.ckeditor.externalplugins', array('MUImage_Listener_ThirdParty', 'getCKEditorPlugins'));

            case '1.3.0':

                // for later updates
        }

        // update successful
        return true;
    }

    /**
     * Uninstall MUImage.
     *
     * @return boolean True on success, false otherwise.
     */
    public function uninstall()
    {
        // delete stored object workflows
        $result = Zikula_Workflow_Util::deleteWorkflowsForModule($this->getName());
        if ($result === false) {
            return LogUtil::registerError($this->__f('An error was encountered while removing stored object workflows for the %s module.', array($this->getName())));
        }

        try {
            DoctrineHelper::dropSchema($this->entityManager, $this->listEntityClasses());
        } catch (Exception $e) {
            if (System::isDevelopmentMode()) {
                LogUtil::registerError($this->__('Doctrine Exception: ') . $e->getMessage());
            }
            return LogUtil::registerError($this->__f('An error was encountered while dropping the tables for the %s module.', array($this->getName())));
        }

        // unregister persistent event handlers
        EventUtil::unregisterPersistentModuleHandlers('MUImage');

        // unregister hook subscriber bundles
        HookUtil::unregisterSubscriberBundles($this->version->getHookSubscriberBundles());

        // unregister hook provider bundles
        HookUtil::unregisterProviderBundles($this->version->getHookProviderBundles());

        // remove all module vars
        $this->delVars();

        // remove category registry entries
        ModUtil::dbInfoLoad('Categories');
        DBUtil::deleteWhere('categories_registry', "modname = 'MUImage'");

        // deletion successful
        return true;
    }

    /**
     * Register persistent event handlers.
     * These are listeners for external events of the core and other modules.
     */
    protected function registerPersistentEventHandlers()
    {

        parent::registerPersistentEventHandlers();

        EventUtil::registerPersistentModuleHandler('MUImage', 'module.scribite.editorhelpers', array('MUImage_Listener_ThirdParty', 'getEditorHelpers'));
        EventUtil::registerPersistentModuleHandler('MUImage', 'moduleplugin.tinymce.externalplugins', array('MUImage_Listener_ThirdParty', 'getTinyMcePlugins'));
        EventUtil::registerPersistentModuleHandler('MUImage', 'moduleplugin.ckeditor.externalplugins', array('MUImage_Listener_ThirdParty', 'getCKEditorPlugins'));
    }
}
