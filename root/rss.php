<?php
/**
* @package: phpBB3 :: RSS feed 2.0
* @version: $Id: rss.php, v 1.2.0 2009/04/10 10:04:09 leviatan21 Exp $
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
define('RSS_DEBUG_MODE', false);
define('RSS_DEBUG_SQL', false);

$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);

// Start session
$user->session_begin();

/** FIX user - Start **/
$user_id	= request_var('uid', 0);
if ( $user_id != 0 )
{
	$user->session_create($user_id);
}
/** FIX user - END **/

$auth->acl($user->data);
$user->setup();
$user->add_lang( array('common', 'acp/common', 'mods/rss') );

if (!$config['rss_enable'])
{
	trigger_error('NO_RSS_ENABLED');
}

// Initialize RSS object
$rssfeed = new phpbb_rssfeed();

// Initialize default values
$rssfeed->get_ini();

// Build the sql
$rssfeed->get_sql();

// Run the sql
$rssfeed_result = $rssfeed->get_feed();

if( !$rssfeed_result )
{
	$template->assign_block_vars('items', array(
		'DESCRIPTION' => $user->lang['NO_RSS_ITEMS_EXPLAIN'],
	));
}
// If we are here means that all is fine,
else
{
	// Okay, lets dump out the page ...
	for ($i = 0, $items = sizeof($rssfeed_result); $i < $items; $i++)
	{
		$item_row = $rssfeed->adjust_item($rssfeed_result[$i]);

		$template->assign_block_vars('items', array(
			'TITLE'			=> html_entity_decode(utf8_htmlspecialchars($item_row['TITLE'])),
			'LINK'			=> utf8_htmlspecialchars($item_row['LINK']),
			'DESCRIPTION'	=> $item_row['TEXT'],
			'STATISTICS'	=> ( !$config['rss_items_statistics'] || !$item_row['STATS']		) ? '' : $user->lang['STATISTICS'] . ' : ' . $item_row['STATS'],
			'PUBDATE'		=> ( !$config['rss_items_statistics'] || !$item_row['DATE']			) ? '' : $rssfeed->date2822(false, $item_row['DATE']),
			'CATEGORY'		=> ( !$config['rss_items_statistics'] || !$item_row['CATEGORY']		) ? '' : utf8_htmlspecialchars($item_row['CATEGORY']),
			'CATEGORY_NAME'	=> ( !$config['rss_items_statistics'] || !$item_row['CATEGORY_NAME']) ? '' : html_entity_decode($item_row['CATEGORY_NAME']),
			'AUTHOR'		=> ( !$config['rss_items_statistics'] || !$item_row['AUTHOR']		) ? '' : utf8_htmlspecialchars($item_row['AUTHOR']),
			'GUID'			=> utf8_htmlspecialchars($item_row['LINK']),
		));
	}
}

// Output page
$rssfeed->close_feed();

/******************************************************************************************************************************************
* RSS class  for grabbing/handling feed entries
* @package rss
******************************************************************************************************************************************/
class phpbb_rssfeed
{
	// SQL Query to be executed to get feed items
	var $sql			= '';
	var $sql_limit		= 0;

	// Default setting for last x days
	var $sort_days = 7;

	// Reset some data
	var $row_title		= '';
	var $row_title2		= '';
	var $row_creator	= '';
	var $row_username	= '';
	var $row_text		= '';
	var $row_bit		= '';
	var $row_uid		= '';
	var $row_date		= '';

	/**
	* Initiealize some basic values
	**/
	function get_ini()
	{
		global $user, $user_id, $db, $config, $phpEx;

		// Initial var setup
		$this->board_url	= generate_board_url();

		$this->rss_f_id		= request_var('f', 0);
		$this->rss_t_id		= request_var('t', 0);
		$this->rss_p_id		= request_var('p', 0);
		$this->lang_mode	= '';
		$this->rss_mode		= request_var('mode', '');

		// Flood limits
		$this->text_limit	= $config['rss_characters'];
		$this->show_text	= ( $this->text_limit == 1 ) ? false : true;

		// Search for active topics in last 7 days
		$this->sort_days	= request_var('st', $this->sort_days);

		// Number of items to fetch
		$this->sql_limit	= $config['rss_limit'];

		$this->u_rss		= 'rss.' . $phpEx . ( empty($this->rss_mode) ? '' : '?mode='.$this->rss_mode ) . ( empty($this->rss_f_id) ? '' : '?f='.$this->rss_f_id ) . ( empty($this->rss_t_id) ? '' : ( empty($this->rss_f_id) ? '?t='.$this->rss_t_id : '&amp;t='.$this->rss_t_id ) );

		if ( $user_id != 0 )
		{
			$this->u_rss	.= '&amp;uid=' . $user_id;
		}

		if ( DEBUG && RSS_DEBUG_MODE )
		{
			$starttime = explode(' ', microtime());
			$this->starttime = $starttime[1] + $starttime[0];
		}
	}

	/**
	* Return correct object for specified mode
	*
	* @return object	Returns correct RSS object for specified mode.
	**/
	function get_sql()
	{
		global $user, $user_id, $db, $config, $phpEx;

		$sql_array = array(
			'SELECT'	=> '',
			'FROM'		=> array(),
			'WHERE'		=> '',
		);

		switch ($this->rss_mode)
		{
			case 'forums':
				// Check if this option is enabled
				if ( $config['rss_overall_forums'] )
				{
					// Adjust the amount of items to display 
					$this->sql_limit	= $config['rss_overall_forums_limit'];

					// This option is forced here, only for a specific user request
					$config['rss_forums_topics'] = true;

					$this->row_title	= 'forum_name';
					$this->row_creator	= 'forum_last_poster_id';
					$this->row_username	= 'forum_last_poster_name';
					$this->row_text		= 'forum_desc';
					$this->row_bit		= 'forum_desc_bitfield';
					$this->row_uid		= 'forum_desc_uid';
					$this->row_date		= 'forum_last_post_time';

					$sql_array['SELECT'] = 'f.forum_id, f.forum_status, f.forum_password, f.forum_topics, f.forum_posts, f.forum_name, f.forum_last_poster_id, f.forum_last_poster_name, f.forum_desc, f.forum_desc_bitfield, f.forum_desc_uid, f.forum_last_post_time, f.parent_id, f.left_id, f.right_id ';
					$sql_array['FROM'][FORUMS_TABLE] = 'f';
					$sql_array['WHERE'] = 'f.forum_type = ' . FORUM_POST . ' AND f.forum_last_post_id > 0 ';
					$sql_array['ORDER_BY'] = 'f.left_id';
				}
			break;

			case 'topics':
				// Check if this option is enabled
				if ( $config['rss_overall_threads'] )
				{
					// Adjust the amount of items to display 
					$this->sql_limit	= $config['rss_overall_threads_limit'];

					$this->row_title	= 'topic_title';
					$this->row_title2	= 'forum_name';
					$this->row_creator	= 'topic_poster';
					$this->row_text		= 'post_text';
					$this->row_bit		= 'bbcode_bitfield';
					$this->row_uid		= 'bbcode_uid';
					$this->row_date		= 'topic_time';

					$sql_array['SELECT'] = 'f.forum_id, f.forum_status, f.forum_password, f.forum_name, f.forum_topics, f.forum_posts, f.parent_id, f.left_id, f.right_id';
					$sql_array['SELECT'].= ', t.topic_id, t.topic_status, t.topic_title, t.topic_poster, t.topic_first_poster_name, t.topic_replies, t.topic_views, t.topic_time';
					$sql_array['SELECT'].= ', p.post_id, p.post_text, p.bbcode_bitfield, p.bbcode_uid, p.post_attachment';
					$sql_array['SELECT'].= ', u.username, u.user_id, u.user_email, u.user_allow_viewemail';
					$sql_array['FROM'][FORUMS_TABLE] = 'f';
					$sql_array['FROM'][TOPICS_TABLE] = 't';
					$sql_array['FROM'][POSTS_TABLE]  = 'p';
					$sql_array['FROM'][USERS_TABLE]  = 'u';
					$sql_array['WHERE'] = 't.topic_approved = 1 AND ( f.forum_id = t.forum_id AND p.topic_id = t.topic_id AND u.user_id = t.topic_poster )';
				//	$sql_array['GROUP_BY'] = 't.topic_id';
					$sql_array['ORDER_BY'] = 't.topic_last_post_time DESC';
				}
			break;

			case 'posts':
				// Check if this option is enabled
				if ( $config['rss_overall_posts'] )
				{
					// Adjust the amount of items to display 
					$this->sql_limit	= $config['rss_overall_posts_limit'];

					$this->row_title	= 'post_subject';
					$this->row_title2	= 'forum_name';
					$this->row_creator	= 'poster_id';
					$this->row_text		= 'post_text';
					$this->row_bit		= 'bbcode_bitfield';
					$this->row_uid		= 'bbcode_uid';
					$this->row_date		= 'post_time';

					$sql_array['SELECT'] = 'f.forum_id, f.forum_status, f.forum_password, f.forum_name, f.parent_id, f.left_id, f.right_id';
					$sql_array['SELECT'].= ', p.post_id, p.poster_id, p.post_time, p.post_subject, p.post_text, p.bbcode_bitfield, p.bbcode_uid, p.post_attachment';
					$sql_array['SELECT'].= ', u.username, u.user_id, u.user_email, u.user_allow_viewemail';
					$sql_array['FROM'][FORUMS_TABLE] = 'f';
					$sql_array['FROM'][POSTS_TABLE]  = 'p';
					$sql_array['FROM'][USERS_TABLE]  = 'u';
					$sql_array['WHERE'] = 'p.post_approved = 1 AND f.forum_id = p.forum_id AND u.user_id = p.poster_id ';
					$sql_array['ORDER_BY'] = 'p.post_time DESC';
				}
			break;

			case 'newposts':
				// Check if this option is enabled
				$config['rss_newposts'] = true;
				if ( $config['rss_newposts'] /**&& $user->data['user_id'] != ANONYMOUS **/)
				{
					$include_forums = explode(",", $config['rss_news_id'] );

					$this->row_title	= 'topic_title';
					$this->row_title2	= 'forum_name';
					$this->row_creator	= 'topic_poster';
					$this->row_text		= 'post_text';
					$this->row_bit		= 'bbcode_bitfield';
					$this->row_uid		= 'bbcode_uid';
					$this->row_date		= 'topic_time';

					// Search for active topics
					$last_post_time_sql	= ( $user->data['user_id'] != ANONYMOUS ) ? $user->data['user_lastvisit'] :  (time() - ($this->sort_days * 24 * 3600));

					$sql_array['SELECT'].= 'f.forum_id, f.forum_status, f.forum_password, f.forum_name, f.forum_topics, f.forum_posts, f.parent_id, f.left_id, f.right_id';
					$sql_array['SELECT'].= ', t.topic_id, t.topic_status, t.topic_title, t.topic_poster, t.topic_first_poster_name, t.topic_replies, t.topic_views, t.topic_time, t.topic_approved, t.topic_moved_id, t.topic_last_post_time';
					$sql_array['SELECT'].= ', p.post_id, p.post_text, p.bbcode_bitfield, p.bbcode_uid, p.post_attachment, p.post_time';
					$sql_array['SELECT'].= ', u.username, u.user_id, u.user_email, u.user_allow_viewemail';
					$sql_array['FROM'][FORUMS_TABLE] = 'f';
					$sql_array['FROM'][TOPICS_TABLE] = 't';
					$sql_array['FROM'][POSTS_TABLE]  = 'p';
					$sql_array['FROM'][USERS_TABLE]  = 'u';
					$sql_array['WHERE'] = 't.topic_last_post_time > ' . $last_post_time_sql;
					$sql_array['WHERE'].= ' AND t.topic_moved_id = 0';
					$sql_array['WHERE'].= ' AND t.topic_approved = 1 ';
					$sql_array['WHERE'].= ' AND ( f.forum_id = t.forum_id AND p.topic_id = t.topic_id  AND u.user_id = t.topic_poster )';
				//	$sql_array['GROUP_BY'] = 't.topic_id';
					$sql_array['ORDER_BY'] = 't.topic_last_post_time DESC';
				}
			break;

			// Force the feeds to read specified forums ?
			case 'news':
				// Check if this option is enabled
				if ( $config['rss_news_id'] !== '' )
				{
					$include_forums = explode(",", $config['rss_news_id'] );

					$this->row_title	= 'topic_title';
					$this->row_title2	= 'forum_name';
					$this->row_creator	= 'topic_poster';
					$this->row_text		= 'post_text';
					$this->row_bit		= 'bbcode_bitfield';
					$this->row_uid		= 'bbcode_uid';
					$this->row_date		= 'topic_time';

					$sql_array['SELECT'] = 'f.forum_id, f.forum_status, f.forum_password, f.forum_name, f.forum_topics, f.forum_posts, f.parent_id, f.left_id, f.right_id';
					$sql_array['SELECT'].= ', t.topic_id, t.topic_status, t.topic_title, t.topic_poster, t.topic_first_poster_name, t.topic_replies, t.topic_views, t.topic_time';
					$sql_array['SELECT'].= ', p.post_id, p.post_text, p.bbcode_bitfield, p.bbcode_uid, p.enable_bbcode, p.enable_smilies, p.enable_magic_url, p.post_attachment';
					$sql_array['SELECT'].= ', u.username, u.user_id, u.user_email, u.user_allow_viewemail';
					$sql_array['FROM'][FORUMS_TABLE] = 'f';
					$sql_array['FROM'][TOPICS_TABLE] = 't';
					$sql_array['FROM'][POSTS_TABLE]  = 'p';
					$sql_array['FROM'][USERS_TABLE]  = 'u';
					$sql_array['WHERE'] = $db->sql_in_set('t.forum_id', $include_forums);
					$sql_array['WHERE'].= ' AND ( f.forum_id = t.forum_id AND p.post_id = t.topic_first_post_id  AND u.user_id = t.topic_poster ) ';
					$sql_array['ORDER_BY'] = 't.topic_time DESC';
				}
			break;

			case 'egosearch':
				if ( $config['rss_egosearch'] )
				{
					//check logged on
					if ( $user->data['user_id'] == ANONYMOUS )
					{
						trigger_error($user->lang['NO_RSS_ITEMS'] . '<p>' . sprintf($user->lang['NO_RSS_ITEMS_LOGGED_IN'], $config['sitename'] ) . '</p>');
					}

					// Adjust the amount of items to display 
					$this->sql_limit	= $config['rss_egosearch_limit'];

					$this->row_title	= 'post_subject';
					$this->row_title2	= 'forum_name';
					$this->row_creator	= 'poster_id';
					$this->row_text		= 'post_text';
					$this->row_bit		= 'bbcode_bitfield';
					$this->row_uid		= 'bbcode_uid';
					$this->row_date		= 'post_time';

					$sql_array['SELECT'] = 'f.forum_id, f.forum_status, f.forum_password, f.forum_name, f.left_id, f.right_id, f.left_id, f.right_id';
					$sql_array['SELECT'].= ', p.post_id, p.poster_id, p.post_time, p.post_subject, p.post_text, p.bbcode_bitfield, p.bbcode_uid, p.post_attachment';
					$sql_array['SELECT'].= ', u.username, u.user_id, u.user_email, u.user_allow_viewemail';
					$sql_array['FROM'][FORUMS_TABLE] = 'f';
					$sql_array['FROM'][POSTS_TABLE]  = 'p';
					$sql_array['FROM'][USERS_TABLE]  = 'u';
					$sql_array['WHERE'] = 'p.poster_id =' . $user->data['user_id'] . ' AND p.post_approved = 1 AND u.user_id = p.poster_id ';
					$sql_array['WHERE'].= ' AND f.forum_id = p.forum_id ';
					$sql_array['ORDER_BY'] = 'p.post_time DESC';
				}
			break;

			case 'attachments':
				// Check if this option is enabled
				if ( $config['rss_attach'] )
				{
					$this->row_title	= 'forum_name';
					$this->row_title2	= 'real_filename';
					$this->row_creator	= 'poster_id';
					$this->row_text		= 'attach_comment';
					$this->row_bit		= 'bbcode_bitfield';
					$this->row_uid		= 'bbcode_uid';
					$this->row_date		= 'filetime';

					$sql_array['SELECT'] = 'f.forum_id, f.forum_status, f.forum_name, t.*, p.*, a.*, u.username, u.user_id, u.user_email, u.user_allow_viewemail';
					$sql_array['FROM'][FORUMS_TABLE] = 'f';
					$sql_array['FROM'][TOPICS_TABLE] = 't';
					$sql_array['FROM'][POSTS_TABLE]  = 'p';
					$sql_array['FROM'][ATTACHMENTS_TABLE] = 'a';
					$sql_array['FROM'][USERS_TABLE]  = 'u';
					$sql_array['WHERE'] = 'p.post_attachment = 1 AND a.post_msg_id = p.post_id AND t.topic_id = p.topic_id AND f.forum_id = p.forum_id AND u.user_id = p.poster_id '; 
					$sql_array['ORDER_BY'] = 'LOWER(a.filetime) DESC';
				}
			break;

			default:
				// Check if this option is enabled
				if ( $config['rss_enable'] || $config['rss_forum'] || $config['rss_thread'] )
				{
					$last_post_time_sql = '';
					$forum_sql	= '';
					$topic_sql	= '';
					$post_sql	= '';
					$order_sql	= '';
					$group_by	= 'p.post_id';

					$this->row_title	= 'post_subject';
					$this->row_title2	= 'topic_title';
					$this->row_creator	= 'poster_id';
					$this->row_text		= 'post_text';
					$this->row_bit		= 'bbcode_bitfield';
					$this->row_uid		= 'bbcode_uid';
					$this->row_date		= 'post_time';

					// Check if this option is enabled
					if ( !$config['rss_forum'] && $this->rss_f_id != 0 && $this->rss_t_id == 0 )
					{
						break;
					}

					if ( $this->rss_f_id != 0 )
					{
						$group_by .= ', f.forum_id';

						// Determine forum childs...
						$sql = 'SELECT sf.forum_id 
							FROM ' . FORUMS_TABLE . ' f, ' . FORUMS_TABLE . ' sf
							WHERE f.forum_id = ' . $this->rss_f_id . '
								AND ( sf.left_id BETWEEN f.left_id AND f.right_id )';
						$result = $db->sql_query($sql);

						$forum_ids = array();
						while ($row = $db->sql_fetchrow($result))
						{
							$forum_ids[] = $row['forum_id'];
						}
						$db->sql_freeresult($result);

						$forum_sql = " AND " . $db->sql_in_set('p.forum_id', $forum_ids);
					}

					// Check if this option is enabled
					if ( !$config['rss_thread'] && $this->rss_t_id != 0 )
					{
						break;
					}

					if ( $this->rss_t_id == 0 )
					{
						$topic_sql = " AND p.post_id = t.topic_last_post_id ";
					}
					else
					{
						$topic_sql = " AND p.topic_id = t.topic_id AND t.topic_id = $this->rss_t_id";
						$group_by .= ', p.topic_id';
					}

					$order_sql = (empty($topic_sql) ? 't.topic_last_post_time DESC' : 'p.post_time DESC');

					// Search for active topics
					if ( $this->rss_f_id == 0 && $this->rss_t_id == 0)
					{
						$last_post_time_sql	= " AND t.topic_last_post_time > " . (time() - ($this->sort_days * 24 * 3600));
					}

					if ( $this->rss_p_id != 0 )
					{
						$post_sql = " AND p.post_id = $this->rss_p_id";
						$last_post_time_sql	= '';
					}

					$sql_array['SELECT'] = 'f.forum_id, f.forum_status, f.forum_password, f.forum_name, f.parent_id, f.left_id, f.right_id' ;
					$sql_array['SELECT'].= ', t.topic_id, t.topic_status, t.topic_last_post_time, t.topic_title, t.topic_time, t.topic_replies, t.topic_views';
					$sql_array['SELECT'].= ', p.post_id, p.topic_id, p.poster_id, p.post_time, p.post_subject, p.post_text, p.bbcode_bitfield, p.bbcode_uid, p.post_attachment';
					$sql_array['SELECT'].= ', u.username, u.user_id, u.user_email, u.user_allow_viewemail';
					$sql_array['FROM'][FORUMS_TABLE] = 'f';
					$sql_array['FROM'][TOPICS_TABLE] = 't';
					$sql_array['FROM'][POSTS_TABLE]  = 'p';
					$sql_array['FROM'][USERS_TABLE]  = 'u';
					$sql_array['WHERE'] = "t.topic_moved_id = 0 AND p.post_approved = 1 $post_sql";
					$sql_array['WHERE'].= " AND ( f.forum_id = p.forum_id AND p.topic_id = t.topic_id AND u.user_id = p.poster_id $forum_sql $topic_sql ) $last_post_time_sql ";
				//	$sql_array['GROUP_BY'] = $group_by;
					$sql_array['ORDER_BY'] = $order_sql;
				}
			break;
		}

		// is there any query to run? may be the feed is disabled ;)
		if ( $sql_array['SELECT'] != '' )
		{
			// Apply filters
			$sql_array['WHERE'] .= (sizeof($this->rss_filters())) ? " AND " . $db->sql_in_set('f.forum_id', $this->rss_filters(), true) : "";
		}

		$this->sql = $sql_array;
	}

	/**
	* Run the sql and fill the data
	*
	* @return array		$this->items	Containing the query result
	**/
	function get_feed()
	{
		if ( $this->sql['SELECT'] == '')
		{
			return false; //trigger_error('NO_RSS_FEED');
		}

		global $db, $cache, $template, $config;

		$this->items = array();

		// Query database
		$this->sql = $db->sql_build_query('SELECT', $this->sql);

		// Pagination
		if ( $config['rss_pagination'] )
		{
			$start		= max(request_var('start', 0), 0);
			$total_count= 0;
			$per_page	= $config['rss_pagination_limit'];

			$result = $db->sql_query_limit($this->sql, $this->sql_limit - $start, $start);

			while ($row = $db->sql_fetchrow($result))
			{
				$this->items[] = $row;
			}
			$db->sql_freeresult($result);

			$total_count = sizeof($this->items) + $start;
			$this->items = array_slice($this->items, 0, $per_page);

			$template->assign_vars(array(
				'PAGINATION'	=> generate_pagination("$this->board_url/$this->u_rss", $total_count, $per_page, $start),
				'PAGE_NUMBER'	=> on_page($total_count, $this->sql_limit, $start),
			));
		}
		else
		{
			$result = $db->sql_query_limit($this->sql, $this->sql_limit);

			while ($row = $db->sql_fetchrow($result))
			{
				// If the forum or the topic is locked, skip it...
				if ( $row['forum_status'] == ITEM_LOCKED || ( isset($row['topic_status']) && $row['topic_status'] == ITEM_LOCKED ) )
				{
					continue;
				}

				$this->items[] = $row;
			}
			$db->sql_freeresult($result);
		}

		return (!$this->items) ? false : $this->items;
	}

	/**
	* Get items property
	*
	* @param array	$row		Array with items data
	* 
	* @return array $item_row	Array with items data
	**/
	function adjust_item(&$row)
	{
		global $db, $config, $user, $phpEx;

		// Reset some data
		$item_row = array();

		$item_row['TITLE']			= ( $row[$this->row_title] ) ? $row[$this->row_title] : $row[$this->row_title2];
		$item_row['DATE']			= $row[$this->row_date];

		// Look if the user has email and allow to display it ;)
		$row['username']			= ( isset($row['username']) ) ? $row['username'] : $row[$this->row_username];
		$item_row['AUTHOR']			= ( (isset($row['user_allow_viewemail']) && isset($row['user_email']) ) ? $row['user_email'] : $config['board_email'] ) . ' (' . $row['username'] . ')';
		$item_row['CATEGORY']		= "$this->board_url/viewforum.$phpEx?f={$row['forum_id']}";
		$item_row['CATEGORY_NAME']	= $row['forum_name'];

		switch ($this->rss_mode)
		{
			case 'forums':
				$this->lang_mode	= $user->lang['ALL_FORUMS'];

				$item_row['LINK']	= $this->rss_append_sid("$this->board_url/viewforum.$phpEx", "f={$row['forum_id']}");
				$item_row['STATS']	= sprintf($user->lang['TOTAL_TOPICS_OTHER'], $row['forum_topics']) . ' &bull; ' . sprintf($user->lang['TOTAL_POSTS_OTHER'], $row['forum_posts']);

				// Get and display all topics in this forum ?
				$row['post_id'] = 0;
				if ( $config['rss_forums_topics'] )
				{
					// Only return up to 100 topics, more will be dangerous? ;)
					$number_topics = 100;

					$topic_sql = "SELECT t.topic_id, t.topic_status, t.topic_title 
						FROM " . TOPICS_TABLE . " t 
						WHERE t.forum_id = {$row['forum_id']}";
					//	GROUP BY t.topic_id, t.topic_title";
					$topic_result = $db->sql_query_limit($topic_sql, $number_topics);

					$topic_titles = '';
					while ( $topic_row = $db->sql_fetchrow($topic_result) )
					{
						// If the topic is locked, skip it...
						if ( $topic_row['topic_status'] == ITEM_LOCKED )
						{
							continue;
						}

						// list of topics witout a link
					//	$topic_titles .= "<li>{$topic_row['topic_title']}</li>";
						// list of topics with a link
						$topic_titles .= "<li><a href=" . $this->rss_append_sid("$this->board_url/viewtopic.$phpEx", 't=' . $topic_row['topic_id']) . ">{$topic_row['topic_title']}</a></li>";
					}
					$db->sql_freeresult($topic_result);	

					$row[$this->row_text] .= "<p><strong>{$user->lang['SUBFORUMS']} : </strong>" . "<ul>$topic_titles</ul></p>" . ( $row['forum_topics'] > $number_topics ? '...' : '' );
				}
				$this->text_limit = 0;

			break;

			case 'topics':
				$this->lang_mode	= $user->lang['ALL_TOPICS'];

				$item_row['LINK']	= $this->rss_append_sid("$this->board_url/viewtopic.$phpEx", "p={$row['post_id']}#p{$row['post_id']}");

				$user_link	= '<a href="' . $this->rss_append_sid("$this->board_url/memberlist.$phpEx", "mode=viewprofile&amp;u=" . $row['user_id']) . '">' . $row['username'] . '</a>';
				$item_row['STATS']	= $user->lang['POSTED'] . ' ' . $user->lang['POST_BY_AUTHOR'] . ' ' . $user_link . ' &bull; ' . $user->lang['POSTED_ON_DATE'] . ' ' . $user->format_date($row['topic_time']). ' &bull; ' . $user->lang['REPLIES'] . ' ' . $row['topic_replies'] . ' &bull; ' . $user->lang['VIEWS'] . ' ' . $row['topic_views'];
			break;

			case 'newposts':
				$this->lang_mode	= $user->lang['RSS_NEWPOST'];
				$item_row['LINK']	= $this->rss_append_sid("$this->board_url/viewtopic.$phpEx", "p={$row['post_id']}#p{$row['post_id']}");

				$user_link	= '<a href="' . $this->rss_append_sid("$this->board_url/memberlist.$phpEx", "mode=viewprofile&amp;u=" . $row['user_id']) . '">' . $row['username'] . '</a>';
				$item_row['STATS']	= $user->lang['POSTED'] . ' ' . $user->lang['POST_BY_AUTHOR'] . ' ' . $user_link . ' &bull; ' . $user->lang['POSTED_ON_DATE'] . ' ' . $user->format_date($row['topic_time']). ' &bull; ' . $user->lang['REPLIES'] . ' ' . $row['topic_replies'] . ' &bull; ' . $user->lang['VIEWS'] . ' ' . $row['topic_views'];
			break;

			case 'news':
				$this->lang_mode	= $user->lang['RSS_NEWS'];

				$item_row['LINK']	= $this->rss_append_sid("$this->board_url/viewtopic.$phpEx", "p={$row['post_id']}#p{$row['post_id']}");

				$user_link	= '<a href="' . $this->rss_append_sid("$this->board_url/memberlist.$phpEx", "mode=viewprofile&amp;u=" . $row['user_id']) . '">' . $row['username'] . '</a>';
				$item_row['STATS']	= $user->lang['POSTED'] . ' ' . $user->lang['POST_BY_AUTHOR'] . ' ' . $user_link . ' &bull; ' . $user->lang['POSTED_ON_DATE'] . ' ' . $user->format_date($row['topic_time']). ' &bull; ' . $user->lang['REPLIES'] . ' ' . $row['topic_replies'] . ' &bull; ' . $user->lang['VIEWS'] . ' ' . $row['topic_views'];
			break;

			case 'posts':
				$this->lang_mode	= $user->lang['ALL_POSTS'];

				$item_row['LINK']	= $this->rss_append_sid("$this->board_url/viewtopic.$phpEx", "p={$row['post_id']}#p{$row['post_id']}");

				$user_link	= '<a href="' . $this->rss_append_sid("$this->board_url/memberlist.$phpEx", "mode=viewprofile&amp;u=" . $row['user_id']) . '">' . $row['username'] . '</a>';
				$item_row['STATS']	= $user->lang['POSTED'] . ' ' . $user->lang['POST_BY_AUTHOR'] . ' ' . $user_link . ' &bull; ' . $user->lang['POSTED_ON_DATE'] . ' ' . $user->format_date($row['post_time']);
			break;

			case 'egosearch':
				$this->lang_mode	= $user->lang['USERNAME'] .' : '. $user->data['username']; //$user->lang['YOUR_POSTS'];

				$item_row['LINK']	= $this->rss_append_sid("$this->board_url/viewtopic.$phpEx", "p={$row['post_id']}#p{$row['post_id']}");
				$item_row['STATS']	= NULL;
			break;

			case 'attachments':
				$this->lang_mode	= $user->lang['RSS_ATTACH'];

				$row['post_id'] = $row['post_msg_id'];
				$row['post_attachment'] = 0;
				$row[$this->row_title2] = ( $row[$this->row_title2] )? "<a href=" . $this->rss_append_sid("$this->board_url/download/file.$phpEx", 'id=' . $row['attach_id']) . ">{$row[$this->row_title2]}</a>" : '';
				$row[$this->row_text] = ( $row[$this->row_text] ? '<strong>' . $row[$this->row_text] . ' : </strong><br />' : '' ) . $row[$this->row_title2];
				$item_row['LINK']	= $this->rss_append_sid("$this->board_url/viewtopic.$phpEx", "p={$row['post_id']}#p{$row['post_id']}");
				$user_link	= '<a href="' . $this->rss_append_sid("$this->board_url/memberlist.$phpEx", "mode=viewprofile&amp;u=" . $row['user_id']) . '">' . $row['username'] . '</a>';
				$item_row['STATS']	= $user->lang['POSTED'] . ' ' . $user->lang['POST_BY_AUTHOR'] . ' ' . $user_link . ' &bull; ' . $user->lang['POSTED_ON_DATE'] . ' ' . $user->format_date($row['post_time']);
			break;

			default:
				$this->lang_mode	= ( ($this->rss_f_id) ? $user->lang['FORUM'] .' » ' . $row['forum_name'] . ( ($this->rss_t_id) ? ' » ' . $user->lang['TOPIC'] . ' : ' . $row['topic_title'] : '' ) : '' );

				$item_row['TITLE']	= $row['forum_name'] . " | " . (( $row[$this->row_title] ) ? $row[$this->row_title] : $row[$this->row_title2]);
				$item_row['LINK']	= $this->rss_append_sid("$this->board_url/viewtopic.$phpEx", "p={$row['post_id']}#p{$row['post_id']}");

				$user_link	= '<a href="' . $this->rss_append_sid("$this->board_url/memberlist.$phpEx", "mode=viewprofile&amp;u=" . $row['user_id']) . '">' . $row['username'] . '</a>';
				$item_row['STATS']	= $user->lang['POSTED'] . ' ' . $user->lang['POST_BY_AUTHOR'] . ' ' . $user_link . ' &bull; ' . $user->lang['POSTED_ON_DATE'] . ' ' . $user->format_date($row['topic_time']). ' &bull; ' . $user->lang['REPLIES'] . ' ' . $row['topic_replies'] . ' &bull; ' . $user->lang['VIEWS'] . ' ' . $row['topic_views'];
				break;
		}

		// Does post have an attachment? If so, add it to the list
		$attach_list = array();
		if ( isset($row['post_attachment']) && $row['post_attachment'] && $config['rss_allow_attachments'] )
		{
			$attach_list[] = $row['post_id'];
		}

		$item_row['TEXT'] = ( !$this->show_text ) ? '' : $this->generate_content($row[$this->row_text], $row[$this->row_uid], $row[$this->row_bit], $attach_list, $row['post_id'], $row['forum_id']);

		unset($attach_list);

		return $item_row;
	}

	/**
	* Find out in which forums the user is not allowed to view
	* 
	* @return array 	$excluded_forums_ary	with forum id to exclude
	**/
	function rss_filters()
	{
		global $auth, $user, $db, $config;

		// Which forums should not be searched ?
		$exclude_forums	= explode(",", $config['rss_exclude_id'] );

		// Exclude forums the user is not able to read
		$excluded_forums_ary = array_keys($auth->acl_getf('!f_read', true));
		$excluded_forums_ary = (sizeof($exclude_forums)) ? array_merge($exclude_forums, $excluded_forums_ary) : $excluded_forums_ary;

		$not_in_fid = (sizeof($excluded_forums_ary)) ? 'WHERE (' . $db->sql_in_set('f.forum_id', $excluded_forums_ary, true) . ' AND ' . $db->sql_in_set('f.parent_id', $excluded_forums_ary, true) . ") OR (f.forum_password <> '' AND fa.user_id <> " . (int) $user->data['user_id'] . ')' : '';

		$sql = 'SELECT f.forum_id, f.forum_name, f.parent_id, f.forum_type, f.right_id, f.forum_password, fa.user_id
			FROM ' . FORUMS_TABLE . ' f
			LEFT JOIN ' . FORUMS_ACCESS_TABLE . " fa ON (fa.forum_id = f.forum_id
				AND fa.session_id = '" . $db->sql_escape($user->session_id) . "')
			$not_in_fid
			ORDER BY f.left_id";
		$result = $db->sql_query($sql);

		$right_id = 0;
		while ($row = $db->sql_fetchrow($result))
		{
			// Exclude passworded forum completely
			if ($row['forum_password'] && $row['user_id'] != $user->data['user_id'])
			{
				$excluded_forums_ary[] = (int) $row['forum_id'];
				continue;
			}

			if ($row['right_id'] > $right_id)
			{
				$right_id = (int) $row['right_id'];
			}
			else if ($row['right_id'] < $right_id)
			{
				continue;
			}
		}
		$db->sql_freeresult($result);

		return $excluded_forums_ary;
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
	* Get date in RFC2822 format
	*
	* @param $forced	bool 	force time to 0 
	* @param $timestamp	integer	the time
	* @param $timezone	integer	the time zone
	* 
	* @return string	string	date in RFC2822 format
	* Code based off : 
	* 	http://cyber.law.harvard.edu/rss/rss.html#requiredChannelElements
	* 	http://www.faqs.org/rfcs/rfc2822 3.3
	**/
	function date2822( $forced = false, $timestamp = 0, $timezone = 0 )
	{
		global $config;

		// Local differential hours+min. (HHMM) ( ("+" / "-") 4DIGIT ); 
		$timezone  = ( $timezone ) ? $timezone   : $config['board_timezone'];
		$timezone  = $timezone + $config['board_dst'];
		$timezone  = ( $timezone > 0 ) ? '+' . $timezone : $timezone;
		$tz = $tzhour = $tzminutes = '';

		$matches = array();
		if ( preg_match('/^([\-+])?([0-9]+)?(\.)?([0-9]+)?$/', $timezone, $matches) )
		{
			$tz			= isset($matches[1] ) ? $matches[1] : $tz;
			$tzhour		= isset($matches[2] ) ? str_pad($matches[2], 2, "0", STR_PAD_LEFT) : $tzhour;
			$tzminutes	= isset($matches[4] ) ? ( ( $matches[4] == '75' ) ? '45' : '30' ) : '00';
			$timezone	= $tz . $tzhour . $tzminutes;
		}
		$timezone  = ( (int) $timezone == 0 ) ? 'GMT' : $timezone;

		$date_time = ( $timestamp ) ? $timestamp : time();
		$date_time = ( $forced ) ? date('D, d M Y 00:00:00', $date_time) : date('D, d M Y H:i:s', $date_time);

		return $date_time . ' ' . $timezone;
	}

	/**
	* Generate text content
	*
	* @param string		$content		Post twext
	* @param int		$uid			bbcode uid
	* @param int		$bitfield		bbcode bitfield
	* @param array		$attach_list	array with post id
	* @param int		$post_id		number of post
	* @param int		$forum_id		number of forum
	* 
	* @return string	
	**/
	function generate_content( $content, $uid, $bitfield, $attach_list, $post_id, $forum_id )
	{
		global $user, $config, $phpbb_root_path, $phpEx;

		if (empty($content))
		{
			return '';
		}

		// Remove Comments from smiles
		$content	= smiley_text($content);

		// Truncates post text ?
		if ( (strlen($content) > $this->text_limit) && ($this->text_limit != 0) )
		{
			$content	= $this->generate_truncate_content($content, $this->text_limit, $uid, false );
		}

		// Prepare some bbcodes for better parsing
		$content	= preg_replace("#\[quote(=&quot;.*?&quot;)?:$uid\]\s*(.*?)\s*\[/quote:$uid\]#si", "[quote$1:$uid]<br />$2<br />[/quote:$uid]", $content);

		/** Just remember : Never use it !
		* Commented out so I do not make the same error twice.
		$content	= html_entity_decode($content);
		**/

		// Parse it!
		$content	= generate_text_for_display($content, $uid, $bitfield, 7);

		// Fix smilies
		$content	= str_replace('{SMILIES_PATH}/', "{$phpbb_root_path}{$config['smilies_path']}/", $content);

		// Relative Path to Absolute path, Windows style
		$content	= str_replace('./', "$this->board_url/", $content);

		// Fix some spaces
		$content	= bbcode_nl2br($content);

		// Remove "Select all" link and mouse events
		$content	= str_replace('<a href="#" onclick="selectCode(this); return false;">' .$user->lang['SELECT_ALL_CODE'] . '</a>', '', $content);
		$content	= preg_replace('#(onkeypress|onclick)="(.*?)"#si', '', $content);

		// Remove Comments from post content
		$content	= preg_replace('#<!-- ([lmwe]) -->(.*?)<!-- ([lmwe]) -->#si', '$2', $content);

		// Remove embed Windows Media Streams
		$content	= preg_replace( '#<\!--\[if \!IE\]>-->([^[]+)<\!--<!\[endif\]-->#si', '', $content);

		// Remove embed and objects
		// Use (<|&lt;) and (>|&gt;) because can be contained into [code][/code]
		$content	= preg_replace( '#(<|&lt;)(object|embed)(.*?) (value|src)=(.*?) ([^[]+)(object|embed)(>|&gt;)#si',' <a href=$5 target="_blank"><strong>$2</strong></a> ',$content);

		// Remove some specials html tag, because somewhere there are a mod to allow html tags ;)
		// Use (<|&lt;) and (>|&gt;) because can be contained into [code][/code]
		$content	= preg_replace( '#(<|&lt;)script (.*?)script(>|&gt;)#si', ' <strong>Script</strong> ', $content);
		$content	= preg_replace( '#(<|&lt;)iframe([^[]+)iframe(>|&gt;)#si', ' <strong>Iframe</strong> ', $content);

		// Resize images ?
		if ( $config['rss_image_size'] )
		{
			$content	= preg_replace('#<img.*?src=\"(.*?)\" alt=(.*?)/>#ise', "\$this->check_rss_imagesize( '$1' )", $content);
		}

		/** Convert special HTML entities back to characters
		* Some languages will need it
		* Commented out so I do not loose the code.
		$content = htmlspecialchars_decode($content);
		**/

		// Other control characters
		$content = preg_replace('#(?:[\x00-\x1F\x7F]+|(?:\xC2[\x80-\x9F])+)#', '', $content);

		// Pull attachment data
		if (sizeof($attach_list))
		{
			global $auth, $db;

			$attachments = $update_count = array();
			$attachments['post_id'][] = $post_id;

			// Pull attachment data
			if ($auth->acl_get('u_download') && $auth->acl_get('f_download', $forum_id))
			{
				$attachments = array();
				$sql = 'SELECT *
					FROM ' . ATTACHMENTS_TABLE . '
					WHERE ' . $db->sql_in_set('post_msg_id', $attach_list) . '
						AND in_message = 0
					ORDER BY filetime DESC, post_msg_id ASC';
				$result = $db->sql_query($sql);

				while ($row = $db->sql_fetchrow($result))
				{
					$attachments[$row['post_msg_id']][] = $row;
				}
				$db->sql_freeresult($result);
			}

			// Attach in-line
			parse_attachments($forum_id, $content, $attachments[$post_id], $update_count);

			// Display not already displayed Attachments for this post, we already parsed them. ;)
			if ( !empty($attachments[$post_id]) )
			{
				$attachment_data = '';
				foreach ($attachments[$post_id] as $attachment)
				{
					$attachment_data .= $attachment;
				}
				// Relative Path to Absolute path, Windows style
				$attachment_data = str_replace('./', "$this->board_url/", $attachment_data);

				$content .= '<br /><br />' . $user->lang['ATTACHMENTS'] . $attachment_data;
			}
		}
		else
		{
			// Remove attachments [ia]
			$content = preg_replace('#<div class="(inline-attachment|attachtitle)">(.*?)<!-- ia(.*?) -->(.*?)<!-- ia(.*?) -->(.*?)</div>#si','',$content);
		}

		/** Just remember : Never use it !
		* Commented out so I do not make the same error twice.
		$content = htmlspecialchars($content);
		**/
	return $content;
	}

	/**
	* Truncates post text while retaining completes bbcodes tag, triying to not cut in between 
	*
	* @param string		$content		post text
	* @param int		$text_limit		number of characters to get
	* @param string		$uid			bbcode uid
	* @param bolean		$recursive		call this function from inside this?
	* 
	* @return string	$content
	**/
	function generate_truncate_content($content, $text_limit, $uid, $recursive = true )
	{
		global $phpbb_root_path, $config;

		$end_content = ( !$recursive ? '<br />...' : '' );

		$content = " " . $content;
		// Change " with '
		$str				= str_replace("&quot;", "'", $content);
		$curr_pos			= 0;
		$curr_length		= 0;
		$curr_text			= '';
		$next_text			= '';

		// Start at the 1st char of the string, looking for opening tags. Cutting the text in each space...
		while( $curr_length < $text_limit )
		{
			$_word = split(' ', $str);
			$skip_lenght = false;

			// pad it with a space so we can distinguish between FALSE and matching the 1st char (index 0).
			$curr_word = (( $_word[0] != ' ') ? ' ' : '' ) . $_word[0];

			// current word/part carry a posible bbcode tag ?
			$the_first_open_bbcode = ( strpos($curr_word, "[") && !strpos($curr_word, "[/") ) ? true : false;

			// fix for smiles, make sure always are completes
			if ( strpos($curr_word, "<img") )
			{
				$smile_open		= strpos( $str, '<img src="' . $phpbb_root_path . $config['smilies_path'] . '/');
				$smile_close	= strpos( $str, " />", $smile_open );
				$curr_word		= substr( $str, 0, $smile_close+3 );
				$the_first_open_bbcode = false;
			}

			// fix for make_clickable, make sure always are completes
			if ( strpos($curr_word, "<a") )
			{
				$anchor_open	= strpos( $str, '<a');
				$anchor_close	= strpos( $str, "/a>", $anchor_open );
				$curr_word		= substr( $str, 0, $anchor_close+3 );
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
				if ( strpos($the_curr_bbcode_tag, "=") )
				{
					list( $bbcode_tag_close, $garbage ) = split( '[=]', $the_curr_bbcode_tag ); // list( $bbcode_tag, $garbage ) = split( '[=:]', $the_curr_bbcode_tag );

					// little fix for a particular bbode :)
					if ( $bbcode_tag_close != "tr" && $bbcode_tag_close != "td")
					{
						$bbcode_tag_close .= ":" . $uid ;
					}

					if ( $bbcode_tag_close == "list:$uid" )
					{
						$bbcode_tag_close = "list:o:$uid";
						$skip_lenght = true;
					}
				}
				else
				{
					$bbcode_tag_close = $the_curr_bbcode_tag;

					if ( $bbcode_tag_close == "list:$uid" )
					{
						$bbcode_tag_close = "list:u:$uid";
						$skip_lenght = true;
					}

					if ( $bbcode_tag_close == "*:$uid")
					{
						$bbcode_tag_close = "*:m:$uid";
					}
				}

				$the_curr_bbcode_tag_close = "[/$bbcode_tag_close]";

				// Is this a simple bbcode tag without a close bbcode [??:??] // like [tab=xx]
				// Or may be the user use the "[" and/or "]" for another propose...
				if ( strpos($str, $the_curr_bbcode_tag_close) === false )
				{
					$the_first_close_bbode   = $the_first_close_bbode+1;
					$the_second_close_bbcode = $the_first_close_bbode;
					$skip_lenght = true;
				}
				else
				{
					$the_second_close_bbcode = strpos($str, $the_curr_bbcode_tag_close)+strlen($the_curr_bbcode_tag_close);
				}

				// Until here all works like expected, 
				// But sometimes the length is much longer as expected, because a bbcode can contain a lot of text, so try to do some magic :)
				$curr_length_until = strlen( $curr_text ) + strlen( substr($str, 0, $the_second_close_bbcode) );

				// Test if the future lenght is longer that the $text_limit 
				if ( ( $curr_length_until > $text_limit ) && !$recursive && !$skip_lenght)
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

					$next_text = "[$the_curr_bbcode_tag]" . $this->generate_truncate_content($current_bbcode_content, ($text_limit-$curr_length), $uid, true) . $the_curr_bbcode_tag_close;
					$curr_text .= " " . $next_text;
					$curr_pos += strlen($next_text);
				}
				else
				{
					$next_text = substr($str, 0, $the_second_close_bbcode);
					$curr_text .= " " . $next_text;
					$curr_pos += strlen($next_text);
				}
			}
			else
			// current word is not a bbcode tag
			{
				$curr_text .= $curr_word;
				$curr_pos += strlen($curr_word);
			}

			$str = substr( $content, $curr_pos );

			// Count for words, without bbcodes, so get the real post length :)
			$curr_length = strlen( preg_replace( "#\[(.*?):$uid\](.*?)\[(.*?):$uid\]#is", '$2', $curr_text ) );
		}

		return $curr_text . $end_content;
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

		// check image with timeout to ensure we don�t wait quite long
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

	/**
	* Output page
	**/
	function close_feed()
	{
		global $db, $template, $auth, $user, $config, $phpbb_root_path, $phpEx;

		$template->assign_vars(array(
			'FEED_ENCODING'			=> '<?xml version="1.0" encoding="UTF-8"?>',
			'FEED_MODE'				=> $this->lang_mode,
			'FEED_TITLE'			=> $config['sitename'],
			'FEED_DESCRIPTION'		=> $config['site_desc'],
			'FEED_LINK'				=> "$this->board_url/index.$phpEx",
			'FEED_LANG'				=> $user->lang['USER_LANG'],
			'FEED_COPYRIGHT'		=> date('Y', $config['board_startdate']) . ' ' . $config['sitename'],
			'FEED_INDEX'			=> "$this->board_url/rss.$phpEx",
			'FEED_DATE'				=> $this->date2822(true),
			'FEED_TIME'				=> $this->date2822(),
			'FEED_MANAGING'			=> $config['board_email'] . " (" . $config['sitename'] . ")",
			'FEED_IMAGE'			=> $this->board_url . '/' . substr($user->img('site_logo', '', false, '', $type = 'src'),2),
			'FEED_TEXT'				=> $this->show_text,
		));

		// Is Board statistics enabled and runing the main the main board feed ?
		if ( $config['rss_board_statistics'] && ( !$this->rss_mode && !$this->rss_f_id && !$this->rss_t_id ) )
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

		/**
		// Check whether the session is still valid if we have one
		if ( basename(trim($config['auth_method'])) == 'apache')
		{
			$php_auth_user	= $_SERVER['PHP_AUTH_USER'];
			$php_auth_pw	= $_SERVER['PHP_AUTH_PW'];

			if ( !rss_checkLogin($php_auth_user, $php_auth_pw) )
			{
				rss_askAuth();
				exit;
			}
		}
		**/

		// Set custom template for styles area
		$template->set_custom_template($phpbb_root_path . 'styles', 'rss');

		// the rss template is never stored in the database
		$user->theme['template_storedb'] = false;

		// gzip_compression
		if ($config['gzip_compress'])
		{
			if (@extension_loaded('zlib') && !headers_sent())
			{
				ob_start('ob_gzhandler');
			}
		}

		// application/xhtml+xml not used because of IE	//header("Content-Type: application/xhtml+xml; charset=UTF-8"); 
		header('Content-type: application/rss+xml; charset=UTF-8');
		header("Last-Modified: " . $this->date2822() );

		$template->set_filenames(array(
			'body'	=> 'rss_template.xml',
		));

		// Output page creation time
		if ( DEBUG && RSS_DEBUG_MODE )
		{
			$mtime = explode(' ', microtime());
			$totaltime = $mtime[0] + $mtime[1] - $this->starttime;

			$debug_output = sprintf('Time : %.3fs | ' . $db->sql_num_queries(false) . ' Queries | GZIP : ' . (($config['gzip_compress']) ? 'On' : 'Off') . (($user->load) ? ' | Load : ' . $user->load : ''), $totaltime);

			if ($auth->acl_get('a_') && defined('DEBUG_EXTRA'))
			{
				if (function_exists('memory_get_usage'))
				{
					if ($memory_usage = memory_get_usage())
					{
						global $base_memory_usage;
						$memory_usage -= $base_memory_usage;
						$memory_usage = get_formatted_filesize($memory_usage);
						$debug_output .= ' | Memory Usage: ' . $memory_usage;
					}
				}
			}

			if ( RSS_DEBUG_SQL )
			{
				$debug_output .= "<br /><strong>SQL : </strong>$this->sql";
			}

			$template->assign_vars(array(
				'DEBUG_OUTPUT'		=> $debug_output,
			));
		}

		//page_footer();
		$template->display('body');

		garbage_collection();
		exit_handler();
	}
}

/******************************************************************************************************************************************/
/* Common functions                                                                                                                       */
/******************************************************************************************************************************************/

/**
* Check if the user is valid
* Code based on root/includes/auth/auth_apache -> login_apache()
**/
function rss_checkLogin($php_auth_user, $php_auth_pw)
{
	global $db;

	if (!empty($php_auth_user) && !empty($php_auth_pw))
	{
		$sql = 'SELECT user_id, username, user_password, user_passchg, user_email, user_type
				FROM ' . USERS_TABLE . "
				WHERE username = '" . $db->sql_escape($php_auth_user) . "'";
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		if ($row)
		{
			// User inactive...
			if ($row['user_type'] == USER_INACTIVE || $row['user_type'] == USER_IGNORE)
			{
				return false;
			}
			// Successful login...
			return true;
		}
		return false;
	}
	return false;
}

function rss_askAuth()
{
	global $config, $user;

	// The name of the area the box asks for access to
	$title = $config['sitename'];

	// I'm not sure about these ones exept they make sure the box comes and gives an message if the login fails
	// There should probably be some control of the tries, like a maximum of 3 tries
	header("WWW-Authenticate: Basic realm='$title'");
	header("HTTP/1.0 401 Unauthorized");
	echo $user->lang['LOGIN_ERROR_EXTERNAL_AUTH_APACHE']; // echo "Sorry, but you are not allowed to see this.";
	exit;
}

?>