<?php /* ajaxmoduleloader file */
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
	$jinput = JFactory::getApplication()->input;
	$moduleposition = $jinput->get('moduleposition','position-7');
	$modulestyle = $jinput->get('modulestyle','xhtml');

?>

<jdoc:include type="modules" name="<?php echo $moduleposition?>" style="<?php echo $modulestyle?>"/>
