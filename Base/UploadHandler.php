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

namespace MU\MUImageModule\Base;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Common\Translator\TranslatorTrait;
use Zikula\UsersModule\Api\CurrentUserApi;
use DataUtil;
use FileUtil;
use RuntimeException;
use ServiceUtil;

/**
 * Upload handler base class.
 */
class UploadHandler
{
    use TranslatorTrait;

    /**
     * @var CurrentUserApi
     */
    protected $currentUserApi;

    /**
     * @var array List of object types with upload fields
     */
    protected $allowedObjectTypes;

    /**
     * @var array List of file types to be considered as images
     */
    protected $imageFileTypes;

    /**
     * @var array List of dangerous file types to be rejected
     */
    protected $forbiddenFileTypes;

    /**
     * @var array List of allowed file sizes per field
     */
    protected $allowedFileSizes;

    /**
     * Constructor initialising the supported object types.
     *
     * @param TranslatorInterface $translator Translator service instance
     */
    public function __construct(TranslatorInterface $translator, CurrentUserApi $currentUserApi)
    {
        $this->setTranslator($translator);
        $this->currentUserApi = $currentUserApi;
        $this->allowedObjectTypes = ['picture'];
        $this->imageFileTypes = ['gif', 'jpeg', 'jpg', 'png', 'swf'];
        $this->forbiddenFileTypes = ['cgi', 'pl', 'asp', 'phtml', 'php', 'php3', 'php4', 'php5', 'exe', 'com', 'bat', 'jsp', 'cfm', 'shtml'];
        $this->allowedFileSizes = ['picture' => ['imageUpload' => 0]];
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
     * Process a file upload.
     *
     * @param string $objectType Currently treated entity type
     * @param string $fileData   Form data array
     * @param string $fieldName  Name of upload field
     *
     * @return array Resulting file name and collected meta data
     *
     * @throws RuntimeException Thrown if upload file base path retrieval fails or the file can not be moved to it's destination folder
     */
    public function performFileUpload($objectType, $fileData, $fieldName)
    {
        $result = [
            'fileName' => '',
            'metaData' => []
        ];
    
        // check whether uploads are allowed for the given object type
        if (!in_array($objectType, $this->allowedObjectTypes)) {
            return $result;
        }
    
        $file = $fileData[$fieldName];
    
        // perform validation
        try {
            $this->validateFileUpload($objectType, $file, $fieldName);
        } catch (\Exception $e) {
            // skip this upload field
            return $result;
        }
    
        // retrieve the final file name
        $fileName = $file->getClientOriginalName();
        $fileNameParts = explode('.', $fileName);
        $extension = null !== $file->guessExtension() ? $file->guessExtension() : $file->guessClientExtension();
        if (null === $extension) {
            $extension = strtolower($fileNameParts[count($fileNameParts) - 1]);
        }
        $extension = str_replace('jpeg', 'jpg', $extension);
        $fileNameParts[count($fileNameParts) - 1] = $extension;
        $fileName = implode('.', $fileNameParts);
    
        $serviceManager = ServiceUtil::getManager();
        $controllerHelper = $serviceManager->get('mu_muimage_module.controller_helper');
        $flashBag = $serviceManager->get('session')->getFlashBag();
        $logger = $serviceManager->get('logger');
    
        // retrieve the final file name
        try {
            $basePath = $controllerHelper->getFileBaseFolder($objectType, $fieldName);
        } catch (\Exception $e) {
            $flashBag->add(\Zikula_Session::MESSAGE_ERROR, $e->getMessage());
            $logger->error('{app}: User {user} could not detect upload destination path for entity {entity} and field {field}.', ['app' => 'MUMUImageModule', 'user' => $this->currentUserApi->get('uname'), 'entity' => $objectType, 'field' => $fieldName]);
    
            return false;
        }
        $fileName = $this->determineFileName($objectType, $fieldName, $basePath, $fileName, $extension);
    
        $targetFile = $file->move($basePath, $fileName);
    
        // validate file size
        $maxSize = $this->allowedFileSizes[$objectType][$fieldName];
        if ($maxSize > 0) {
            $fileSize = filesize($basePath . $fileName);
            if ($fileSize > $maxSize) {
                $maxSizeKB = $maxSize / 1024;
                if ($maxSizeKB < 1024) {
                    $maxSizeKB = DataUtil::formatNumber($maxSizeKB); 
                    $flashBag->add(\Zikula_Session::MESSAGE_ERROR, $this->__f('Error! Your file is too big. Please keep it smaller than %s kilobytes.', [$maxSizeKB]));
                    $logger->error('{app}: User {user} tried to upload a file with a size greater than "{size} KB".', ['app' => 'MUMUImageModule', 'user' => $this->currentUserApi->get('uname'), 'size' => $maxSizeKB]);
        
                    $fs = new Filesystem();
                    try {
                        $fs->remove(array($basePath . $fileName));
                    } catch (IOExceptionInterface $e) {
                        $logger->error('{app}: The file could not be properly removed from the file system.', []);
                    }
        
                    return false;
                }
                $maxSizeMB = $maxSizeKB / 1024;
                $maxSizeMB = DataUtil::formatNumber($maxSizeMB); 
                $flashBag->add(\Zikula_Session::MESSAGE_ERROR, $this->__f('Error! Your file is too big. Please keep it smaller than %s megabytes.', [$maxSizeMB]));
                $logger->error('{app}: User {user} tried to upload a file with a size greater than "{size} MB".', ['app' => 'MUMUImageModule', 'user' => $this->currentUserApi->get('uname'), 'size' => $maxSizeMB]);
        
                $fs = new Filesystem();
                try {
                    $fs->remove(array($basePath . $fileName));
                } catch (IOExceptionInterface $e) {
                    $logger->error('{app}: The file could not be properly removed from the file system.', []);
                }
        
                return false;
            }
        }
        
        // validate image file
        $isImage = in_array($extension, $this->imageFileTypes);
        if ($isImage) {
            $imgInfo = getimagesize($basePath . $fileName);
            if (!is_array($imgInfo) || !$imgInfo[0] || !$imgInfo[1]) {
                $flashBag->add(\Zikula_Session::MESSAGE_ERROR, $this->__('Error! This file type seems not to be a valid image.'));
                $logger->error('{app}: User {user} tried to upload a file which is seems not to be a valid image.', ['app' => 'MUMUImageModule', 'user' => $this->currentUserApi->get('uname')]);
        
                return false;
            }
        }
    
        // collect data to return
        $result['fileName'] = $fileName;
        $result['metaData'] = $this->readMetaDataForFile($fileName, $basePath . $fileName);
    
        return $result;
    }

    /**
     * Check if an upload file meets all validation criteria.
     *
     * @param string $objectType Currently treated entity type
     * @param array $file Reference to data of uploaded file
     * @param string $fieldName  Name of upload field
     *
     * @return boolean true if file is valid else false
     *
     * @throws RuntimeException Thrown if validating the upload file fails
     */
    protected function validateFileUpload($objectType, $file, $fieldName)
    {
        $serviceManager = ServiceUtil::getManager();
        $flashBag = $serviceManager->get('session')->getFlashBag();
        $logger = $serviceManager->get('logger');
    
        // check if a file has been uploaded properly without errors
        if (!is_array($file) || (is_array($file) && $file['error'] != '0')) {
            if (is_array($file)) {
                return $this->handleError($file);
            }
            $flashBag->add(\Zikula_Session::MESSAGE_ERROR, $this->__('Error! No file found.'));
            $logger->error('{app}: User {user} tried to upload a file which could not be found.', ['app' => 'MUMUImageModule', 'user' => $this->currentUserApi->get('uname')]);
    
            return false;
        }
    
        // extract file extension
        $fileName = $file->getClientOriginalName();
        $extension = null !== $file->guessExtension() ? $file->guessExtension() : $file->guessClientExtension();
        if (null === $extension) {
            $fileNameParts = explode('.', $fileName);
            $extension = strtolower($fileNameParts[count($fileNameParts) - 1]);
        }
        $extension = str_replace('jpeg', 'jpg', $extension);
    
        // validate extension
        $isValidExtension = $this->isAllowedFileExtension($objectType, $fieldName, $extension);
        if ($isValidExtension === false) {
            $flashBag->add(\Zikula_Session::MESSAGE_ERROR, $this->__('Error! This file type is not allowed. Please choose another file format.'));
            $logger->error('{app}: User {user} tried to upload a file with a forbidden extension ("{extension}").', ['app' => 'MUMUImageModule', 'user' => $this->currentUserApi->get('uname'), 'extension' => $extension]);
    
            return false;
        }
    
        return true;
    }

    /**
     * Read meta data from a certain file.
     *
     * @param string $fileName  Name of file to be processed
     * @param string $filePath  Path to file to be processed
     *
     * @return array collected meta data
     */
    public function readMetaDataForFile($fileName, $filePath)
    {
        $meta = [];
        if (empty($fileName)) {
            return $meta;
        }
    
        $extensionarr = explode('.', $fileName);
        $meta['extension'] = strtolower($extensionarr[count($extensionarr) - 1]);
        $meta['size'] = filesize($filePath);
        $meta['isImage'] = in_array($meta['extension'], $this->imageFileTypes) ? true : false;
    
        if (!$meta['isImage']) {
            return $meta;
        }
    
        if ($meta['extension'] == 'swf') {
            $meta['isImage'] = false;
        }
    
        $imgInfo = getimagesize($filePath);
        if (!is_array($imgInfo)) {
            return $meta;
        }
    
        $meta['width'] = $imgInfo[0];
        $meta['height'] = $imgInfo[1];
    
        if ($imgInfo[1] < $imgInfo[0]) {
            $meta['format'] = 'landscape';
        } elseif ($imgInfo[1] > $imgInfo[0]) {
            $meta['format'] = 'portrait';
        } else {
            $meta['format'] = 'square';
        }
    
        return $meta;
    }

    /**
     * Determines the allowed file extensions for a given object type.
     *
     * @param string $objectType Currently treated entity type
     * @param string $fieldName  Name of upload field
     * @param string $extension  Input file extension
     *
     * @return array the list of allowed file extensions
     */
    protected function isAllowedFileExtension($objectType, $fieldName, $extension)
    {
        // determine the allowed extensions
        $allowedExtensions = [];
        switch ($objectType) {
            case 'picture':
                $allowedExtensions = ['gif', 'jpeg', 'jpg', 'png'];
                    break;
        }
    
        if (count($allowedExtensions) > 0) {
            if (!in_array($extension, $allowedExtensions)) {
                return false;
            }
        }
    
        if (in_array($extension, $this->forbiddenFileTypes)) {
            return false;
        }
    
        return true;
    }

    /**
     * Determines the final filename for a given input filename.
     *
     * It considers different strategies for computing the result.
     *
     * @param string $objectType Currently treated entity type
     * @param string $fieldName  Name of upload field
     * @param string $basePath   Base path for file storage
     * @param string $fileName   Input file name
     * @param string $extension  Input file extension
     *
     * @return string the resulting file name
     */
    protected function determineFileName($objectType, $fieldName, $basePath, $fileName, $extension)
    {
        $backupFileName = $fileName;
    
        $namingScheme = 0;
    
        switch ($objectType) {
            case 'picture':
                $namingScheme = 0;
                    break;
        }
    
    
        $iterIndex = -1;
        do {
            if ($namingScheme == 0) {
                // original file name
                $fileNameCharCount = strlen($fileName);
                for ($y = 0; $y < $fileNameCharCount; $y++) {
                    if (preg_match('/[^0-9A-Za-z_\.]/', $fileName[$y])) {
                        $fileName[$y] = '_';
                    }
                }
                // append incremented number
                if ($iterIndex > 0) {
                    // strip off extension
                    $fileName = str_replace('.' . $extension, '', $backupFileName);
                    // add iterated number
                    $fileName .= (string) ++$iterIndex;
                    // readd extension
                    $fileName .= '.' . $extension;
                } else {
                    $iterIndex++;
                }
            } elseif ($namingScheme == 1) {
                // md5 name
                $fileName = md5(uniqid(mt_rand(), TRUE)) . '.' . $extension;
            } elseif ($namingScheme == 2) {
                // prefix with random number
                $fileName = $fieldName . mt_rand(1, 999999) . '.' . $extension;
            }
        }
        while (file_exists($basePath . $fileName)); // repeat until we have a new name
    
        // return the new file name
        return $fileName;
    }

    /**
     * Error handling helper method.
     *
     * @param array $file File array from $_FILES
     *
     * @return boolean false
     *
     * @throws RuntimeException Thrown if an unknown error occurs
     */
    private function handleError($file)
    {
        $errorMessage = '';
        switch ($file['error']) {
            case UPLOAD_ERR_OK: //no error; possible file attack!
                $errorMessage = $this->__('Unknown error');
                break;
            case UPLOAD_ERR_INI_SIZE: //uploaded file exceeds the upload_max_filesize directive in php.ini
                $errorMessage = $this->__('File too big');
                break;
            case UPLOAD_ERR_FORM_SIZE: //uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the html form
                $errorMessage = $this->__('File too big');
                break;
            case UPLOAD_ERR_PARTIAL: //uploaded file was only partially uploaded
                $errorMessage = $this->__('File uploaded partially');
                break;
            case UPLOAD_ERR_NO_FILE: //no file was uploaded
                $errorMessage = $this->__('No file uploaded');
                break;
            case UPLOAD_ERR_NO_TMP_DIR: //missing a temporary folder
                $errorMessage = $this->__('No tmp folder');
                break;
            default: //a default (error, just in case!  :)
                $errorMessage = $this->__('Unknown error');
                break;
        }
    
        $serviceManager = ServiceUtil::getManager();
        $session = $serviceManager->get('session');
        $session->getFlashBag()->add(\Zikula_Session::MESSAGE_ERROR, $this->__('Error with upload') . ': ' . $errorMessage);
        $logger = $serviceManager->get('logger');
        $logger->error('{app}: User {user} received an upload error: "{errorMessage}".', ['app' => 'MUMUImageModule', 'user' => $this->currentUserApi->get('uname'), 'errorMessage' => $errorMessage]);
    
        return false;
    }

    /**
     * Deletes an existing upload file.
     * For images the thumbnails are removed, too.
     *
     * @param string  $objectType Currently treated entity type
     * @param string  $objectData Object data array
     * @param string  $fieldName  Name of upload field
     * @param integer $objectId   Primary identifier of the given object
     *
     * @return mixed Array with updated object data on success, else false
     */
    public function deleteUploadFile($objectType, $objectData, $fieldName, $objectId)
    {
        if (!in_array($objectType, $this->allowedObjectTypes)) {
            return false;
        }
    
        if (empty($objectData[$fieldName])) {
            return $objectData;
        }
    
        $serviceManager = ServiceUtil::getManager();
        $controllerHelper = $serviceManager->get('mu_muimage_module.controller_helper');
    
        // determine file system information
        try {
            $basePath = $controllerHelper->getFileBaseFolder($objectType, $fieldName);
        } catch (\Exception $e) {
            $logger = $serviceManager->get('logger');
            $logger->error('{app}: User {user} could not detect upload destination path for entity {entity} and field {field}.', ['app' => 'MUMUImageModule', 'user' => $this->currentUserApi->get('uname'), 'entity' => $objectType, 'field' => $fieldName]);
    
            return false;
        }
        $fileName = $objectData[$fieldName];
    
        // path to original file
        $filePath = $basePath . $fileName;
    
        // check whether we have to consider thumbnails, too
        $fileExtension = FileUtil::getExtension($fileName, false);
        if (in_array($fileExtension, $this->imageFileTypes) && $fileExtension != 'swf') {
            // remove thumbnail images as well
            $manager = ServiceUtil::getManager()->get('systemplugin.imagine.manager');
            $manager->setModule('MUMUImageModule');
            $fullObjectId = $objectType . '-' . $objectId;
            $manager->removeImageThumbs($filePath, $fullObjectId);
        }
    
        // remove original file
        if (!unlink($filePath)) {
            return false;
        }
        $objectData[$fieldName] = '';
        $objectData[$fieldName . 'Meta'] = [];
    
        return $objectData;
    }
}
