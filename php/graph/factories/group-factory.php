<?php

require 'graph-object-factory.php';

/**
 * A factory for retrieving, hiding, and showing Facebook groups.
 */
class GroupFactory extends GraphObjectFactory {

    private $sqlConnectionInfo;
    private $sqlServer;

    function __construct() {
        parent::__construct();
        
        $this->sqlConnectionInfo = array("UID" => "rogan2929@lreuagtc6u", "pwd" => "Revelation19:11", "Database" => "swapperAGiJRLgvy", "LoginTimeout" => 30, "Encrypt" => 1);
        $this->sqlServer = "tcp:lreuagtc6u.database.windows.net,1433";
    }
    
    /**
     * Add a group to the application.
     * @param string $gid
     */
    public function addGroup($gid) {
        $uid = $this->graphApiClient->getMe();

        $conn = sqlsrv_connect($this->sqlServer, $this->sqlConnectionInfo);

        if ($conn === false) {
            die(print('Could not connect to database.'));
        }

        // Insert the appropriate row.
        $sql = 'INSERT INTO UserGroups (UserId, GroupId) VALUES (\'' . $uid . '\', \'' . $gid . '\')';

        // Execute the query.
        sqlsrv_query($conn, $sql);
    }
    
    /**
     * Remove a marked group.
     * @param string $gid
     */
    public function removeGroup($gid) {
        $uid = $this->graphApiClient->getMe();

        $conn = sqlsrv_connect($this->sqlServer, $this->sqlConnectionInfo);

        if ($conn === false) {
            die(print('Could not connect to database.'));
        }

        // Delete the appropriate row.
        $sql = 'DELETE FROM UserGroups WHERE UserId=\'' . $uid . '\' AND GroupId=\'' . $gid . '\'';

        // Execute the query.
        sqlsrv_query($conn, $sql);   
    }
    
    /**
     * Look up group membership information for the current user.
     * @return array
     */
    public function getGroupInfo() {
        $userGroupIds = $this->getUserGroupIds();
        
        $groups = array();
        
        for ($i = 0; $i < count($userGroupIds); $i++) {
            $response = $this->graphApiClient->executeRequest('GET', '/' . $userGroupIds[$i]);
            $groups[] = new Group($response->id, $response->name, $response->icon);
        }
            
        return $groups;
    }
    
    /**
     * Retrieve marked groups for that belong to the user.
     */
    private function getUserGroupIds() {
        //return array('409783902455116', '125721407536012');
        // Get the UID of the currently logged in user.
        $uid = $this->graphApiClient->getMe();

        $conn = sqlsrv_connect($this->sqlServer, $this->sqlConnectionInfo);

        if ($conn === false) {
            die(print('Could not connect to database.'));
        }

        $sql = 'SELECT UserId,GroupId FROM UserGroups WHERE UserId=\'' . $uid . '\'';

        // Execute the query.
        $result = sqlsrv_query($conn, $sql);

        $userGroupIds = array();

        while ($row = sqlsrv_fetch_array($result)) {
            $userGroupIds[] = $row['GroupId'];
        }
        
        return $userGroupIds;
    }
}