<?php

class BUDDYPRESS_JSON_API_FUNCTION extends JSON_API_BuddypressRead_Controller {

    protected static $sVars = array();

    /**
     * Load the Parameters defined in parameters.php
     * @param String $sModule the module to load
     * @throws Exception if parameters for module aren't defined
     */
    protected static function init($sModule) {
        global $json_api;
        if (!self::checkModuleActive($sModule))
            $json_api->error("The BuddyPress module '" . $sModule . "' has to be enabled to use this function.");
        require_once (BUDDYPRESS_JSON_API_HOME . '/library/parameters.php');

        if (!isset($aParams [$sModule]))
            throw new Exception("Parameters for module not defined.");
		foreach ($aParams [$sModule] as $sType => $aParameters) {
			foreach ($aParameters as $sValName => $sVal) {
                self::$sVars [$sValName] = self::getVar($sValName, $sVal, $sType);
            }
        }
    }

    private function checkModuleActive($sModule) {
		if ($sModule != 'notifications' && !key_exists($sModule, bp_get_option('bp-active-components'))) {
            if ($sModule == 'forums'){
                if (function_exists("bbp_is_group_forums_active") && bbp_is_group_forums_active()){return true;}
			}
            return false;
        }
        return true;
    }

    private static function getVar($sValName, $sVal, $sType) {
        global $json_api;
        $mReturnVal = is_null($json_api->query->$sValName) ? $sVal : $json_api->query->$sValName;
        return self::sanitize($mReturnVal, $sType);
    }

    /**
     * Method to sanitize the values given
     * @param mixed $mValue Value to sanitize
     * @param String $sType type of the Value given by parameters array
     * @return mixed sanitized value
     */
    private static function sanitize($mValue, $sType) {
        switch ($sType) {
            case "int":
                if ($mValue !== false)
                    $mValue = (int) $mValue;
                break;
            case "boolean":
                $mValue = (boolean) $mValue;
            case "string":
            default:
                switch (gettype($mValue)) {
                    case 'string':
                        $mValue = strip_tags($mValue);
                        break;
                    case 'boolean':
                    default:
                        break;
                }
                break;
        }
        return $mValue;
    }

    protected static function get_group_from_params() {
        if (self::$sVars ['groupid'] === false && self::$sVars ['groupslug'] === false)
            return 2;
        $oGroup = groups_get_group(array('group_id' => self::$sVars ['groupid']));
        if (is_null($oGroup->id)) {
            self::$sVars ['groupid'] = groups_get_id(sanitize_title(self::$sVars ['groupslug']));
            if (self::$sVars ['groupid'] === 0)
                return 3;
            else
                $oGroup = groups_get_group(array('group_id' => self::$sVars ['groupid']));
        }
        else {
            self::$sVars ['groupid'] = $oGroup->id;
        }
        if ($oGroup->status == 'private' && !$oGroup->is_member)
            return false;
        return true;
    }

    protected static function groupforum_check_forum_existence() {
		if (self::$sVars['forumid'] === false && self::$sVars['forumslug'] === false)
            return 4;
		
		$oForum = bbp_get_forum(self::$sVars['forumid']);
		if (is_null($oForum) || $oForum === false) {
			
			if(function_exists('bb_get_id_from_slug')){
				$iForumId = bb_get_id_from_slug('forum', sanitize_title(self::$sVars['forumslug']));
			}else{
			global $wpdb;
				$in_string =  sanitize_title(self::$sVars['forumslug']);
				$post_type_in_string = bbp_get_forum_post_type();
				$sql = "SELECT ID FROM $wpdb->posts WHERE post_name IN (\"$in_string\") AND post_type IN (\"$post_type_in_string\")";
				$iForumId = $wpdb->get_var($sql);
			}
			
            if ($iForumId === 0)
                return 5;
            else {
                self::$sVars['forumid'] = $iForumId;
            }
        } else {
            self::$sVars['forumid'] = $oForum->ID;
        }
		$iGroupId = groups_get_id(sanitize_title(self::$sVars ['groupslug']));
        $oGroup = groups_get_group(array('group_id' => $iGroupId));
        if ($oGroup->status == 'private' && !$oGroup->is_member)
            return false;
        return true;
    }

    protected static function sitewideforum_check_forum_existence() {
        if (self::$sVars['forumid'] === false && self::$sVars['forumslug'] === false)
            return 4;
        $oForum = bbp_get_forum(self::$sVars['forumid']);
        if (is_null($oForum)) {
            global $wpdb;
            $aForums = $wpdb->get_results($wpdb->prepare(
                            "SELECT ID
                 FROM   $wpdb->posts
                 WHERE  post_type='forum'
                 AND post_name='" . self::$sVars['forumslug'] . "'"
                    ));
            if (empty($aForums))
                return 5;
            else {
                self::$sVars['forumid'] = array();
                foreach ($aForums as $aForum) {
                    self::$sVars['forumid'][] = $aForum->ID;
                }
            }
        } else {
            self::$sVars['forumid'] = array();
            self::$sVars['forumid'][] = $oForum->ID;
        }
        return true;
    }

    protected static function groupforum_check_topic_existence() {
        if (self::$sVars['topicid'] === false && self::$sVars['topicslug'] === false)
            return 6;
		
		if(function_exists('bp_forums_get_topic_details')){
			$oTopic = bp_forums_get_topic_details(self::$sVars['topicid']);
		}elseif(function_exists('bbp_get_topic')){
			$oTopic = bbp_get_topic(self::$sVars['topicid']);
			if($oTopic){$oTopic->topic_id = $oTopic->ID;}
		}
		
		if (is_null($oTopic) || (int) $oTopic->topic_id != self::$sVars['topicid']) {
				if(function_exists('bb_get_id_from_slug')){
					$iTopicId = bb_get_id_from_slug('topic', sanitize_title(self::$sVars['topicslug']));
				}else{
					global $wpdb;
					$in_string =  sanitize_title(self::$sVars['topicslug']);
					$post_type_in_string = bbp_get_topic_post_type();				
					$sql = "SELECT ID FROM $wpdb->posts WHERE post_name IN (\"$in_string\") AND post_type IN (\"$post_type_in_string\")";
					$iTopicId = $wpdb->get_var($sql);
				}
			
			if ($iTopicId === 0){
				return 8;
            } else {
				self::$sVars['topicid'] = $iTopicId;
				global $wpdb;
				$post_type_in_string = bbp_get_topic_post_type();
				$oTopic = bbp_get_topic(self::$sVars['topicid']);
				$oTopic = $wpdb->get_col("SELECT * FROM $wpdb->posts WHERE ID=\"$iTopicId\"");
            }
        }
        else
            self::$sVars['topicid'] = $oTopic->ID;
		
        if (is_null($oTopic))
            return false;
		
        return true;
    }

    protected static function sitewideforum_check_topic_existence() {
        if (self::$sVars['topicid'] === 0 && self::$sVars['topicslug'] === false)
            return 6;
        global $wpdb;
        if (self::$sVars['topicid'] !== 0)
            $oTopic = $wpdb->get_row($wpdb->prepare(
                            "SELECT ID
                 FROM   $wpdb->posts
                 WHERE  post_type='topic'
                 AND id='" . self::$sVars['topicid'] . "'"
                    ));

        if (is_null($oTopic)) {
            global $wpdb;
            $aTopics = $wpdb->get_results($wpdb->prepare(
                            "SELECT ID
                 FROM   $wpdb->posts
                 WHERE  post_type='topic'
                 AND post_name='" . self::$sVars['topicslug'] . "'"
                    ));
            if (empty($aTopics))
                return 8;
            else {
                self::$sVars['topicid'] = array();
                foreach ($aTopics as $aTopic) {
                    self::$sVars['topicid'][] = $aTopic->ID;
                }
            }
        } else {
            self::$sVars['topicid'] = array();
            self::$sVars['topicid'][] = $oTopic->ID;
        }
        return true;
    }

    /**
     * Returns a String containing an error message
     * @param String $sModule Modules name
     * @param type $iCode Errorcode
     */
    protected static function error($sModule, $iCode = "") {
        $oReturn = new stdClass();
        $oReturn->status = "error";
        switch ($sModule) {
            case "activity":
                $oReturn->msg = __('No Activities found.', 'json-api-for-buddypress');
                break;
            case "xprofile":
                switch ($iCode) {
                    case 0:
                        $oReturn->msg = __('No Profile found.', 'json-api-for-buddypress');
                        break;
                    case 1:
                        $oReturn->msg = __('Username not found.', 'json-api-for-buddypress');
                        break;
                }
                break;
            case "messages":
                $oReturn->msg = __('No messages found.', 'json-api-for-buddypress');
                break;
            case "notifications":
                $oReturn->msg = __('No notifications found.', 'json-api-for-buddypress');
                break;
            case "friends":
                switch ($iCode) {
                    case 0:
                        $oReturn->msg = __('Username not found.', 'json-api-for-buddypress');
                        break;
                    case 1:
                        $oReturn->msg = __('No friends found.', 'json-api-for-buddypress');
                        break;
                    case 2:
                        $oReturn->msg = __('No friendship requests found.', 'json-api-for-buddypress');
                        break;
                    case 3:
                        $oReturn->msg = __('Friendname not found.', 'json-api-for-buddypress');
                        break;
                }
                break;
            case "groups":
                switch ($iCode) {
                    case 0:
                        $oReturn->msg = __('No groups found.', 'json-api-for-buddypress');
                        break;
                    case 1:
                        $oReturn->msg = __('Username not found.', 'json-api-for-buddypress');
                        break;
                    case 2:
                        $oReturn->msg = __('Neither groupid nor groupslug are set.', 'json-api-for-buddypress');
                        break;
                    case 3:
                        $oReturn->msg = __('Group not found.', 'json-api-for-buddypress');
                        break;
                    case 4:
                        $oReturn->msg = __('No Members in Group', 'json-api-for-buddypress');
                        break;
                }
                break;
            case "forums":
                switch ($iCode) {
                    case 0:
                        $oReturn->msg = __('Forums are disabled for this group.', 'json-api-for-buddypress');
                        break;
                    case 1:
                        $oReturn->msg = __('No forum assigned to this group.', 'json-api-for-buddypress');
                        break;
                    case 2:
                        $oReturn->msg = __('Neither groupid nor groupslug are set.', 'json-api-for-buddypress');
                        break;
                    case 3:
                        $oReturn->msg = __('Group not found.', 'json-api-for-buddypress');
                        break;
                    case 4:
                        $oReturn->msg = __('Neither forumid nor forumslug are set.', 'json-api-for-buddypress');
                        break;
                    case 5:
                        $oReturn->msg = __('Forum not found.', 'json-api-for-buddypress');
                        break;
                    case 6:
                        $oReturn->msg = __('Neither topicid nor topicslug are set.', 'json-api-for-buddypress');
                        break;
                    case 7:
                        $oReturn->msg = __('No topics in this forum.', 'json-api-for-buddypress');
                        break;
                    case 8:
                        $oReturn->msg = __('No topics found.', 'json-api-for-buddypress');
                        break;
                    case 9:
                        $oReturn->msg = __('No forums found.', 'json-api-for-buddypress');
                        break;
                }
                break;
            case "settings":
                switch ($iCode) {
                    case 0:
                        $oReturn->msg = __('Username not found.', 'json-api-for-buddypress');
                        break;
                }
                break;
            case "base":
                switch ($iCode) {
                    case 0:
                        $oReturn->msg = __('You are not allowed to view this information.', 'json-api-for-buddypress');
                        break;
                }
                break;
            default:
                $oReturn->msg = __('An undefined error occured.', 'json-api-for-buddypress');
        }
        return $oReturn;
    }

}