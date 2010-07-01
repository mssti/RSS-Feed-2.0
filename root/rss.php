<?php
/**
* @package: phpBB3 :: RSS feed 2.0
* @version: $Id: rss.php, v 1.0.4 2008/08/18 18:08:08 leviatan21 Exp $
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

/**
// FIX user - Start
$user_id	= request_var('uid', 0);
if ( $user_id != 0 )
{
	$user->session_create($user_id);
//	$user->data['user_id'] = $user_id;
}
// FIX user - End
**/

$auth->acl($user->data);
$user->setup();
$user->add_lang( array('common', 'acp/common', 'mods/rss') );

// Initial var setup
$board_url	= generate_board_url();

$sql		= '';
$error		= '';
$stats		= '';

$rss_f_id	= request_var('f', 0);
$rss_t_id	= request_var('t', 0);
$rss_mode	= request_var('mode', '');
$feed_mode	= empty($rss_mode) ? '' : $rss_mode ;

// Flood limits
$text_limit = $config['rss_characters'];
$show_text	= ( $text_limit == 1 ) ? false : true;

// Pagination
$start		= max(request_var('start', 0), 0);
$u_rss		= 'rss.' . $phpEx . ( empty($rss_mode) ? '' : '?mode='.$rss_mode ) . ( empty($rss_f_id) ? '' : '?f='.$rss_f_id ) . ( empty($rss_t_id) ? '' : ( empty($rss_f_id) ? '?t='.$rss_t_id : '&amp;t='.$rss_t_id ) );
$per_page	= $config['rss_limit'];
$total_count= 0;

// The SQL query selects the latest topics of all forum

switch ($rss_mode)
{
	case 'forums':
		$row_title	= 'forum_name';
		$row_creator= 'forum_last_poster_id';
		$row_text	= 'forum_desc';
		$row_bit	= 'forum_desc_bitfield';
		$row_uid	= 'forum_desc_uid';
		$row_date	= 'forum_last_post_time';

		// Get only forums, no cat, no links
		$sql = 'SELECT f.forum_id, f.forum_topics, f.forum_posts, f.forum_name, f.forum_last_poster_id, f.forum_desc, f.forum_desc_bitfield, f.forum_desc_uid, f.forum_last_post_time 
				FROM ' . FORUMS_TABLE . ' f 
				WHERE f.forum_type = ' . FORUM_POST . ' AND f.forum_last_post_id > 0 
				ORDER BY f.left_id';
		break;

	case 'topics':
		$show_text	= false;
		$row_title	= 'topic_title';
		$row_creator= 'topic_poster';
		$row_text	= '';
		$row_bit	= '';
		$row_uid	= '';
		$row_date	= 'topic_last_post_time';

		$sql = 'SELECT  f.forum_id, f.forum_name, 
						t.topic_title, t.topic_poster, t.topic_last_post_time, t.topic_id, t.topic_first_poster_name, t.topic_replies, t.topic_views, t.topic_time 
				FROM ' . FORUMS_TABLE  . ' f, ' . TOPICS_TABLE . ' t 
				WHERE t.topic_approved = 1 
					AND f.forum_id = t.forum_id 
				ORDER BY t.topic_last_post_time DESC';
		break;

	case 'posts':
		$row_title	= 'post_subject';
		$row_creator= 'poster_id';
		$row_text	= 'post_text';
		$row_bit	= 'bbcode_bitfield';
		$row_uid	= 'bbcode_uid';
		$row_date	= 'post_time';

		$sql = 'SELECT  f.forum_id, f.forum_name, 
						p.post_id, p.poster_id, p.post_time, p.post_subject, p.post_text, p.bbcode_bitfield, p.bbcode_uid, 
						u.username 
				FROM ' . FORUMS_TABLE  . ' f, ' . POSTS_TABLE . ' p, ' . USERS_TABLE . ' u 
				WHERE p.post_approved = 1 
					AND p.poster_id = u.user_id 
					AND f.forum_id = p.forum_id 
				ORDER BY p.post_time DESC';
		break;

	case 'egosearch':
		//check logged on
		if ($user->data['user_id'] == ANONYMOUS)
		{
			$error = 'egosearch';
			$error_desc = sprintf($user->lang['NO_RSS_ITEMS_LOGGED_IN'], $config['sitename'] );
		}

		$row_title	= 'post_subject';
		$row_creator= 'poster_id';
		$row_text	= 'post_text';
		$row_bit	= 'bbcode_bitfield';
		$row_uid	= 'bbcode_uid';
		$row_date	= 'post_time';

		$sql = 'SELECT  f.forum_id, f.forum_name, 
						p.post_id, p.poster_id, p.post_time, p.post_subject, p.post_text, p.bbcode_bitfield, p.bbcode_uid 
				FROM ' . POSTS_TABLE . ' p , ' . FORUMS_TABLE . ' f 
				WHERE p.poster_id =' . $user->data['user_id'] . ' 
					AND p.post_approved = 1 
					AND f.forum_id = p.forum_id 
				ORDER BY p.post_time DESC';
		break;

	default:
		$row_title	= 'post_subject';
		$row_creator= 'user_id';
		$row_text	= 'post_text';
		$row_bit	= 'bbcode_bitfield';
		$row_uid	= 'bbcode_uid';
		$row_date	= 'post_time';

		$forum_sql = ($rss_f_id == 0) ? '' : " AND f.forum_id = $rss_f_id";
		$topic_sql = ($rss_t_id == 0) ? ' AND p.post_id = t.topic_last_post_id ' : " AND p.topic_id = t.topic_id AND t.topic_id = $rss_t_id";
		$order_sql = (empty($topic_sql) ? 't.topic_last_post_time DESC' : 'p.post_time DESC');

		$sql = 'SELECT  f.forum_id, f.forum_name, 
						t.topic_title, t.topic_time, t.topic_replies, t.topic_views, 
						p.post_id, p.post_time, p.post_subject, p.post_text, p.bbcode_bitfield, p.bbcode_uid, 
						u.username, u.user_id 
				FROM ' . FORUMS_TABLE . ' f,' . TOPICS_TABLE . ' t,' . POSTS_TABLE . ' p,' . USERS_TABLE . ' u 
				WHERE t.forum_id = f.forum_id 
					AND t.topic_status != 1 AND t.topic_approved = 1 
					AND p.post_approved = 1 
 					AND u.user_id = p.poster_id' . $forum_sql . $topic_sql . ' 
				ORDER BY ' . (empty($topic_sql) ? 't.topic_last_post_time DESC' : 'p.post_time DESC');
		break;
}

// only return up to 100 ids (you can change it to the value that best suits your needs)
if ( $result = $db->sql_query_limit($sql, 100) )
{
	$forum_data = array();
	while ($row = $db->sql_fetchrow($result))
	{
		$forum_data[] = $row;
	}
	$db->sql_freeresult($result);
	$total_count = sizeof($forum_data);
	unset($forum_data);

	$result = $db->sql_query_limit($sql, $per_page, $start);
}

if(!$result || !$total_count )
{
	trigger_error($user->lang['NO_RSS_ITEMS'] . '<p>' . $user->lang['NO_RSS_ITEMS_EXPLAIN'] . '</p>');
}

// Okay, lets dump out the page ...
while ($row = $db->sql_fetchrow($result))
{
	//getting authentication
	if( $auth->acl_get('f_read', $row['forum_id']) )
	{
		switch ($rss_mode)
		{
			case 'forums':
				$feed_mode	= $user->lang['FORUMS'];
				
				$item_link	= append_sid("$board_url/{$phpbb_root_path}viewforum.$phpEx", "f={$row['forum_id']}");
				$stats		= sprintf($user->lang['TOTAL_TOPICS_OTHER'], $row['forum_topics']) . ' &bull; ' . sprintf($user->lang['TOTAL_POSTS_OTHER'], $row['forum_posts']);
				break;

			case 'topics':
				$feed_mode	= $user->lang['TOPICS'];
				
				$item_link	= append_sid("$board_url/{$phpbb_root_path}viewtopic.$phpEx", "f={$row['forum_id']}&amp;t={$row['topic_id']}");
				$user_link	= '<a href="' . append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=viewprofile&amp;u=" . $row['topic_poster']) . '">' . $row['topic_first_poster_name'] . '</a>';
				$stats		= $user->lang['POSTED'] . ' ' . $user->lang['POST_BY_AUTHOR'] . ' ' . $user_link . ' &bull; ' . $user->lang['POSTED_ON_DATE'] . ' ' . $user->format_date($row['topic_time']). ' &bull; ' . $user->lang['REPLIES'] . ' ' . $row['topic_replies'] . ' &bull; ' . $user->lang['VIEWS'] . ' ' . $row['topic_views'];
				break;

			case 'posts':
				$feed_mode	= $user->lang['POSTS'];
				
				$item_link	= append_sid("$board_url/{$phpbb_root_path}viewtopic.$phpEx", "p={$row['post_id']}#p{$row['post_id']}");
				$user_link	= '<a href="' . append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=viewprofile&amp;u=" . $row['poster_id']) . '">' . $row['username'] . '</a>';
				$stats		= $user->lang['POSTED'] . ' ' . $user->lang['POST_BY_AUTHOR'] . ' ' . $user_link . ' &bull; ' . $user->lang['POSTED_ON_DATE'] . ' ' . $user->format_date($row['post_time']);
				break;

			case 'egosearch':
				$feed_mode	= $user->lang['SEARCH_SELF'];

				$item_link	= append_sid("$board_url/{$phpbb_root_path}viewtopic.$phpEx", "p={$row['post_id']}#p{$row['post_id']}");
				break;

			default:
				$feed_mode	= ( ($rss_f_id) ? $user->lang['FORUMS'] .' > ' . $row['forum_name'] . ( ($rss_t_id) ? ' : ' . $user->lang['TOPICS'] . ' : ' . $row['topic_title'] : '' ) : '' );

				$item_link	= append_sid("$board_url/{$phpbb_root_path}viewtopic.$phpEx", "p={$row['post_id']}#p{$row['post_id']}");
				$user_link	= '<a href="' . append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=viewprofile&amp;u=" . $row['user_id']) . '">' . $row['username'] . '</a>';
				$stats		= $user->lang['POSTED'] . ' ' . $user->lang['POST_BY_AUTHOR'] . ' ' . $user_link . ' &bull; ' . $user->lang['POSTED_ON_DATE'] . ' ' . $user->format_date($row['topic_time']). ' &bull; ' . $user->lang['REPLIES'] . ' ' . $row['topic_replies'] . ' &bull; ' . $user->lang['VIEWS'] . ' ' . $row['topic_views'];
				break;
		}

		$template->assign_block_vars('items', array(
			'TITLE'			=> $row[$row_title],
			'LINK'			=> $item_link,
			'DESCRIPTION'	=> ( $row_text != '' && $show_text) ? generate_content($row[$row_text], $row[$row_uid], $row[$row_bit]) : '',
			'STATISTICS'	=> $user->lang['STATISTICS'] . ' : ' . $stats,
			'PUBDATE'		=> date3339($row[$row_date], $config['board_dst']),
			'CATEGORY'		=> "$board_url/viewforum.$phpEx?f={$row['forum_id']}",
			'CATEGORY_NAME'	=> utf8_htmlspecialchars($row['forum_name']),
			'CREATOR'		=> $row[$row_creator],
			'GUID'			=> $item_link,
		));
	}
}

// Output page
header("Content-Type: application/xml; charset=UTF-8");
// Do you need/want it ? , un comment it
//header("Last-Modified: " . date('D, d M Y H:i:s O', time()) );

// Set custom template for styles area
$template->set_custom_template($phpbb_root_path . 'styles', 'rss');
$template->assign_var('T_TEMPLATE_PATH', $phpbb_root_path . 'styles');

// the rss template is never stored in the database
$user->theme['template_storedb'] = false;

// Which timezone?
$tz = ($user->data['user_id'] != ANONYMOUS) ? strval(doubleval($user->data['user_timezone'])) : strval(doubleval($config['board_timezone']));

// Days since board start
$boarddays = (time() - $config['board_startdate']) / 86400;

$template->assign_vars(array(
	'FEED_TITLE'			=> $user->lang['ACP_RSS_MANAGEMENT'] . ' :: ' . $config['sitename'],
	'FEED_MODE'				=> ($rss_mode == 'egosearch') ? $user->lang['USERNAME'] .' : '. $user->data['username'] : $feed_mode,
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

/**
* Enter description here...
*
* @param string		$content
* @param int		$uid
* @param int		$bitfield
* @return string	
**/
function generate_content( $content, $uid, $bitfield )
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

		$content	= generate_truncate_content($content, $text_limit, $uid, false);

		// Fix smilies
		$content	= str_replace('{SMILIES_PATH}', "{$phpbb_root_path}{$config['smilies_path']}/", $content);

		// Fix local links
		$content	= str_replace('./', generate_board_url().'/', $content);

		$content	= generate_text_for_display($content, $uid, $bitfield,7);

		// Remove Comments from post content
//		$content	= preg_replace('#<!-- m --><a class=(.*?) href=(.*?)>(.*?)</a><!-- m -->#si','$2',$content);
		$content	= preg_replace('#<!-- ([lmwe]) --><a class=(.*?) href=(.*?)>(.*?)</a><!-- ([lmwe]) -->#si','$3',$content);

		// Remove Comments from smiles
		$content	= preg_replace('#<!-- s(.*?) -->(.*?)<!-- s(.*?) -->#si','$2',$content);

		// Remove embed and objects
		$content	= preg_replace( '#<(embed|object)(.*?) src=(.*?) ([^[]+)(embed|object)>#si',' <a href=$3 target="_blank"><strong>$1</strong></a> ',$content);

		// Remove some specials html tag, because somewhere there are a mod to allow html tags ;) 
		$content	= preg_replace( '#<script(.*?) ([^[]+)script>#si',' <strong>script</strong> ',$content);
		$content	= preg_replace( '#<iframe(.*?) ([^[]+)iframe>#si',' <strong>iframe</strong> ',$content);
		
		// Remove "Select all" link 
		$content	= str_replace('<a href="#" onclick="selectCode(this); return false;">' .$user->lang['SELECT_ALL_CODE'] . '</a>', '', $content);
	}

	$content = htmlspecialchars_decode($content);
	
//	$content = wordwrap(utf8_wordwrap($content), 140, "\n", true);

	return $content;
}

/**
* Truncates post text while retaining completes bbcodes tag, triying to not cut in between 
*
* @param string		$content		post text
* @param int		$text_limit		number of characters to get
* @param string		$uid			bbcode uid
* @param bolean		$recursive		call this function from inside this?
* @return string	$content
**/
function generate_truncate_content($content, $text_limit, $uid, $recursive = true )
{
	if ( strlen($content) < $text_limit )
	{
		return $content;
	}
	else
	{
		$end_content = ( !$recursive ? '...' : '' );
		
		$content = " " . $content;

		// First: if there isn't a "[" and a "]" in the message, don't bother, truncates string as it is
		if (! (strpos($content, "[") && strpos($content, "]")) )
		{
			return (preg_match('/^(.*)\W.*$/', substr($content, 0, $text_limit+1), $matches) ? $matches[1] : substr($content, 0, $text_limit)) . $end_content;
		//	return substr($content, 0 , $text_limit_blank) . $end_content ;
		}

		// So have at least 1 bbcode
		$str				= " " . str_replace("&quot;","'",$content);
		$curr_pos			= 0;
		$curr_length		= 0;
		$curr_text			= '';
		$next_text			= '';

		// Start at the 1st char of the string, looking for opening tags. Cutting the text in each space...
		while( ($curr_length < $text_limit) && ($curr_pos < $text_limit) )
		{
			$_word = split(' ', $str);

			// pad it with a space so we can distinguish between FALSE and matching the 1st char (index 0).
			$curr_word = (( $_word[0] != ' ') ? ' ' : '' ) . $_word[0];

			// current word/part carry a posible bbcode tag ?
			$the_first_open_bbcode = strpos($curr_word, "[");

			// if yes looks for the end of this bbcode tag
			if ( $the_first_open_bbcode !== false )
			{
				$the_first_open_bbcode = strpos($str, "[");
				$the_first_close_bbode = strpos($str, "]");

				if ( $the_first_open_bbcode > $the_first_close_bbode )
				{
					$the_first_open_bbcode = -1;
				}

				// Get the current bbcode, all between [??:??]
				$the_curr_bbcode_tag = substr($str, ($the_first_open_bbcode+1), (($the_first_close_bbode)-($the_first_open_bbcode+1)));

				// Now search for the end of the current bbcode tag, all between [/??:??]
				if ( (strpos($the_curr_bbcode_tag, " ") || strpos($the_curr_bbcode_tag, "=") || strpos($the_curr_bbcode_tag, ":")) )
				{
					list( $bbcode_tag, $garbage ) = split( '[ =:]', $the_curr_bbcode_tag );
					
					if ( $bbcode_tag == 'list' )
					{
						if ( strpos($the_curr_bbcode_tag, "=") )
						{
							$bbcode_tag = $bbcode_tag . ":o";
						}
						else
						{
							$bbcode_tag = $bbcode_tag . ":u";
						}
					}

					// little fix for a particular bbode :)
					if ( $bbcode_tag != 'tr' && $bbcode_tag != 'td')
					{
						$bbcode_tag .= ":" . $uid ;
					}
				}
				else
				{
					$bbcode_tag = $the_curr_bbcode_tag;
				}

				$the_curr_bbcode_tag_close = "[/" . $bbcode_tag . "]";

				// Is this a simple bbcode tag without a close bbcode [??:??] // like [tab=xx]
				// Or may be the user use the "[" and/or "]" for another propose...
				if ( strpos($str, $the_curr_bbcode_tag_close) === false )
				{
					$the_first_close_bbode = $the_first_close_bbode+1;
					$the_second_close_bbcode = $the_first_close_bbode;
				}
				else
				{
					$the_second_close_bbcode = strpos($str, $the_curr_bbcode_tag_close)+strlen($the_curr_bbcode_tag_close);
				}
				
				// Until here all works like expected, 
				// But sometimes the length is much longer as expected, because a bbcode can contain a lot of text, so try to do some magic :)
				$curr_length_until = strlen( $curr_text ) + strlen( substr($str, 0, $the_second_close_bbcode) );

				// Test if the future lenght is longer that the $text_limit 
				if ( ( $curr_length_until > $text_limit ) && !$recursive )
				{
					// Run me again but this time only with the current bbcode content, Can we do that ? :) Yes !
					$the_second_open_bbcode = strpos($str, "[");

					if ( $the_second_open_bbcode )
					{
						$curr_text .= " " . substr($str, 0, $the_second_open_bbcode);
						$str = substr($str, $the_second_open_bbcode);
					}

					$current_bbcode_content = substr( $str, strlen("[$the_curr_bbcode_tag]") );
					$current_bbcode_content = substr( $current_bbcode_content, 0, strpos($current_bbcode_content, $the_curr_bbcode_tag_close) );

					
					$next_text = "[" . $the_curr_bbcode_tag . "]" . generate_truncate_content($current_bbcode_content, ($text_limit-$curr_length), $uid, true) . $the_curr_bbcode_tag_close;
					
				}
				else
				{
					$next_text = substr($str, 0, $the_second_close_bbcode);
				}

				$curr_text .= $next_text;
				$curr_pos = strlen($curr_text);
			}
			else
			// current word is not a bbcode tag
			{
				$curr_text .= $curr_word;
				$curr_pos += strlen($curr_word);
			}

			$str = substr( $content, $curr_pos );

			// Count for words, without bbcodes, so get the real post length :)
			$curr_length = strlen( preg_replace( "#\[(.*?)\](.*?)\[(.*?)\]#is", '$2', $curr_text ) );
		}
		return $curr_text . $end_content;
	}
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

/**
* Try to resize a big image
*
* @param string 	$image_src		the image url
* @param int		$rss_imagesize	the max-width 
* @return html

function check_rss_imagesize( $image_src, $rss_imagesize )
{
	global $user;

	$dimension = @getimagesize($image_src);

	$width = '';
	if ( $dimension !== false || !empty($dimension[0]) )
	{
		if ( $dimension[0] > $rss_imagesize )
		{
			$width = 'width="' . $rss_imagesize . '" ';
		}
	}
	return '<img src="' . $image_src . '" alt="' . $user->lang['IMAGE'] . '" ' . $width . ' border="0" />';
}
**/
?>