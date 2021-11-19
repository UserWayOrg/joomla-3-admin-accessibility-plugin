<?php
/*
 *  @package Module UserWay for Joomla! 3.10.3
 *  @version userway.php: 191153 you radik
 *  @author UserWay Development Team
 *  @copyright (C) 2021 - UserWay Inc.
 *  @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die;

require_once dirname(__FILE__) . '/helper.php';

$controller = JControllerLegacy::getInstance('Userway');
$input = JFactory::getApplication()->input;
$task = $input->getCmd('task');
if ($task) {
    $controller->execute($task);
} else {
    echo WidgetHelper::getWidget();
}
?>