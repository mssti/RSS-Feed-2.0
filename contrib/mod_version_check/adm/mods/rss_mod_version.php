<?php
/**
* @package: phpBB3 :: RSS feed 2.0 mod version check -> adm -> mods
* @version: $Id: rss_mod_version.php, v 1.0.7 2009/01/16 16:01:09 leviatan21 Exp $
* @copyright: leviatan21 < info@mssti.com > (Gabriel) http://www.mssti.com/phpbb3/
* @license: http://opensource.org/licenses/gpl-license.php GNU Public License
* @author: leviatan21 - http://www.phpbb.com/community/memberlist.php?mode=viewprofile&u=345763
*
**/

if (!defined('IN_PHPBB'))
{
	exit;
}

class rss_mod_version
{
	function version()
	{
		return array(
			'author'	=> 'leviatan21',
			'title'		=> '>MSSTI RSS Feed 2.0 with ACP',
			'tag'		=> 'rssfeed20',
			'version'	=> '1.0.7',
			'file'		=> array('mssti.com', 'phpbb3', 'mssti_phpbb3x_mods.xml'),
		);
	}
}
?>