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
 * Utility implementation class for image helper methods.
 */
class MUImage_Util_Image extends MUImage_Util_Base_Image
{
	public static function getMetaDatas($imageurl) {

		$dom = ZLanguage::getModuleDomain('MUImage');

		$filter = array('FileDateTime', 'FileType', 'html', 'IsColor', 'ApertureFNumber', 'ResolutionUnit', 'XResolution', 'YResolution','Orientation',
				'ImageDescription', 'ExifVersion','MakerNote', 'UserCommentEncoding', 'SectionsFound', 'ByteOrderMotorola', 'CompressedBitsPerPixel',
				'ShutterSpeedValue', 'ApertureValue', 'ExifImageWidth', 'ExifImageHeight', 'ExifImageLength', 'ComponentsConfiguration', 'DateTimeDigitized',
				'ExposureBiasValue', 'Exif_IFD_Pointer', 'Aperture', 'MeteringMode', 'DateTime','LightSource', 'ExposureProgram', 'FileSource',
				'FocalLengthIn35mmFilm', 'MaxApertureValue', 'ExposureMode', 'SceneType');


		$Exif = exif_read_data($imageurl,'IFD0', true);
		if($Exif === false) {
			$metadatas = __('No metadatas available', $dom);
		}
		else
		{
			$metadatas = '';
			foreach($Exif as $key => $Abschnitt)
			{
				foreach($Abschnitt as $Name => $Wert)
				{
					if ($key != 'APP12' && !in_array($Name, $filter)) {

						if ($Name == 'FileName') {
							$Name = __('File name', $dom);
						}
						if ($Name == 'FileSize') {
							$Name = __('File size ', $dom);
							$Wert = $Wert . __(' bytes', $dom);
						}
						if ($Name == 'FNumber'){
							$Name = __('Aperture', $dom);
							$Wert = (int)$Wert / 10;
						}
						if ($Name == 'ExposureTime') {
							$Name = __('Exposer time', $dom);
						}
						if ($Name == 'WhiteBalance') {
							$Name = __('White balance', $dom);
						}
						if ($Name == 'MimeType') {
							$Name = __('Mime type', $dom);
						}
						if ($Name == 'Make') {
							$Name = __('Company', $dom);
						}
						if ($Name == 'Model') {
							$Name = __('Model', $dom);
						}
						if ($Name == 'DateTimeOriginal') {
							$Name = __('Original time', $dom);
						}
						if ($Name == 'ISOSpeedRatings') {
							$Name = __('ISO', $dom);
						}
						if ($Name == 'FocalLength') {
							$Name = __('Focal length', $dom);
							$nameArray = explode('/', $Wert);
							$Wert = $nameArray[0] / $nameArray[1];
						}
						if ($Name == 'BrightnessValue') {
							$Name = __('Brightness', $dom);
						}
						if ($Name == 'Contrast') {
							$Name = __('Contrast', $dom);
						}
						if ($Name == 'Saturation') {
							$Name = __('Saturation', $dom);
						}
						if ($Name == 'Sharpness') {
							$Name = __('Sharpness', $dom);
						}
						if ($Name == 'SceneCaptureType') {
							$Name = __('Scene capture type', $dom);
							switch ($Wert) {
								case 0:
									$Wert = __('Standard', $dom);
									break;
								case 1:
									$Wert = __('Landscape', $dom);
									break;
								case 2:
									$Wert = __('Portrait', $dom);
									break;
								case 3:
									$Wert = __('Nigtht scene', $dom);
									break;
								default:
									$Wert = __('Other', $dom);
							}
						}
						if ($Name == 'Flash') {
							if ($Wert == 0)
								$Wert = __('No', $dom);
							else {
								$Wert = __('Yes', $dom);
							}
						}
						if ($key == 'EXIF' && $Name == 'UserComment') {
							continue;
						}
						if ($Name == 'UserComment' && key != 'EXIF'){
							$Name = __('User comment', $dom);
						}
						$metadatas .= "<b>$Name:</b> $Wert<br>\n";
					}
				}
			}
		}

		return $metadatas;
	}
}
