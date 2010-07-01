<?php
/**
* @package: phpBB3 :: RSS feed 2.0 -> language -> en -> mods 
* @version: $Id: rss.php, v 1.0.3 2008/07/23 23:07:08 leviatan21 Exp $
* @copyright: leviatan21 < info@mssti.com > (Gabriel) http://www.mssti.com/phpbb3/
* @license: http://opensource.org/licenses/gpl-license.php GNU Public License
* @author: leviatan21 - http://www.phpbb.com/community/memberlist.php?mode=viewprofile&u=345763
*
**/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine

$lang = array_merge($lang, array(
	'ACP_RSS'					=> 'RSS management',
	'ACP_RSS_FEEDS'				=> 'RSS',

	'BOARD_DAYS'				=> 'Days since started',
	'ACP_RSS_MANAGEMENT'		=> 'RSS Feeds',
	'ACP_RSS_MANAGEMENT_EXPLAIN'=> 'This Module controls the enabling of the Various RSS Feeds, and the settings for the RSS Feed Parser for reading in external feeds',
	'ACP_RSS_ENABLE'			=> 'Enable Feeds',
	'ACP_RSS_ENABLE_EXPLAIN'	=> 'Disable this to switch off all Feeds, regardless of the settings below',
	'ACP_RSS_CHARACTERS'		=> 'Max length of post text to display',
	'ACP_RSS_CHARACTERS_EXPLAIN'=> 'The number of characters allowed, recommended 1000 chars long.<br /> 0 means infinite, 1 means no text',
	'ACP_RSS_CHARS'				=> 'characters',
	'ACP_RSS_LIMIT'				=> 'Items per page',
	'ACP_RSS_LIMIT_EXPLAIN'		=> 'The maximum number of feed items to display per page.',
	'ACP_RSS_IMAGE_SIZE'		=> 'Maximum image width in pixel',
	'ACP_RSS_IMAGE_SIZE_EXPLAIN'=> 'Image will be resized if exceed the width set here.<br /> 0 means no resize',

	'ACP_RSS_OVERALL_FORUMS'	=> 'Enable overall forums feed',
	'ACP_RSS_OVERALL_THREAD'	=> 'Enable overall threads feed',
	'ACP_RSS_OVERALL_POSTS'		=> 'Enable overall posts feed',
	'ACP_RSS_EGOSEARCH'			=> 'Enable EgoSearch Feed',
	'ACP_RSS_EGOSEARCH_EXPLAIN'	=> 'Like View Your Posts, only works if you remain logged in when you browse away from the forum...',
	'ACP_RSS_FORUM'				=> 'Enable Forum Feed',
	'ACP_RSS_FORUM_EXPLAIN'		=> 'Single Forum thrads/posts',
	'ACP_RSS_THREAD'			=> 'Enable Thread Feed',
	'ACP_RSS_THREAD_EXPLAIN'	=> 'Single Thread new posts',

	'COPYRIGHT'					=> 'Copyright',
	'NO_RSS_ITEMS'				=> 'No Items Available',
	'NO_RSS_ITEMS_EXPLAIN'		=> 'Unfortunately there appears to be no news items on the page you have requested, worthy to be logged here',
	'NO_RSS_ITEMS_LOGGED_IN'	=> 'You must be logged in to use %1$s RSS Feed',

));

?>
