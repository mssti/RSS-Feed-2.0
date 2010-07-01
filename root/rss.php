<?php
/**
* @package: phpBB3 :: RSS feed 2.0
* @version: $Id: rss.php, v 1.0.8 2009/01/22 09:01:22 leviatan21 Exp $
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
}
// FIX user - End
**/

$auth->acl($user->data);
$user->setup();
$user->add_lang( array('common', 'acp/common', 'mods/rss') );

// Initial var setup
$board_url	= generate_board_url();
$sql		= '';

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

// Apply filters
$f_exclude_ary = rss_filters();
$not_in_fid = (sizeof($f_exclude_ary)) ? "AND " . $db->sql_in_set('f.forum_id', $f_exclude_ary, true) : "";

$sql_array = array(
	'SELECT'	=> '',
	'FROM'		=> array(),
	'LEFT_JOIN'	=> array(),
);

// The SQL query selects the latest topics of all forum
switch ($rss_mode)
{
	case 'forums':
		// This option is forced here, only for a specific user request
		$config['rss_forums_topics'] = true;

		$row_title		= 'forum_name';
		$row_creator	= 'forum_last_poster_id';
		$row_username	= 'forum_last_poster_name';
		$row_text		= 'forum_desc';
		$row_bit		= 'forum_desc_bitfield';
		$row_uid		= 'forum_desc_uid';
		$row_date		= 'forum_last_post_time';

		$sql_array['SELECT'] = 'f.forum_id, f.forum_password, f.forum_topics, f.forum_posts, f.forum_name, f.forum_last_poster_id, f.forum_last_poster_name, f.forum_desc, f.forum_desc_bitfield, f.forum_desc_uid, f.forum_last_post_time, f.parent_id, f.left_id, f.right_id ';
		$sql_array['SELECT'].= ', u.username, u.user_id, u.user_email';
		$sql_array['FROM'][FORUMS_TABLE] = 'f';
		$sql_array['LEFT_JOIN'][] = array('FROM' => array(USERS_TABLE  => 'u'), 'ON' => 'f.forum_last_poster_id = u.user_id');
		$sql_array['WHERE'] = 'f.forum_type = ' . FORUM_POST . ' AND (f.forum_last_post_id > 0 ' . $not_in_fid . ')';
		$sql_array['ORDER_BY'] = 'f.left_id';

		break;

	case 'topics':
		$per_page		= $config['rss_overall_threads_limit'];

		$row_title		= 'topic_title';
		$row_title2		= 'forum_name';
		$row_creator	= 'topic_poster';
		$row_username	= 'topic_first_poster_name';
		$row_text		= 'post_text';
		$row_bit		= 'bbcode_bitfield';
		$row_uid		= 'bbcode_uid';
		$row_date		= 'topic_time'; //''topic_last_post_time';

		$sql_array['SELECT'] = 'f.forum_id, f.forum_password, f.forum_name, f.parent_id, f.left_id, f.right_id';
		$sql_array['SELECT'].= ', t.topic_title, t.topic_poster, t.topic_id, t.topic_first_post_id, t.topic_first_poster_name, t.topic_replies, t.topic_views, t.topic_time';
		$sql_array['SELECT'].= ', p.post_id, p.post_text, p.bbcode_bitfield, p.bbcode_uid';
		$sql_array['SELECT'].= ', u.username, u.user_id, u.user_email';
		$sql_array['FROM'][TOPICS_TABLE] = 't';
		$sql_array['LEFT_JOIN'][] = array('FROM' => array(FORUMS_TABLE => 'f'), 'ON' => 'f.forum_id = t.forum_id');
		$sql_array['LEFT_JOIN'][] = array('FROM' => array(POSTS_TABLE  => 'p'), 'ON' => 'p.post_id = t.topic_first_post_id');
		$sql_array['LEFT_JOIN'][] = array('FROM' => array(USERS_TABLE  => 'u'), 'ON' => 't.topic_poster = u.user_id');
		$sql_array['WHERE'] = 't.topic_approved = 1 AND f.forum_id = t.forum_id ';
		$sql_array['ORDER_BY'] = 't.topic_last_post_time DESC';

		break;

	case 'posts':
		$per_page		= $config['rss_overall_posts_limit'];

		$row_title		= 'post_subject';
		$row_title2		= 'forum_name';
		$row_creator	= 'poster_id';
		$row_username	= 'username';
		$row_text		= 'post_text';
		$row_bit		= 'bbcode_bitfield';
		$row_uid		= 'bbcode_uid';
		$row_date		= 'post_time';

		$sql_array['SELECT'] = 'f.forum_id, f.forum_password, f.forum_name, f.parent_id, f.left_id, f.right_id';
		$sql_array['SELECT'].= ', p.post_id, p.poster_id, p.post_time, p.post_subject, p.post_text, p.bbcode_bitfield, p.bbcode_uid';
		$sql_array['SELECT'].= ', u.username, u.user_id, u.user_email';
		$sql_array['FROM'][POSTS_TABLE] = 'p';
		$sql_array['LEFT_JOIN'][] = array('FROM' => array(FORUMS_TABLE => 'f'), 'ON' => 'f.forum_id = p.forum_id');
		$sql_array['LEFT_JOIN'][] = array('FROM' => array(USERS_TABLE  => 'u'), 'ON' => 'p.poster_id = u.user_id');
		$sql_array['WHERE'] = 'p.post_approved = 1';
		$sql_array['ORDER_BY'] = 'p.post_time DESC';

		break;

	// Force the feeds to read specified forums ?
	case 'news':
		$per_page		= $config['rss_overall_forums_limit'];

		if ( $config['rss_news_id'] !== '' )
		{
			$include_forums = explode(",", $config['rss_news_id'] );

			$row_title		= 'topic_title';
			$row_title2		= 'forum_name';
			$row_creator	= 'topic_poster';
			$row_username	= 'topic_first_poster_name';
			$row_text		= 'post_text';
			$row_bit		= 'bbcode_bitfield';
			$row_uid		= 'bbcode_uid';
			$row_date		= 'topic_time';

			$sql_array['SELECT'] = 'f.forum_id, f.forum_password, f.forum_name, f.forum_topics, f.forum_posts, f.parent_id, f.left_id, f.right_id';
			$sql_array['SELECT'].= ', t.topic_id, t.topic_title, t.topic_poster, t.topic_first_poster_name, t.topic_replies, t.topic_views, t.topic_time';
			$sql_array['SELECT'].= ', p.post_id, p.post_text, p.bbcode_bitfield, p.bbcode_uid';
			$sql_array['SELECT'].= ', u.username, u.user_id, u.user_email';
			$sql_array['FROM'][TOPICS_TABLE] = 't';
			$sql_array['LEFT_JOIN'][] = array('FROM' => array(FORUMS_TABLE => 'f'), 'ON' => 'f.forum_id = t.forum_id');
			$sql_array['LEFT_JOIN'][] = array('FROM' => array(POSTS_TABLE  => 'p'), 'ON' => 'p.post_id = t.topic_first_post_id');
			$sql_array['LEFT_JOIN'][] = array('FROM' => array(USERS_TABLE  => 'u'), 'ON' => 't.topic_poster = u.user_id');
			$sql_array['WHERE'] = $db->sql_in_set('t.forum_id', $include_forums);
			$sql_array['ORDER_BY'] = 't.topic_time DESC';
		}

		break;

	case 'egosearch':
		//check logged on
		if ($user->data['user_id'] == ANONYMOUS)
		{
			trigger_error($user->lang['NO_RSS_ITEMS'] . '<p>' . sprintf($user->lang['NO_RSS_ITEMS_LOGGED_IN'], $config['sitename'] ) . '</p>');
		}
		$per_page		= $config['rss_egosearch_limit'];

		$row_title		= 'post_subject';
		$row_title2		= 'forum_name';
		$row_creator	= 'poster_id';
	//	$row_username	= 'post_username';
		$row_text		= 'post_text';
		$row_bit		= 'bbcode_bitfield';
		$row_uid		= 'bbcode_uid';
		$row_date		= 'post_time';

		$sql_array['SELECT'] = 'f.forum_id, f.forum_password, f.forum_name, f.left_id, f.right_id, f.left_id, f.right_id';
		$sql_array['SELECT'].= ', p.post_id, p.poster_id, p.post_time, p.post_subject, p.post_text, p.bbcode_bitfield, p.bbcode_uid';
		$sql_array['FROM'][POSTS_TABLE] = 'p';
		$sql_array['LEFT_JOIN'][] = array('FROM' => array(FORUMS_TABLE => 'f'), 'ON' => 'f.forum_id = p.forum_id');
		$sql_array['WHERE'] = 'p.poster_id =' . $user->data['user_id'] . ' AND p.post_approved = 1';
		$sql_array['ORDER_BY'] = 'p.post_time DESC';

		break;

	default:
		$last_post_time_sql = '';
		$forum_sql = '';
		$topic_sql = '';
		$order_sql = '';

		$row_title		= 'post_subject';
		$row_title2		= 'topic_title';
		$row_creator	= 'user_id';
		$row_username	= 'username';
		$row_text		= 'post_text';
		$row_bit		= 'bbcode_bitfield';
		$row_uid		= 'bbcode_uid';
		$row_date		= 'post_time';

	//	$forum_sql = ($rss_f_id == 0) ? '' : " AND f.forum_id = $rss_f_id";
		$forum_sql = ($rss_f_id == 0) ? '' : " AND ( f.forum_id = $rss_f_id OR sf.forum_id = $rss_f_id )";

	//	$topic_sql = ($rss_t_id == 0) ? '' : " AND p.topic_id = t.topic_id AND t.topic_id = $rss_t_id";
		$topic_sql = ($rss_t_id == 0) ? ' AND p.post_id = t.topic_last_post_id ' : " AND p.topic_id = t.topic_id AND t.topic_id = $rss_t_id";

		$order_sql = (empty($topic_sql) ? 't.topic_last_post_time DESC' : 'p.post_time DESC');

		if ( !$forum_sql && !$topic_sql )
		{
			// Search for active topics in last 7 days
			$sort_days = request_var('st', 7);
		//	$min_time = ($sort_days) ? time() - ($sort_days * 86400) : 0;

			$last_post_time_sql	= ($sort_days) ? " AND t.topic_last_post_time > " . (time() - ($sort_days * 24 * 3600)) : '';
		//	$last_post_sql		= ($sort_days) ? " AND p.post_time >= $min_time" : '';
		}

		$sql_array['SELECT'] = 'f.forum_id, f.forum_password, f.forum_name, f.parent_id, f.left_id, f.right_id';
		$sql_array['SELECT'].= ', t.topic_last_post_time, t.topic_id, t.topic_title, t.topic_time, t.topic_replies, t.topic_views';
		$sql_array['SELECT'].= ', p.post_id, p.post_time, p.post_subject, p.post_text, p.bbcode_bitfield, p.bbcode_uid';
		$sql_array['SELECT'].= ', u.username, u.user_id, u.user_email';
		$sql_array['FROM'][POSTS_TABLE] = 'p';
		$sql_array['LEFT_JOIN'][] = array('FROM' => array(FORUMS_TABLE => 'f'), 'ON' => 'p.forum_id = f.forum_id');
		$sql_array['LEFT_JOIN'][] = array('FROM' => array(FORUMS_TABLE =>'sf'), 'ON' => 'f.left_id BETWEEN sf.left_id AND sf.right_id');
		$sql_array['LEFT_JOIN'][] = array('FROM' => array(TOPICS_TABLE => 't'), 'ON' => 'p.topic_id = t.topic_id');
		$sql_array['LEFT_JOIN'][] = array('FROM' => array(USERS_TABLE  => 'u'), 'ON' => 'p.poster_id = u.user_id');
		$sql_array['WHERE'] = 't.topic_moved_id = 0 ' . $forum_sql . $topic_sql . $last_post_time_sql;
		$sql_array['GROUP_BY'] = 'p.post_id';
		$sql_array['ORDER_BY'] = $order_sql;

		break;
}

$sql = $db->sql_build_query('SELECT', $sql_array);

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
//  For testing propose
//	trigger_error($user->lang['NO_RSS_ITEMS'] . '<p>' . $user->lang['NO_RSS_ITEMS_EXPLAIN'] . '</p> mode=('.$rss_mode.') <p>' . $sql .'</p>');
	trigger_error($user->lang['NO_RSS_ITEMS'] . '<p>' . $user->lang['NO_RSS_ITEMS_EXPLAIN'] . '</p>');
}

// Okay, lets dump out the page ...
while ($row = $db->sql_fetchrow($result))
{
	// Reset some data
	$feed_mode = $item_link = $user_link = $stats = '';

	switch ($rss_mode)
	{
		case 'forums':
			$feed_mode	= $user->lang['FORUMS'];

			$item_link	= rss_append_sid("$board_url/viewforum.$phpEx", "f={$row['forum_id']}");
			$stats		= sprintf($user->lang['TOTAL_TOPICS_OTHER'], $row['forum_topics']) . ' &bull; ' . sprintf($user->lang['TOTAL_POSTS_OTHER'], $row['forum_posts']);

			// Get and display all topics in this forum ?
			if ( $config['rss_forums_topics'] )
			{
				$forum_id	  = $row['forum_id'];
				$topic_titles = '';

				$topic_sql = "SELECT t.topic_id, t.topic_title 
					FROM " . TOPICS_TABLE . " t 
					WHERE t.forum_id = $forum_id 
					GROUP BY t.topic_id, t.topic_title";
				$topic_result = $db->sql_query($topic_sql);

				while ( $topic_row = $db->sql_fetchrow($topic_result) )
				{
					$topic_titles .= $topic_row['topic_title'] . "\r";
				}
				$row[$row_text] = $row[$row_text] . "<p>$topic_titles</p>";
				$db->sql_freeresult($topic_result);
			}
		break;

		case 'topics':
		case 'news':
			$feed_mode	= ( $rss_mode == 'news' ) ? $user->lang['RSS_NEWS'] : $user->lang['TOPICS'];
			$item_link	= rss_append_sid("$board_url/viewtopic.$phpEx", "p={$row['post_id']}#p{$row['post_id']}");

			$user_link	= '<a href="' . rss_append_sid("$board_url/memberlist.$phpEx", "mode=viewprofile&amp;u=" . $row['topic_poster']) . '">' . $row['topic_first_poster_name'] . '</a>';
			$stats		= $user->lang['POSTED'] . ' ' . $user->lang['POST_BY_AUTHOR'] . ' ' . $user_link . ' &bull; ' . $user->lang['POSTED_ON_DATE'] . ' ' . $user->format_date($row['topic_time']). ' &bull; ' . $user->lang['REPLIES'] . ' ' . $row['topic_replies'] . ' &bull; ' . $user->lang['VIEWS'] . ' ' . $row['topic_views'];
		break;

		case 'posts':
			$feed_mode	= $user->lang['POSTS'];
			$item_link	= rss_append_sid("$board_url/viewtopic.$phpEx", "p={$row['post_id']}#p{$row['post_id']}");

			$user_link	= '<a href="' . rss_append_sid("$board_url/memberlist.$phpEx", "mode=viewprofile&amp;u=" . $row['poster_id']) . '">' . $row['username'] . '</a>';
			$stats		= $user->lang['POSTED'] . ' ' . $user->lang['POST_BY_AUTHOR'] . ' ' . $user_link . ' &bull; ' . $user->lang['POSTED_ON_DATE'] . ' ' . $user->format_date($row['post_time']);
		break;

		case 'egosearch':
			$feed_mode	= $user->lang['SEARCH_SELF'];
			$item_link	= rss_append_sid("$board_url/viewtopic.$phpEx", "p={$row['post_id']}#p{$row['post_id']}");
		break;

		default:
			$row[$row_title] = $row['forum_name'] . " | " . (( $row[$row_title] ) ? $row[$row_title] : $row[$row_title2]);
			$feed_mode	= ( ($rss_f_id) ? $user->lang['FORUMS'] .' > ' . $row['forum_name'] . ( ($rss_t_id) ? ' : ' . $user->lang['TOPICS'] . ' : ' . $row['topic_title'] : '' ) : '' );
			$item_link	= rss_append_sid("$board_url/viewtopic.$phpEx", "p={$row['post_id']}#p{$row['post_id']}");

			$user_link	= '<a href="' . rss_append_sid("$board_url/memberlist.$phpEx", "mode=viewprofile&amp;u=" . $row['user_id']) . '">' . $row['username'] . '</a>';
			$stats		= $user->lang['POSTED'] . ' ' . $user->lang['POST_BY_AUTHOR'] . ' ' . $user_link . ' &bull; ' . $user->lang['POSTED_ON_DATE'] . ' ' . $user->format_date($row['topic_time']). ' &bull; ' . $user->lang['REPLIES'] . ' ' . $row['topic_replies'] . ' &bull; ' . $user->lang['VIEWS'] . ' ' . $row['topic_views'];
			break;
	}

	$template->assign_block_vars('items', array(
		'TITLE'			=> ( $row[$row_title] ) ? $row[$row_title] : $row[$row_title2],
		'LINK'			=> htmlspecialchars($item_link),
		'DESCRIPTION'	=> ( $row_text != '' && $show_text) ? generate_content($row[$row_text], $row[$row_uid], $row[$row_bit]) : '',
		'STATISTICS'	=> ( !$config['rss_items_statistics'] ) ? '' : $user->lang['STATISTICS'] . ' : ' . $stats,
		'PUBDATE'		=> ( !$config['rss_items_statistics'] ) ? '' : date2822(false, $row[$row_date]),
		'CATEGORY'		=> ( !$config['rss_items_statistics'] ) ? '' : "$board_url/viewforum.$phpEx?f={$row['forum_id']}",
		'CATEGORY_NAME'	=> ( !$config['rss_items_statistics'] ) ? '' : utf8_htmlspecialchars($row['forum_name']),
		'AUTHOR'		=> ( !$config['rss_items_statistics'] ) ? '' : ( (isset($row['user_email'])) ? $row['user_email'] : '' ) . ' (' .( (isset($row_username)) ? $row[$row_username] : $row[$row_creator] ) . ')',
		'GUID'			=> htmlspecialchars($item_link),
	));
}

// Set custom template for styles area
$template->set_custom_template($phpbb_root_path . 'styles', 'rss');

// the rss template is never stored in the database
$user->theme['template_storedb'] = false;

$template->assign_vars(array(
	'FEED_MODE'				=> ($rss_mode == 'egosearch') ? $user->lang['USERNAME'] .' : '. $user->data['username'] : $feed_mode,
	'FEED_TITLE'			=> $config['sitename'],
	'FEED_DESCRIPTION'		=> $config['site_desc'],
	'FEED_LINK'				=> "$board_url/index.$phpEx",
	'FEED_LANG'				=> $user->lang['USER_LANG'],
	'FEED_COPYRIGHT'		=> date('Y', $config['board_startdate']) . ' ' . $config['sitename'],
	'FEED_INDEX'			=> "$board_url/rss.$phpEx",
	'FEED_DATE'				=> date2822(true),
	'FEED_TIME'				=> date2822(),
	'FEED_MANAGING'			=> $config['board_email'] . " (" . $config['sitename'] . ")",
	'FEED_IMAGE'			=> $board_url . '/' . substr($user->img('site_logo', '', false, '', $type = 'src'),2),
	'FEED_TEXT'				=> $show_text,
));

// Is pagination enabled ?
if ( $config['rss_pagination'] )
{
	$template->assign_vars(array(
		'PAGINATION'		=> generate_pagination("$board_url/$u_rss", $total_count, $per_page, $start),
		'PAGE_NUMBER'		=> on_page($total_count, $per_page, $start),
	));	
}

// Is Board statistics enabled and runing the main the main board feed ?
if ( $config['rss_board_statistics'] && ( !$rss_mode && !$rss_f_id && !$rss_t_id ) )
{
	// Which timezone?
	$tz = ($user->data['user_id'] != ANONYMOUS) ? strval(doubleval($user->data['user_timezone'])) : strval(doubleval($config['board_timezone']));

	// Days since board start
	$boarddays = (time() - $config['board_startdate']) / 86400;

	$template->assign_vars(array(
		'S_STATISTICS'			=> true,
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
		'L_NEWEST_USER'			=> sprintf($user->lang['NEWEST_USER'], ''),
		'STAT_ONLINE_USERS'		=> sprintf($user->lang['RECORD_ONLINE_USERS'], $config['record_online_users'], $user->format_date($config['record_online_date'])),
		'STAT_NEWEST_USER'		=> $config['newest_username'],
	));	
}

// Output page

// application/xhtml+xml not used because of IE	//header("Content-Type: application/xhtml+xml; charset=UTF-8"); 
header('Content-type: application/rss+xml; charset=UTF-8');
/**
header("Content-type: text/xml");
echo '<?xml version="1.0" encoding="UTF-8"?>';
**/

// Do you need/want it ?, else comment it
header("Last-Modified: " . date2822() );

$template->set_filenames(array(
	'body' => 'rss_template.xml')
);

//page_footer();
$template->display('body');

garbage_collection();
exit_handler();

/******************************************************************************************************************************************/
/* Common functions                                                                                                                       */
/******************************************************************************************************************************************/

/**
* find out in which forums the user is allowed to view
* @return array with forum id
**/
function rss_filters()
{
	global $auth, $db, $config, $phpbb_root_path, $phpEx;

	include($phpbb_root_path . 'includes/functions_display.' . $phpEx);

	// Which forums should not be searched ?
	$exclude_forums	= explode(",", $config['rss_exclude_id'] );
	$f_id_ary 		= array();
//	$f_read_ary	= array_keys($auth->acl_getf('!f_read', true));
//	$fid_ary		= array_diff($exclude_forums, $f_read_ary);

	// Get some extra data for the excluded forums - Start
	$sql = 'SELECT forum_id, left_id, right_id 
			FROM ' . FORUMS_TABLE . ' 
			WHERE ' . $db->sql_in_set('forum_id', $exclude_forums) . ' 
			ORDER BY forum_id';
	$result = $db->sql_query($sql);

	while ($row = $db->sql_fetchrow($result))
	{
		$f_id_ary[] = array('forum_id' => $row['forum_id'], 'left_id' => $row['left_id'], 'right_id' => $row['right_id']);
	}
	$db->sql_freeresult($result);
	// Get some extra data for the excluded forums - End

	// Get all forums
	$sql = 'SELECT forum_id, forum_name, parent_id, left_id, right_id, forum_parents, forum_password, forum_type 
			FROM ' . FORUMS_TABLE . " 
			ORDER BY forum_id";
	$result = $db->sql_query($sql);

	$f_exclude_ary = array();
	while ($row = $db->sql_fetchrow($result))
	{
		$skip_id	= false;

		foreach ( $f_id_ary as $exclude_id => $exclude_data )
		{
			// First exclude for forums ID
			if ( $row['forum_id'] == $exclude_data['forum_id'] )
			{
				$f_exclude_ary[] = (int) $row['forum_id'];
				$skip_id = true;
				continue;
			}

			// Second exclude direct child of current branch
			if ( $row['parent_id'] == $exclude_data['forum_id'] )
			{
				$f_exclude_ary[] = (int) $row['forum_id'];
				$skip_id = true;
				continue;
			}

			// Then exclude child 
			if ( $row['forum_type'] == FORUM_POST && ($row['right_id'] < $exclude_data['right_id']) && ($row['left_id'] > $exclude_data['left_id']) )
			{
				$f_exclude_ary[] = (int) $row['forum_id'];
				$skip_id = true;
				continue;
			}

			// Getting authentication
			if ( $config['rss_permissions'] && !$auth->acl_get('f_read', $row['forum_id']) )
			{
				$f_exclude_ary[] = (int) $row['forum_id'];
				$skip_id = true;
				continue;
			}

			// Last skip paswored forums
			if ( $row['forum_password'] )
			{
				$f_exclude_ary[] = (int) $row['forum_id'];
				$skip_id = true;
				continue;
			}
		}

		if ( $skip_id )
		{
			continue;
		}
	}
	$db->sql_freeresult($result);

	return $f_exclude_ary;
}

/**
* Property build links 
*
* @param string $url The url the session id needs to be appended to (can have params)
* @param mixed $params String or array of additional url parameters
* @param bool $is_amp Is url using &amp; (true) or & (false)
* @param string $session_id Possibility to use a custom session id instead of the global one
*
* Examples:
* <code>
* append_sid("{$phpbb_root_path}viewtopic.$phpEx?t=1&amp;f=2");
* append_sid("{$phpbb_root_path}viewtopic.$phpEx", 't=1&amp;f=2');
* append_sid("{$phpbb_root_path}viewtopic.$phpEx", 't=1&f=2', false);
* append_sid("{$phpbb_root_path}viewtopic.$phpEx", array('t' => 1, 'f' => 2));
* </code>
*
* Code based off root/includes/function.php -> reapply_sid()
**/
function rss_append_sid($url, $params)
{
	$rss_link = append_sid($url, $params, false);

	// Remove added sid
	if ( strpos($rss_link, 'sid=') !== false )
	{
		$rss_link = preg_replace('/(&amp;|&|\?)sid=[a-z0-9]+(&amp;|&)?/', '\1', $rss_link);
	}
	return $rss_link;
}

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
	global $text_limit, $show_text, $board_url;
	global $user, $config, $phpbb_root_path, $phpEx;

	if ( $show_text && !empty($content) )
	{
		// Remove Comments from smiles
		$content	= smiley_text($content);

		// Truncates post text ?
		$content	= generate_truncate_content($content, $text_limit, $uid, false );

		// Prepare some bbcodes for better parsing
		$content	= preg_replace("#\[quote(=&quot;.*?&quot;)?:$uid\]\s*(.*?)\s*\[/quote:$uid\]#si", "[quote$1:$uid]<br />$2<br />[/quote:$uid]", $content);

		/* Just remember : Never use it !
		* Commented out so I do not make the same error twice.
		$content	= html_entity_decode($content);
		*/

		// Parse it!
		$content	= generate_text_for_display($content, $uid, $bitfield, 7);

		// Fix smilies
		$content	= str_replace('{SMILIES_PATH}/', "{$phpbb_root_path}{$config['smilies_path']}/", $content);

		// Relative Path to Absolute path, Windows style
		$content	= str_replace('./', "$board_url/", $content);

		// Fix some spaces
		$content	= preg_replace('#<a(.*?)>(.*?)</a>#si',' <a$1>$2</a> ', $content);
		$content	= bbcode_nl2br($content);

		// Remove "Select all" link and mouse events
		$content	= str_replace('<a href="#" onclick="selectCode(this); return false;">' .$user->lang['SELECT_ALL_CODE'] . '</a>', '', $content);
		$content	= preg_replace('#(onkeypress|onclick)="(.*?)"#si', '', $content);

		// Remove Comments from post content
		$content	= preg_replace('#<!-- ([lmwe]) --><a class=(.*?) href=(.*?)>(.*?)</a><!-- ([lmwe]) -->#si', '$3', $content);

		// Remove embed Windows Media Streams
		$content	= preg_replace( '#<\!--\[if \!IE\]>-->([^[]+)<\!--<!\[endif\]-->#si', '', $content);

		// Remove embed and objects
		// Use (<|&lt;) and (>|&gt;) because can be contained into [code][/code]
		$content	= preg_replace( '#(<|&lt;)(object|embed)(.*?) (value|src)=(.*?) ([^[]+)(object|embed)(>|&gt;)#si',' <a href=$5 target="_blank"><strong>$2</strong></a> ',$content);

		// Remove some specials html tag, because somewhere there are a mod to allow html tags ;)
		// Use (<|&lt;) and (>|&gt;) because can be contained into [code][/code]
		$content	= preg_replace( '#(<|&lt;)script([^[]+)script(>|&gt;)#si', ' <strong>Script</strong> ', $content);
		$content	= preg_replace( '#(<|&lt;)iframe([^[]+)iframe(>|&gt;)#si', ' <strong>Iframe</strong> ', $content);

		// Remove Comments from inline attachments [ia]
		$content	= preg_replace('#<div class="(inline-attachment|attachtitle)">(.*?)<!-- ia(.*?) -->(.*?)<!-- ia(.*?) -->(.*?)</div>#si','$4',$content);

		// Resize images ?
		if ( $config['rss_image_size'] )
		{
			$content	= preg_replace('#<img src=\"(.*?)\" alt=(.*?)/>#ise', "check_rss_imagesize( '$1' )", $content);
		}

		/* Convert special HTML entities back to characters
		* Some languages will need it
		* Commented out so I do not loose the code.
		$content = htmlspecialchars_decode($content);
		*/

		// Other control characters
		$content = preg_replace('#(?:[\x00-\x1F\x7F]+|(?:\xC2[\x80-\x9F])+)#', '', $content);
	}

	/* Just remember : Never use it !
	* Commented out so I do not make the same error twice.
	$content = htmlspecialchars($content);
	*/
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
	global $phpbb_root_path, $config;

	// Check lenght
	$text_limit = ( $text_limit == 0 ) ? strlen($content)+1 : $text_limit;

	if ( strlen($content) < $text_limit )
	{
		return $content;
	}
	else
	{
		$end_content = ( !$recursive ? '<br />...' : '' );

		$content = " " . $content;
		// Change " with '
		$str				= " " . str_replace("&quot;", "'", $content);
		$curr_pos			= 0;
		$curr_length		= 0;
		$curr_text			= '';
		$next_text			= '';

		// Start at the 1st char of the string, looking for opening tags. Cutting the text in each space...
		while( $curr_length < $text_limit )
		{
			$_word = split(' ', $str);

			// pad it with a space so we can distinguish between FALSE and matching the 1st char (index 0).
			$curr_word = (( $_word[0] != ' ') ? ' ' : '' ) . $_word[0];
			
			// current word/part carry a posible bbcode tag ?
			$the_first_open_bbcode = strpos($curr_word, "[");

			// fix for smiles, make sure always are completes
			if ( strpos($curr_word, "<img") )
			{
				$smile_open		= strpos( $str, '<img src="' . $phpbb_root_path . $config['smilies_path'] . '/');
				$smile_close	= strpos( $str, " />", $smile_open );
				$curr_word	= substr( $str, 0, $smile_close+3 );
				$the_first_open_bbcode = false;
			}

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

					$bbcode_tag = str_replace('/','', $bbcode_tag);

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

					if ( $bbcode_tag == '*')
					{
							$bbcode_tag = $bbcode_tag . ":m";
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
 *Get date in RFC2822 format
*
* @param $forced	bool 	force time to 0 
* @param $timestamp	integer	the time
* @param $timezone	integer	the time zone
* @return string	string	date in RFC2822 format
* Code based off : http://cyber.law.harvard.edu/rss/rss.html#requiredChannelElements
**/
function date2822( $forced = false, $timestamp = 0, $timezone = 0 )
{
	global $config;

	// Local differential hours+min. (HHMM) ( ("+" / "-") 4DIGIT ); 
	$timezone  = ( $timezone ) ? $timezone   : $config['board_timezone'];
	$timezone  = ( $timezone > 0 ) ? '+' . $timezone : $timezone;
	$tz = $tzhour = $tzminutes = $tzsep = '';

	$matches = array();
	if ( preg_match('/^([\-+])?([0-9]+)?(\.)?([0-9]+)?$/', $timezone, $matches) )
	{
		$tz			= isset($matches[1] ) ? $matches[1] : $tz;
		$tzhour		= isset($matches[2] ) ? str_pad($matches[2], 2, "0", STR_PAD_LEFT) : $tzhour;
	//	$tzsep		= isset($matches[3] ) ? $matches[3] : $tzsep;
		$tzminutes	= isset($matches[4] ) ? ( ( $matches[4] == '75' ) ? '45' : '30' ) : '00';
		$timezone	= $tz . $tzhour . $tzminutes;
	}
	$timezone  = ( (int) $timezone == 0 ) ? 'GMT' : $timezone;

	$date_time = ( $timestamp ) ? $timestamp : time();
	$date_time = ( $forced ) ? date('D, d M Y 00:00:00', $date_time) : date('D, d M Y H:i:s', $date_time);

	return $date_time . ' ' . $timezone;
}

/**
* Try to resize a big image
*
* @param string 	$image_src		the image url
* @param int		$rss_imagesize	the max-width 
* @return html
**/
function check_rss_imagesize( $image_src, $image_size = 0 )
{
	global $user, $config;

	$rss_imagesize	= ( $image_size ) ? $image_size : $config['img_link_width'];
	$rss_imagesize	= ( $image_size ) ? $image_size : $config['img_max_width'];
	$rss_imagesize	= ( $image_size ) ? $image_size : $config['img_max_thumb_width'];
	$rss_imagesize	= ( $image_size ) ? $image_size : 200;
	$width			= '';

	// check image with timeout to ensure we don’t wait quite long
	$timeout = 5;
	$old = ini_set('default_socket_timeout', $timeout);

	if( $dimension = @getimagesize($image_src) )
	{
		if ( $dimension !== false || !empty($dimension[0]) )
		{
			if ( $dimension[0] > $rss_imagesize )
			{
				$width = 'width="' . $rss_imagesize . '" ';
			}
		}
	}

	ini_set('default_socket_timeout', $old);
	return '<img src="' . $image_src . '" alt="' . $user->lang['IMAGE'] . '" ' . $width . ' border="0" />';
}

?>