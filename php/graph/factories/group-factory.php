<?php

require 'base-factory.php';

/**
 * A factory for retrieving, hiding, and showing Facebook groups.
 */
class GroupFactory extends BaseFactory {

    private $sqlConnectionInfo;
    private $sqlServer;

    function __construct() {
        parent::__construct();
        
        $this->sqlConnectionInfo = array("UID" => "rogan2929@lreuagtc6u", "pwd" => "Revelation19:11", "Database" => "swapperAGiJRLgvy", "LoginTimeout" => 30, "Encrypt" => 1);
        $this->sqlServer = "tcp:lreuagtc6u.database.windows.net,1433";
    }

    /**
     * Look up group membership information for the current user.
     * @return array
     */
    public function getGroupInfo() {
        $queries = array(
            'memberQuery' => 'SELECT gid,bookmark_order FROM group_member WHERE uid=me() ORDER BY bookmark_order',
            'groupQuery' => 'SELECT gid,name,icon FROM group WHERE gid IN (SELECT gid FROM #memberQuery)'
        );

        $response = $this->graphApiClient->api(array(
            'method' => 'fql.multiquery',
            'queries' => $queries
        ));
        
        $groups = array();
        
        for ($i = 0; $i < count($response[1]['fql_result_set']); $i++) {
            $groups[] = new Group($response[1]['fql_result_set'][$i]['gid'], $response[1]['fql_result_set'][$i]['name'], $response[1]['fql_result_set'][$i]['icon']);
        }

        // Grab the results of the query and return it.
        return $groups;
    }

    /**
     * Queries the Swapper's Delight SQL backend for groups that the user has marked as 'hidden'.
     * @return string
     */
    public function getHiddenGroups() {
        $uid = $this->graphApiClient->getMe();

        $conn = sqlsrv_connect($this->sqlServer, $this->sqlConnectionInfo);

        if ($conn === false) {
            die(print('Could not connect to database.'));
        }

        $sql = 'SELECT HiddenGroup FROM HiddenGroups WHERE UID=\'' . $uid . '\'';

        // Execute the query.
        $result = sqlsrv_query($conn, $sql);

        $hiddenGroups = '';

        while ($row = sqlsrv_fetch_array($result)) {
            $hiddenGroups .= $row['HiddenGroup'] . ' ';
        }

        return $hiddenGroups;
    }

    /**
     * Mark the group with provided gid as hidden.
     * @param type $id
     */
    public function hideGroup($id) {
        $uid = $this->graphApiClient->getMe();

        $conn = sqlsrv_connect($this->sqlServer, $this->sqlConnectionInfo);

        if ($conn === false) {
            die(print('Could not connect to database.'));
        }

        $sql = 'INSERT INTO HiddenGroups (UID, HiddenGroup) VALUES (\'' . $uid . '\', \'' . $id . '\')';

        // Execute the query.
        sqlsrv_query($conn, $sql);
    }

    /**
     * Removes all of the current user's hidden groups from the Swapper's Delight backend.
     */
    public function restoreGroups() {
        $uid = $this->graphApiClient->getMe();

        $conn = sqlsrv_connect($this->sqlServer, $this->sqlConnectionInfo);

        if ($conn === false) {
            die(print('Could not connect to database.'));
        }

        $sql = 'DELETE FROM HiddenGroups WHERE UID=\'' . $uid . '\'';

        // Execute the query.
        sqlsrv_query($conn, $sql);
    }

}