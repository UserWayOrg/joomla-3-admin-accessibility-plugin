<?php
/*
 *  @package Module UserWay for Joomla! 3.10.3
 *  @version helper.php: 191153 you radik
 *  @author UserWay Development Team
 *  @copyright (C) 2021 - UserWay Inc.
 *  @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die('Restricted access');

class WidgetHelper
{
    /**
     * @return mixed
     */
    public static function getCurrentWidgetData()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select($db->quoteName(array('state', 'account_id')))
            ->from($db->quoteName('#__userway'));

        $db->setQuery($query);
        $db->query();
        return $db->loadObject();
    }

    static function getSiteUrl(){
        $mainframe = JFactory::getApplication('site');
        if( isset($mainframe) && is_object($mainframe)) {
            return $mainframe->isAdmin() ? $mainframe->getSiteURL() : JURI::base();
        } else {
            return JURI::base();
        }
    }

    public static function getWidget()
    {
        $widgetData = WidgetHelper::getCurrentWidgetData();
        $widgetState = 'false';
        $widgetAccount = '';

        if (isset($widgetData)) {
            $widgetState = $widgetData->state ? 'true' : 'false';
            $widgetAccount = $widgetData->account_id ?: '';
        }

        $url = rtrim(JURI::root(), '/');
        $widgetUrl = "https://qa.userway.dev/api/apps/joomla?storeUrl={$url}&active={$widgetState}";

        if ($widgetAccount !== '') {
            $widgetUrl .= '&account_id=' . $widgetAccount;
        }

        return <<<HTML
<html>
<body>
<iframe
id="userway-frame"
src="{$widgetUrl}"
title="UserWay Widget"
width="100%"
height="1180px"
style="border: none;"
data-url="{$url}"
>
</iframe>
<script type="text/javascript">
const MESSAGE_ACTION_TOGGLE = 'WIDGET_TOGGLE';
const MESSAGE_ACTION_SIGNUP = 'WIDGET_SIGNUP';
const request = (url, data) => {
return new Promise((resolve, reject) => {
jQuery.ajax({
  url,
  type: 'GET',
}).done(data => resolve(data))
  .fail(err => reject(err));
})
}
const requestUpdateAccountState = (url, accountId, state) => {
return request(url + '/administrator/index.php?option=com_userway&accountId=' + accountId + '&state=' + state + '&task=save', { accountId, state })
};
const requestToggleState = (url, accountId, state) => {

   console.log('url =>', url)
console.log('url =>', url + '&account=' + accountId + '&state=' + state + '&task=save')

return request(url + '/administrator/index.php?option=com_userway&accountId=' + accountId + '&state=' + state + '&task=save', { accountId, state }, { state })
};
const isPostMessageAccountToggleAction = (postMessage) => {
return postMessage.data !== undefined
&& postMessage.data.action
&& postMessage.data.action === MESSAGE_ACTION_TOGGLE
}
const isPostMessageSignupAction = (postMessage) => {
return postMessage.data !== undefined
&& postMessage.data.action
&& postMessage.data.action === MESSAGE_ACTION_SIGNUP
}
jQuery(document).ready(function () {
const selector = document.getElementById('userway-frame');
const frameContentWindow = selector.contentWindow;
const { url } = selector.dataset;

window.addEventListener('message', postMessage => {
if (postMessage.source !== frameContentWindow) {
  return;
}

if (isPostMessageAccountToggleAction(postMessage)) {
  const state = postMessage.data.state ? 1 : 0;
  const account = postMessage.data?.account;
  
  if (!account) {
      console.log('account not found')
      return
  }
  
  requestToggleState(decodeURIComponent(url), account, state)
    .then(res => console.log(res))
    .catch(err => console.error(err));
} else if (isPostMessageSignupAction(postMessage)) {
  const state = postMessage.data.state ? 1 : 0;
  const account = postMessage.data.account;
   
   if (!account) {
      console.log('account not found')
      return
  }
  
  requestUpdateAccountState(decodeURIComponent(url), account, state)
    .then(res => console.log(res))
    .catch(err => console.error(err));
}
});
});
</script>
</body>
</html>
HTML;
    }
}

?>