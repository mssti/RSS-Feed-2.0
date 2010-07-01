<?php
/**
* @package: phpBB3 :: RSS feed 2.0 -> includes -> acp -> info
* @version: $Id: acp_rss.php, v 1.0.7 2009/01/16 16:01:09 leviatan21 Exp $
* @copyright: leviatan21 < info@mssti.com > (Gabriel) http://www.mssti.com/phpbb3/
* @license: http://opensource.org/licenses/gpl-license.php GNU Public License
* @author: leviatan21 - http://www.phpbb.com/community/memberlist.php?mode=viewprofile&u=345763
*
**/

/**
* @package module_install
*/
class acp_rss_info
{
	function module()
	{
		return array(
			'filename'	=> 'acp_rss',
			'title'		=> 'ACP_RSS',
			'version'	=> '1.0.7',
			'modes'		=> array(
				'rss_feeds'	=> array('title' => 'ACP_RSS_FEEDS', 'auth' => 'acl_a_board', 'cat' => array('ACP_GENERAL_TASKS')),
			),
		);
	}

	function install()
	{
	}

	function uninstall()
	{
	}
}

?>
