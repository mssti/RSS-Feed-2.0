<?php
/**
* @package: phpBB3 :: RSS feed 2.0 -> includes -> acp -> info
* @version: $Id: rss.php, v 1.2.0 2009/04/10 10:04:09 leviatan21 Exp $
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
			'version'	=> '1.2.0',
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