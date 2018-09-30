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

namespace MU\ImageModule;

use MU\ImageModule\Base\AbstractImageModuleInstaller;

use RuntimeException;

/**
 * Installer implementation class.
 */
class ImageModuleInstaller extends AbstractImageModuleInstaller
{
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
    
        $logger = $this->container->get('logger');
    
        // Upgrade dependent on old version number
        switch ($oldVersion) {
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
                $this->setVar('supportSubAlbums', true);
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
                // update the database schema
                try {
                    $this->schemaTool->update($this->listEntityClasses());
                } catch (\Exception $exception) {
                    $this->addFlash('error', $this->__('Doctrine Exception') . ': ' . $exception->getMessage());
                    $logger->error('{app}: Could not update the database tables during the upgrade. Error details: {errorMessage}.', ['app' => 'MUImageModule', 'errorMessage' => $exception->getMessage()]);
                    
                    return false;
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
            	// nothing to do
            	
            case '1.3.1':
            	try {
            		$this->schemaTool->update($this->listEntityClasses());
            	} catch (\Exception $exception) {
            		$this->addFlash('error', $this->__('Doctrine Exception') . ': ' . $exception->getMessage());
            		$logger->error('{app}: Could not update the database tables during the upgrade. Error details: {errorMessage}.', ['app' => 'MUImageModule', 'errorMessage' => $exception->getMessage()]);
            	
            		return false;
            	}
            	
            	// rename module for all modvars
            	$this->updateModVarsTo14();
            	
            	// update extension information about this app
            	$this->updateExtensionInfoFor14();
            	
            	// rename existing permission rules
            	$this->renamePermissionsFor14();
            	
            	// rename existing category registries
            	$this->renameCategoryRegistriesFor14();
            	
            	// rename all tables
            	$this->renameTablesFor14();
            	
            	// remove event handler definitions from database
            	$this->dropEventHandlersFromDatabase();
            	
            	// update module name in the hook tables
            	$this->updateHookNamesFor14();
            	
            	// update module name in the workflows table
            	$this->updateWorkflowsFor14();
            	
            	// remove obsolete persisted hooks from the database
            	$this->hookApi->uninstallSubscriberHooks($this->bundle->getMetaData());
            	
            	$modvars = $this->getVars();
            	
            	$this->setVar('firstWidth', $modvars['widthFirst']);
            	$this->setVar('firstHeight', $modvars['heightFirst']);
            	
            	$this->setVar('secondWidth', $modvars['widthSecond']);
            	$this->setVar('secondHeight', $modvars['heightSecond']);
            	
            	$this->setVar('thirdWidth', $modvars['widthThird']);
            	$this->setVar('thirdHeight', $modvars['heightThird']);
            	
            	// delete modvars
                $this->delVar('fileNameForTitle');
                $this->delVar('layout');
                
                $this->delVar('widthFirst');
                $this->delVar('heightFirst');
                
                $this->delVar('widthSecond');
                $this->delVar('heightSecond');
                
                $this->delVar('widthThird');
                $this->delVar('heightThird');                
                
            case '1.4.0':
            	// later update
                
        // update successful
        return true;                
                
        }
    }
}
