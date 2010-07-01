<?php
/**
* @package: phpBB3 :: RSS feed 2.0
* @version: $Id: rss.php, v 0.0.3 2008/07/23 23:07:08 leviatan21 Exp $
* @copyright: leviatan21 < info@mssti.com > (Gabriel) http://www.mssti.com/phpbb3/
* @license: http://opensource.org/licenses/gpl-license.php GNU Public License
* @author: leviatan21 - http://www.phpbb.com/community/memberlist.php?mode=viewprofile&u=345763
*
**/

/**
* @ignore
* http://www.uatsap.com/rss/manual/6
* http://blogs.law.harvard.edu/tech/rss
**/

define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);

include($phpbb_root_path . 'common.' . $phpEx);

// Start session
$user->session_begin();
$auth->acl($user->data);
$user->setup();
$user->add_lang( array('common', 'acp/common', 'mods/rss') );

// Initial var setup
$board_url	= generate_board_url();

$sql		= '';
$error		= '';
$text_to_display = '';
$stats		= '';

$forum_id	= request_var('f', 0);
$topic_id	= request_var('t', 0);
$rss_mode	= request_var('mode', '');
$feed_mode	= empty($rss_mode) ? '' : $rss_mode ;

// Flood limits
$text_limit = $config['rss_characters'];
$show_text	= ( $text_limit == 1 ) ? false : true;

// Pagination
$start		= max(request_var('start', 0), 0);
$u_rss		= 'rss.' . $phpEx . ( empty($rss_mode) ? '' : '?mode='.$rss_mode ) . ( empty($forum_id) ? '' : '?f='.$forum_id ) . ( empty($topic_id) ? '' : '&amp;t='.$topic_id );
$per_page	= $config['rss_limit'];
$total_count= 0;

// The SQL query selects the latest topics of all forum
switch ($rss_mode)
{
	case 'forums':
		$feed_mode	= $user->lang['FORUMS'];

		$row_title	= 'forum_name';
		$row_creator= 'forum_last_poster_id';
		$row_text	= 'forum_desc';
		$row_bit	= 'forum_desc_bitfield';
		$row_uid	= 'forum_desc_uid';
		$row_date	= 'forum_last_post_time';

		$sql = 'SELECT  forum_id, forum_name, forum_desc, forum_desc_uid, forum_desc_bitfield, forum_last_poster_name, forum_last_post_time, forum_last_poster_id 
				FROM ' . FORUMS_TABLE . ' 
				WHERE forum_type = ' . FORUM_POST . ' AND forum_last_post_id > 0 
				ORDER BY forum_last_post_time DESC';
		break;

	case 'topics':
		$show_text	= false;
		$feed_mode	= $user->lang['TOPICS'];

		$row_title	= 'topic_title';
		$row_creator= 'topic_poster';
		$row_text	= '';
		$row_bit	= '';
		$row_uid	= '';
		$row_date	= 'topic_last_post_time';

		$sql = 'SELECT  forum_id, topic_id, topic_title, topic_last_post_time, topic_poster 
				FROM ' . TOPICS_TABLE . ' 
				WHERE topic_status != 1 
				ORDER BY topic_last_post_time DESC';
		break;

	case 'posts':
		$feed_mode	= $user->lang['POSTS'];

		$row_title	= 'post_subject';
		$row_creator= 'poster_id';
		$row_text	= 'post_text';
		$row_bit	= 'bbcode_bitfield';
		$row_uid	= 'bbcode_uid';
		$row_date	= 'post_time';

		$sql = 'SELECT  forum_id, post_id, post_subject, post_text, bbcode_uid, bbcode_bitfield, post_time, poster_id 
				FROM ' . POSTS_TABLE . ' 
				WHERE post_approved = 1 
				ORDER BY post_time DESC';
		break;

	case 'egosearch':
		//check logged on
		if ($user->data['user_id'] == ANONYMOUS)
		{
			$error = 'egosearch';
			$error_desc = sprintf($user->lang['NO_RSS_ITEMS_LOGGED_IN'], $config['sitename'] );
		}
		$feed_mode	= $user->lang['SEARCH_SELF'];

		$row_title	= 'post_subject';
		$row_creator= 'poster_id';
		$row_text	= 'post_text';
		$row_bit	= 'bbcode_bitfield';
		$row_uid	= 'bbcode_uid';
		$row_date	= 'post_time';

		$sql = 'SELECT  forum_id, post_id, post_subject, poster_id, post_text, bbcode_uid, bbcode_bitfield, post_time, poster_id
				FROM ' . POSTS_TABLE . ' 
				WHERE poster_id =' . $user->data['user_id'] . ' 
				AND post_approved = 1 
				ORDER BY post_time DESC';
		break;

	default:
		$row_title	= 'topic_title';
		$row_creator= 'user_id';
		$row_text	= 'post_text';
		$row_bit	= 'bbcode_bitfield';
		$row_uid	= 'bbcode_uid';
		$row_date	= 'post_time';

		$forum_sql = empty($forum_id) ? '' : " AND f.forum_id = $forum_id";
		$topic_sql = empty($topic_id) ? '' : " AND p.topic_id = t.topic_id AND t.topic_id = $topic_id";

		$sql = 'SELECT  f.forum_id, 
						t.topic_id, t.topic_title, t.topic_last_post_id, 
						p.post_id, p.post_time, p.post_text, p.bbcode_uid, p.bbcode_bitfield, 
						u.username, u.user_id 
				FROM ' . FORUMS_TABLE . ' f,' . TOPICS_TABLE . ' t,' . POSTS_TABLE . ' p,' . USERS_TABLE . ' u 
				WHERE t.forum_id = f.forum_id 
				AND t.topic_status != 1 AND t.topic_approved = 1
 				AND u.user_id = p.poster_id' . $forum_sql . $topic_sql . (empty($topic_sql) ? ' AND p.post_id = t.topic_last_post_id ' : '') . ' 
				ORDER BY ' . (empty($topic_sql) ? 't.topic_last_post_time DESC' : 'p.post_time DESC');
		break;
}

// only return up to 100 ids (you can change it to your liking)
if ( $result = $db->sql_query_limit($sql, 100) )
{
	$page_ary = array();
	while ($row = $db->sql_fetchrow($result))
	{
		$page_ary[] = $row['forum_id'];
	}
	$db->sql_freeresult($result);
	$total_count = sizeof($page_ary);
	unset($page_ary);

	$result = $db->sql_query_limit($sql, $per_page, $start);
}

if(!$result)
{
	trigger_error($user->lang['NO_RSS_ITEMS'] . '<p>' . $user->lang['NO_RSS_ITEMS_EXPLAIN'] . '</p>');
}

while($row = $db->sql_fetchrow($result))
{
	$forum_data = get_forum_data($row['forum_id']); // $forum_data['forum_id'] // $forum_data['forum_name']

	//getting authentication
	if($auth->acl_get('f_read', $forum_data['forum_id']))
	{
		switch ($rss_mode)
		{
			case 'forums':
				$item_link	= "$board_url/viewforum.$phpEx?f={$forum_data['forum_id']}";
				$stats		= $user->lang['STATISTICS'] . ' : ' . sprintf($user->lang['TOTAL_TOPICS_OTHER'], $forum_data['forum_topics']) . ' &bull; ' . sprintf($user->lang['TOTAL_POSTS_OTHER'], $forum_data['forum_posts']);
				break;
			case 'topics':
				$topic_data	= get_topic_data($row['topic_id']);
				$item_link = "$board_url/viewtopic.$phpEx?f={$forum_data['forum_id']}&amp;t={$topic_data['topic_id']}";
				$stats		= $user->lang['STATISTICS'] . ' : ' . $user->lang['POSTED'] . ' ' . $user->lang['POST_BY_AUTHOR'] . ' ' . get_user_data( $topic_data['topic_poster'] ) . ' &bull; ' . $user->lang['POSTED_ON_DATE'] . ' ' . $user->format_date($topic_data['topic_time']). ' &bull; ' . $user->lang['REPLIES'] . ' ' . $topic_data['topic_replies'] . ' &bull; ' . $user->lang['VIEWS'] . ' ' . $topic_data['topic_views'];
				break;
			case 'posts':
				$post_data	= get_post_data($row['post_id']);
				$item_link = "$board_url/viewtopic.$phpEx?p={$post_data['post_id']}#p{$post_data['post_id']}";
				$stats		= $user->lang['STATISTICS'] . ' : ' . $user->lang['POSTED'] . ' ' . $user->lang['POST_BY_AUTHOR'] . ' ' . get_user_data( $post_data['poster_id'] ) . ' &bull; ' . $user->lang['POSTED_ON_DATE'] . ' ' . $user->format_date($post_data['post_time']);
				break;
			case 'egosearch':
				$item_link = "$board_url/viewtopic.$phpEx?p={$row['post_id']}#p{$row['post_id']}";
				break;
			default:
				$topic_data	= get_topic_data($row['topic_id']);
				$item_link = "$board_url/viewtopic.$phpEx?p={$row['post_id']}#p{$row['post_id']}";
				$feed_mode = $user->lang['FORUMS'] .( ($forum_id) ? ' > ' . $forum_data['forum_name'] . ( ($topic_id) ? ' > ' . $user->lang['TOPICS'] . ' > ' . $row['topic_title'] : '' ) : '' );
				$stats		= $user->lang['STATISTICS'] . ' : ' . $user->lang['POSTED'] . ' ' . $user->lang['POST_BY_AUTHOR'] . ' ' . get_user_data( $topic_data['topic_poster'] ) . ' &bull; ' . $user->lang['POSTED_ON_DATE'] . ' ' . $user->format_date($topic_data['topic_time']). ' &bull; ' . $user->lang['REPLIES'] . ' ' . $topic_data['topic_replies'] . ' &bull; ' . $user->lang['VIEWS'] . ' ' . $topic_data['topic_views'];
				break;
		}

		$template->assign_block_vars('items', array(
			'TITLE'			=> $row[$row_title],
			'LINK'			=> $item_link,
			'DESCRIPTION'	=> ( $row_text != '' && $show_text) ? fix_content($row[$row_text], $row[$row_uid], $row[$row_bit], false) : '',
		//	'CONTENT'		=> fix_content($row[$row_text], $row[$row_uid], $row[$row_bit], true),
			'STATISTICS'	=> $stats,
			'PUBDATE'		=> date3339($row[$row_date], $config['board_dst']),
			'CATEGORY'		=> "$board_url/viewforum.$phpEx?f={$forum_data['forum_id']}",
			'CATEGORY_NAME'	=> utf8_htmlspecialchars($forum_data['forum_name']),
			'CREATOR'		=> get_user_data( $row[$row_creator] ),
			'GUID'			=> $item_link,
		));
	}
}
$db->sql_freeresult($result);

// Output page
header("Content-Type: application/xml; charset=UTF-8");

// Set custom template for styles area
$template->set_custom_template($phpbb_root_path . 'styles', 'rss');
$template->assign_var('T_TEMPLATE_PATH', $phpbb_root_path . 'styles');

// the rss template is never stored in the database
$user->theme['template_storedb'] = false;

// Which timezone?
$tz = ($user->data['user_id'] != ANONYMOUS) ? strval(doubleval($user->data['user_timezone'])) : strval(doubleval($config['board_timezone']));
$boarddays = (time() - $config['board_startdate']) / 86400;
$template->assign_vars(array(
	'FEED_TITLE'			=> $user->lang['ACP_RSS_MANAGEMENT'] . ' :: ' . $config['sitename'],
	'FEED_MODE'				=> ($rss_mode == 'egosearch') ? $user->lang['USERNAME'] .' > '. $user->data['username'] : $feed_mode,
	'FEED_DESCRIPTION'		=> $config['site_desc'],
	'FEED_INDEX'			=> $board_url,
	'FEED_LANG'				=> $user->lang['USER_LANG'],
	'FEED_DATE'				=> date('D, d M Y H:i:s O',$config['board_startdate']),
	'FEED_TIME'				=> date('D, d M Y H:i:s O', time()),
	'FEED_MANAGING'			=> $config['board_email'] . " (" . $config['sitename'] . ")",
	'FEED_IMAGE'			=> $board_url . '/' . substr($user->img('site_logo', '', false, '', $type = 'src'),2),
	'FEED_TEXT'				=> $show_text,

	'STAT_TITLE'			=> $config['sitename'] . ' ' . $user->lang['STATISTICS'] . ' : ',
	'STAT_BOARD_STARTED'	=> $user->format_date($config['board_startdate']),
	'STAT_BOARD_DAYS'		=> floor(abs( (time() - $config['board_startdate']) / (60 * 60 * 24) )),
	'STAT_BOARD_VERSION'	=> $config['version'],
	'L_STAT_TIMEZONE'		=> sprintf($user->lang['ALL_TIMES'], '', ''),
	'STAT_TIMEZONE'			=> ($user->data['user_dst'] || ($user->data['user_id'] != ANONYMOUS && $config['board_dst'])) ? $user->lang['tz'][$tz] . ' - ' . $user->lang['tz']['dst'] : $user->lang['tz'][$tz], '',

	'STAT_TOTAL_POSTS'		=> $config['num_posts'],
	'STAT_POSTS_PER_DAY'	=> sprintf('%.2f', $config['num_posts'] / $boarddays),
	'STAT_TOTAL_TOPICS'		=> $config['num_topics'],
	'STAT_TOPICS_PER_DAY'	=> sprintf('%.2f', $config['num_topics'] / $boarddays),
	'STAT_TOTAL_USERS'		=> $config['num_users'],
	'STAT_USERS_PER_DAY'	=> sprintf('%.2f', $config['num_users'] / $boarddays),
	'L_NEWEST_USER'			=> sprintf( $user->lang['NEWEST_USER'], ''),
	'STAT_ONLINE_USERS'		=> sprintf($user->lang['RECORD_ONLINE_USERS'], $config['record_online_users'], $user->format_date($config['record_online_date'])),
	'STAT_NEWEST_USER'		=> $config['newest_username'],

	'PAGINATION'			=> generate_pagination($u_rss, $total_count, $per_page, $start),
	'PAGE_NUMBER'			=> on_page($total_count, $per_page, $start),

	'S_ERROR'				=> ($error == 'egosearch') ? $error_desc : false,
));

$template->set_filenames(array(
	'body' => 'rss_template.xml')
);

//page_footer();
$template->display('body');


function fix_content($content, $uid, $bitfield, $html = false)
{
	global $text_limit, $show_text;
	global $phpbb_root_path, $user, $phpEx, $config;

	if ( $show_text && !empty($content) )
	{
		/**
		* 
		* 'T_THEME_PATH'			=> "{$phpbb_root_path}styles/" . $user->theme['theme_path'] . '/theme',
		* 'T_TEMPLATE_PATH'			=> "{$phpbb_root_path}styles/" . $user->theme['template_path'] . '/template',
		* 'T_IMAGESET_PATH'			=> "{$phpbb_root_path}styles/" . $user->theme['imageset_path'] . '/imageset',
		* 'T_IMAGESET_LANG_PATH'	=> "{$phpbb_root_path}styles/" . $user->theme['imageset_path'] . '/imageset/' . $user->data['user_lang'],
		* 'T_IMAGES_PATH'			=> "{$phpbb_root_path}images/",
		* 'T_SMILIES_PATH'			=> "{$phpbb_root_path}{$config['smilies_path']}/",
		* 'T_AVATAR_PATH'			=> "{$phpbb_root_path}{$config['avatar_path']}/",
		* 'T_AVATAR_GALLERY_PATH'	=> "{$phpbb_root_path}{$config['avatar_gallery_path']}/",
		* 'T_ICONS_PATH'			=> "{$phpbb_root_path}{$config['icons_path']}/",
		* 'T_RANKS_PATH'			=> "{$phpbb_root_path}{$config['ranks_path']}/",
		* 'T_UPLOAD_PATH'			=> "{$phpbb_root_path}{$config['upload_path']}/",
		* 'T_STYLESHEET_LINK'		=> (!$user->theme['theme_storedb']) ? "{$phpbb_root_path}styles/" . $user->theme['theme_path'] . '/theme/stylesheet.css' : "{$phpbb_root_path}style.$phpEx?sid=$user->session_id&amp;id=" . $user->theme['style_id'] . '&amp;lang=' . $user->data['user_lang'],
		* 'T_STYLESHEET_NAME'		=> $user->theme['theme_name'],
		**/
		$content	= str_replace('{SMILIES_PATH}', "{$phpbb_root_path}{$config['smilies_path']}/", $content);

		$content	= str_replace('./', generate_board_url().'/', $content);
		$content	= preg_replace('#<!-- (.*?) -->#si','',$content);

		$content	= generate_text_for_display($content, $uid, $bitfield,7);
		
		$content	= truncate($content, $text_limit, $text_limit*2, false, ' [....]');
	}

	$content = htmlspecialchars_decode($content);
	
	$content = wordwrap($content, 140, "\n", true);

	return $content;
}

/**
* Truncates string while retaining special characters if going over the max length
* The default max length is 60 at the moment
* The maximum storage length is there to fit the string within the given length. The string may be further truncated due to html entities.
* For example: string given is 'a "quote"' (length: 9), would be a stored as 'a &quot;quote&quot;' (length: 19)
*
* @param string $string The text to truncate to the given length. String is specialchared.
* @param int $max_length Maximum length of string (multibyte character count as 1 char / Html entity count as 1 char)
* @param int $max_store_length Maximum character length of string (multibyte character count as 1 char / Html entity count as entity chars).
* @param bool $allow_reply Allow Re: in front of string
* @param string $append String to be appended
*/
function truncate($string, $max_length = 60, $max_store_length = 255, $allow_reply = true, $append = '')
{
	$chars = array();
	$stripped = false;

	$_chars = utf8_str_split(htmlspecialchars_decode($string));
	$chars = array_map('utf8_htmlspecialchars', $_chars);

	// Now check the length ;)
	if (sizeof($chars) > $max_length)
	{
		// Cut off the last elements from the array
		$string = implode('', array_slice($chars, 0, $max_length - utf8_strlen($append)));
		$stripped = true;
	}

	// Due to specialchars, we may not be able to store the string...
	if (utf8_strlen($string) > $max_store_length)
	{
		// let's split again, we do not want half-baked strings where entities are split
		$_chars = utf8_str_split(htmlspecialchars_decode($string));
		$chars = array_map('utf8_htmlspecialchars', $_chars);

		do
		{
			array_pop($chars);
			$string = implode('', $chars);
		}
		while (utf8_strlen($string) > $max_store_length || !sizeof($chars));
	}

	if ($append != '' && $stripped)
	{
		$string = $string . $append;
	}

	return $string;
}

function unhtmlentities($cadena)
{
    // reemplazar entidades numéricas
    $cadena = preg_replace('~&#x([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $cadena);
    $cadena = preg_replace('~&#([0-9]+);~e', 'chr("\\1")', $cadena);
    // reemplazar entidades literales
    $trans_tbl = get_html_translation_table(HTML_ENTITIES);
    $trans_tbl = array_flip($trans_tbl);
    return strtr($cadena, $trans_tbl);
}
    
/**
* Get date in RFC3339, used in XML/Atom
*
* @param integer $timestamp
* @return string date in RFC3339
* @author Boris Korobkov
* @see http://tools.ietf.org/html/rfc3339
**/
function date3339($timestamp = 0)
{
	if (!$timestamp)
	{
		$timestamp = time();
	}
	$date = date('Y-m-d\TH:i:s', $timestamp);
	
	$matches = array();
	if (preg_match('/^([\-+])(\d{2})(\d{2})$/', date('O', $timestamp), $matches))
	{
		$date .= $matches[1].$matches[2].':'.$matches[3];
	}
	else
	{
		$date .= 'Z';
	}
	
    return $date;
}

function get_forum_data( $forum_id )
{
	global $db;
	
	$sql = "SELECT forum_id, forum_name, forum_topics, forum_posts 
			FROM " . FORUMS_TABLE . "
			WHERE forum_id = $forum_id";

	$result = $db->sql_query($sql);
	$forum_data = $db->sql_fetchrow($result);
	$db->sql_freeresult($result);
	
	if (!$forum_data)
	{
		trigger_error('NO_FORUM');
	}

	return $forum_data;
}

function get_topic_data( $topic_id )
{
	global $db;
	
	$sql = "SELECT topic_id, topic_title, topic_views, topic_replies, topic_poster, topic_time 
			FROM " . TOPICS_TABLE . "
			WHERE topic_id = $topic_id";

	$result = $db->sql_query($sql);
	$topic_data = $db->sql_fetchrow($result);
	$db->sql_freeresult($result);
	
	if (!$topic_data)
	{
		trigger_error('NO_FORUM');
	}

	return $topic_data;
}

function get_post_data( $post_id )
{
	global $db;
	
	$sql = "SELECT post_id, post_subject, post_time, poster_id 
			FROM " . POSTS_TABLE . "
			WHERE post_id = $post_id";

	$result = $db->sql_query($sql);
	$post_data = $db->sql_fetchrow($result);
	$db->sql_freeresult($result);
	
	if (!$post_data)
	{
		trigger_error('NO_POSTS');
	}

	return $post_data;
}

function get_user_data( $user_id )
{
	global $db;
	
	$sql = "SELECT user_id, username 
			FROM " . USERS_TABLE . "
			WHERE user_id = $user_id";

	$result = $db->sql_query($sql);
	$user_data = $db->sql_fetchrow($result);
	$db->sql_freeresult($result);
	
	if (!$user_data)
	{
		trigger_error('NO_FORUM');
	}

	return $user_data['username'];
}
?>