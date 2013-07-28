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
        $basePath = MUImage_Util_Controller::getFileBaseFolder('picture', 'imageUpload');
        if (!is_dir($basePath)) {
            return LogUtil::registerError($this->__f('The upload folder "%s" does not exist. Please create it before installing this application.', array($basePath)));
        }
        if (!is_writable($basePath)) {
            return LogUtil::registerError($this->__f('The upload folder "%s" is not writable. Please change permissions accordingly before installing this application.', array($basePath)));
        }

        // create all tables from according entity definitions
        try {
            DoctrineHelper::createSchema($this->entityManager, $this->listEntityClasses());
        } catch (Exception $e) {
            if (System::isDevelopmentMode()) {
                LogUtil::registerError($this->__('Doctrine Exception: ') . $e->getMessage());
            }
            return LogUtil::registerError($this->__f('An error was encountered while creating the tables for the %s module.', array($this->getName())));
        }

        // set up all our vars with initial values
        $this->setVar('pagesize', 10);
        $this->setVar('showTitle', false);
        $this->setVar('showDescription', false);
        $this->setVar('countImageView', false);
        $this->setVar('numberParentAlbums', 1);
        $this->setVar('numberSubAlbums', 2);
        $this->setVar('numberPictures', 20);
        $this->setVar('fileSize', '');
        $this->setVar('ending', 'html');
        $this->setVar('deleteUserPictures', false);
        $this->setVar('minWidth', 400);
        $this->setVar('pageSizeAdminAlbums', 10);
        $this->setVar('pageSizeAdminPictures', 10);
        $this->setVar('slideshow1', false);
        $this->setVar('slide1Interval', 4000);
        $this->setVar('slide1Speed', 1000);

        // create the default data for MUImage
        $this->createDefaultData();

        // add entries to category registry
        $rootcat = CategoryUtil::getCategoryByPath('/__SYSTEM__/Modules/Global');
        CategoryRegistryUtil::insertEntry('MUImage', 'Album', 'Main', $rootcat['id']);

        // register persistent event handlers
        $this->registerPersistentEventHandlers();

        // register hook subscriber bundles
        HookUtil::registerSubscriberBundles($this->version->getHookSubscriberBundles());

        // Set up module hooks
        HookUtil::registerProviderBundles($this->version->getHookProviderBundles());

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
                	
                // later updates
                	
                // do something
                // ...
                // update the database schema
                /*try {
                 DoctrineHelper::updateSchema($this->entityManager, $this->listEntityClasses());
                 } catch (Exception $e) {
                 if (System::isDevelopmentMode()) {
                 LogUtil::registerError($this->__('Doctrine Exception: ') . $e->getMessage());
                 }
                 return LogUtil::registerError($this->__f('An error was encountered while dropping the tables for the %s module.', array($this->getName())));
                 }*/
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
}
