<?php
/*
 *  @package Module UserWay for Joomla! 3.10.3
 *  @version controller.php: 191153 you radik
 *  @author UserWay Development Team
 *  @copyright (C) 2021 - UserWay Inc.
 *  @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die('Restricted access');

class UserwayController extends JControllerLegacy
{
    private $db;


    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->db = JFactory::getDbo();
    }

    /**
     * @return void
     * @throws Exception
     */
    public function save()
    {
        try {
            $input = JFactory::getApplication()->input;
            $accountId = $input->get('accountId', null, 'string');
            $state = $input->get('state', null, 'string');

            if (is_null($accountId) || is_null($state)) {
                throw new Exception('Invalid Save Parameters', 422);
            }

            $entry = $this->getAccount();

            if (!is_null($entry)) {
                $this->update();
                return;
            }

            $insertQuery = $this->db->getQuery(true);
            $columns = array('account_id', 'state', 'created_time', 'updated_time');
            $values = array($this->db->quote($accountId), $this->db->quote($state), $this->getDate(), $this->getDate());

            $insertQuery->insert($this->db->quoteName('#__userway'))
                ->columns($this->db->quoteName($columns))
                ->values(implode(',', $values));

            $this->db->setQuery($insertQuery);
            $this->db->query();

            $cache = JCache::getInstance('callback');
            $cache->clean(null, 'notgroup');

            $accountState = $this->getAccount();

            echo json_encode($accountState);
        } catch (Exception $e) {
            echo 'something went wrong';
        } finally {
            jexit();
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    public function update()
    {

        try {
            $input = JFactory::getApplication()->input;
            $state = $input->get('state', null, 'string');

            if (is_null($state)) {
                throw new Exception('Invalid Update Parameter', 422);
            }

            $entry = $this->getAccount();
            if (is_null($entry)) {
                return;
            }

            $query = $this->db->getQuery(true);
            $set = array(
                $this->db->quoteName('state') . ' = ' . $this->db->quote($state === '1'),
                $this->db->quoteName('updated_time') . ' = ' . $this->getDate()
            );

            $query->update('#__userway')
                ->set($set)
                ->where('account_id', $entry->account_id);

            $this->db->setQuery($query);
            $this->db->query();

            $accountState = $this->getAccount();

            echo json_encode($accountState);
        } catch (Exception $e) {
            echo 'something went wrong';
        } finally {
            jexit();
        }
    }

    /**
     * @return mixed
     */
    private function getAccount()
    {
        $query = $this->db->getQuery(true);
        $query->select($this->db->quoteName(array('state', 'account_id')))
            ->from($this->db->quoteName('#__userway'));

        $this->db->setQuery($query);
        $this->db->query();

        return $this->db->loadObject();
    }

    private function getDate()
    {
        return $this->db->quote(date("Y-m-d H:i:s"));
    }
}

?>
