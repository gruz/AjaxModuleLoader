<?php
/**
 * The installer script which installs languages and performs migrating
 *
 * @package		NotifyArticleSubmit
 * @subpackage	NotifyArticleSubmit.Script
 * @author Gruz <arygroup@gmail.com>
 * @copyright	Copyleft - All rights reversed
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Script file
 */

class plgSystemAjaxmoduleloaderInstallerScript {
	/**
	 * method to install the component
	 *
	 * @return void
	 */
	function install($parent)
	{

		$tmpl_path = JPATH_ROOT.'/templates/system/ajaxmoduleloader.php';
		$source_path = __DIR__ . '/helper/ajaxmoduleloader.php';
		JFile::copy($source_path,$tmpl_path);
		//echo ;
		// $parent is the class calling this method
		// $parent->getParent()->setRedirectURL('index.php?option=com_helloworld');
		// echo '<p>' . JText::_('COM_HELLOWORLD_INSTALL_TEXT') . '</p>';
	}

	/**
	 * method to uninstall the component
	 *
	 * @return void
	 */
	function uninstall($parent)
	{
		$tmpl_path = JPATH_ROOT.'/templates/system/ajaxmoduleloader.php';
		JFile::delete($tmpl_path);
		// echo '<p>' . JText::_('COM_HELLOWORLD_UNINSTALL_TEXT') . '</p>';
	}

	/**
	 * method to update the component
	 *
	 * @return void
	 */
	function update($parent)
	{
		$this->install($parent);
		// $parent is the class calling this method
		// echo '<p>' . JText::sprintf('COM_HELLOWORLD_UPDATE_TEXT', $parent->get('manifest')->version) . '</p>';
	}

	/**
	 * method to run before an install/update/uninstall method
	 *
	 * @return void
	 */
	function preflight($type, $parent)
	{
		// $parent is the class calling this method
		// $type is the type of change (install, update or discover_install)
		// echo '<p>' . JText::_('COM_HELLOWORLD_PREFLIGHT_' . $type . '_TEXT') . '</p>';
	}

	/**
	 * method to run after an install/update/uninstall method
	 *
	 * @return void
	 */
	function postflight($type, $parent)
	{
		// $parent is the class calling this method
		// $type is the type of change (install, update or discover_install)
		// echo '<p>' . JText::_('COM_HELLOWORLD_POSTFLIGHT_' . $type . '_TEXT') . '</p>';
	}
}
?>

