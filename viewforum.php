<?php
// -------------------------------------------------------------
//
// $Id$
//
// FILENAME  : viewforum.php 
// STARTED   : Sat Feb 13, 2001
// COPYRIGHT : � 2001, 2003 phpBB Group
// WWW       : http://www.phpbb.com/
// LICENCE   : GPL vs2.0 [ see /docs/COPYING ] 
// 
// -------------------------------------------------------------

define('IN_PHPBB', true);
$phpbb_root_path = './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.'.$phpEx);

// Start session
$user->start();
$auth->acl($user->data);

// Start initial var setup
$forum_id	= (isset($_REQUEST['f'])) ? max(intval($_REQUEST['f']), 0) : 0;
$mark_read	= (!empty($_GET['mark'])) ? htmlspecialchars($_GET['mark']) : '';
$start		= 0;

$sort_days = (isset($_REQUEST['st'])) ? max(intval($_REQUEST['st']), 0) : ((!empty($user->data['user_show_days'])) ? $user->data['user_show_days'] : 0);
$sort_key = (!empty($_REQUEST['sk'])) ? htmlspecialchars($_REQUEST['sk']) : ((!empty($user->data['user_sortby_type'])) ? $user->data['user_sortby_type'] : 't');
$sort_dir = (!empty($_REQUEST['sd'])) ? htmlspecialchars($_REQUEST['sd']) : ((!empty($user->data['user_sortby_dir'])) ? $user->data['user_sortby_dir'] : 'd');

// Check if the user has actually sent a forum ID with his/her request
// If not give them a nice error page.
if (!$forum_id)
{
	trigger_error('NO_FORUM');
}

// Grab appropriate forum data
if ($user->data['user_id'] == ANONYMOUS)
{
	$sql = 'SELECT *
		FROM ' . FORUMS_TABLE . '
		WHERE forum_id = ' . $forum_id;
}
else
{
	switch (SQL_LAYER)
	{
		case 'oracle':
			if ($config['load_db_lastread'])
			{
			}
			else
			{
			}
			break;

		default:
			if ($config['load_db_lastread'])
			{
				$sql_lastread = 'LEFT JOIN ' . FORUMS_TRACK_TABLE . ' ft ON (ft.user_id = ' . $user->data['user_id'] . ' 
					AND ft.forum_id = f.forum_id)';
				$lastread_select = ', ft.mark_time ';
			}
			else
			{
				$sql_lastread = $lastread_select = '';

				$tracking_topics = (isset($_COOKIE[$config['cookie_name'] . '_track'])) ? unserialize(stripslashes($_COOKIE[$config['cookie_name'] . '_track'])) : array();
			}

			$sql_from = ($sql_lastread) ? '((' . FORUMS_TABLE . ' f LEFT JOIN ' . FORUMS_WATCH_TABLE . ' fw ON (fw.forum_id = f.forum_id AND fw.user_id = ' . $user->data['user_id'] . ")) $sql_lastread)" : '(' . FORUMS_TABLE . ' f LEFT JOIN ' . FORUMS_WATCH_TABLE . ' fw ON (fw.forum_id = f.forum_id AND fw.user_id = ' . $user->data['user_id'] . '))';

			$sql = "SELECT f.*, fw.notify_status $lastread_select 
				FROM $sql_from 
				WHERE f.forum_id = $forum_id";
	}
}
$result = $db->sql_query($sql);

if (!($forum_data = $db->sql_fetchrow($result)))
{
	trigger_error('NO_FORUM');
}
$db->sql_freeresult($result);

// Is this forum a link? ... User got here either because the 
// number of clicks is being tracked or they guessed the id
if ($forum_data['forum_link'])
{
	// Does it have click tracking enabled?
	if ($forum_data['forum_flags'] & 1)
	{
		$sql = 'UPDATE ' . FORUMS_TABLE . '
			SET forum_posts = forum_posts + 1 
			WHERE forum_id = ' . $forum_id;
		$db->sql_query($sql);
	}

	redirect($forum_data['forum_link']);
}

// Configure style, language, etc.
$user->setup(false, $forum_data['forum_style']);

// Forum is passworded ... check whether access has been granted to this
// user this session, if not show login box
if ($forum_data['forum_password'])
{
	login_forum_box($forum_data);
}

// Permissions check
if (!$auth->acl_get('f_read', $forum_id))
{
	if ($user->data['user_id'] != ANONYMOUS)
	{
		trigger_error($user->lang['SORRY_AUTH_READ']);
	}

	login_box(preg_replace('#.*?([a-z]+?\.' . $phpEx . '.*?)$#i', '\1', htmlspecialchars($_SERVER['REQUEST_URI'])), '', $user->lang['LOGIN_VIEWFORUM']);
}

// Build navigation links
generate_forum_nav($forum_data);

// Do we have subforums?
$moderators = array();

if ($forum_data['left_id'] != $forum_data['right_id'] - 1)
{
	include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
	display_forums($forum_data);
}
else
{
	$template->assign_var('S_HAS_SUBFORUM', FALSE);
}
get_moderators($moderators, $forum_id);

// Output forum listing if it is postable
if ($forum_data['forum_type'] == FORUM_POST)
{
	// Handle marking posts
	if ($mark_read == 'topics')
	{
		if ($user->data['user_id'] != ANONYMOUS)
		{
			markread('mark', $forum_id);
		}

		meta_refresh(3, "viewforum.$phpEx$SID&amp;f=$forum_id");

		$message = $user->lang['TOPICS_MARKED'] . '<br /><br />' . sprintf($user->lang['RETURN_FORUM'], '<a href="' . "viewforum.$phpEx$SID&amp;f=$forum_id" . '">', '</a> ');
		trigger_error($message);
	}

	// Is a forum specific topic count required?
	if ($forum_data['forum_topics_per_page'])
	{
		$config['topics_per_page'] = $forum_data['forum_topics_per_page'];
	}

	$start	= (isset($_GET['start'])) ? max(intval($_GET['start']), 0) : 0;

	// Do the forum Prune thang - cron type job ...
	// TODO
	// Include viewed too... new row necessary for auto pruning...
	if ($forum_data['prune_next'] < time() && $forum_data['enable_prune'])
	{
		include_once($phpbb_root_path . 'includes/functions_admin.'.$phpEx);
		auto_prune($forum_id, 'posted', $forum_data['forum_flags'], $forum_data['prune_days'], $forum_data['prune_freq']);
	}

	// Forum rules, subscription info and word censors
	$s_watching_forum = $s_watching_forum_img = '';
	if (($config['email_enable'] || $config['jab_enable']) && $config['allow_forum_notify'])
	{
		$notify_status = (isset($forum_data['notify_status'])) ? $forum_data['notify_status'] : NULL;
		watch_topic_forum('forum', $s_watching_forum, $s_watching_forum_img, $user->data['user_id'], $forum_id, $notify_status);
	}

	$s_forum_rules = '';
	gen_forum_rules('forum', $forum_id);

	$censors = array();
	obtain_word_list($censors);

	// Topic ordering options
	$limit_days = array(0 => $user->lang['ALL_TOPICS'], 1 => $user->lang['1_DAY'], 7 => $user->lang['7_DAYS'], 14 => $user->lang['2_WEEKS'], 30 => $user->lang['1_MONTH'], 90 => $user->lang['3_MONTHS'], 180 => $user->lang['6_MONTHS'], 364 => $user->lang['1_YEAR']);

	$sort_by_text = array('a' => $user->lang['AUTHOR'], 't' => $user->lang['POST_TIME'], 'r' => $user->lang['REPLIES'], 's' => $user->lang['SUBJECT'], 'v' => $user->lang['VIEWS']);
	$sort_by_sql = array('a' => 't.topic_first_poster_name', 't' => 't.topic_last_post_time', 'r' => 't.topic_replies', 's' => 't.topic_title', 'v' => 't.topic_views');

	$s_limit_days = $s_sort_key = $s_sort_dir = '';
	gen_sort_selects($limit_days, $sort_by_text, $sort_days, $sort_key, $sort_dir, $s_limit_days, $s_sort_key, $s_sort_dir);

	// Limit topics to certain time frame, obtain correct topic count
	if ($sort_days)
	{
		$min_post_time = time() - ($sort_days * 86400);

		$sql = 'SELECT COUNT(topic_id) AS num_topics
			FROM ' . TOPICS_TABLE . "
			WHERE forum_id = $forum_id
				AND topic_type <> " . POST_ANNOUNCE . "  
				AND topic_last_post_time >= $min_post_time
			" . (($auth->acl_get('m_approve', $forum_id)) ? '' : 'AND topic_approved = 1');
		$result = $db->sql_query($sql);

		if (isset($_POST['sort']))
		{
			$start = 0;
		}
		$topics_count = ($row = $db->sql_fetchrow($result)) ? $row['num_topics'] : 0;
		$sql_limit_time = "AND t.topic_last_post_time >= $min_post_time";
	}
	else
	{
		if ($auth->acl_get('m_approve', $forum_id))
		{
			$topics_count = ($forum_data['forum_topics_real']) ? $forum_data['forum_topics_real'] : 1;
		}
		else
		{
			$topics_count = ($forum_data['forum_topics']) ? $forum_data['forum_topics'] : 1;
		}

		$sql_limit_time = '';
	}

	// Basic pagewide vars
	$post_alt = ($forum_data['forum_status'] == ITEM_LOCKED) ? $user->lang['FORUM_LOCKED'] : $user->lang['POST_NEW_TOPIC'];

	$template->assign_vars(array(
		'PAGINATION'	=> generate_pagination("viewforum.$phpEx$SID&amp;f=$forum_id&amp;st=$sort_days&amp;sk=$sort_key&amp;sd=$sort_dir", $topics_count, $config['topics_per_page'], $start),
		'PAGE_NUMBER'	=> on_page($topics_count, $config['topics_per_page'], $start),
		'TOTAL_TOPICS'	=> ($topics_count == 1) ? $user->lang['VIEW_FORUM_TOPIC'] : sprintf($user->lang['VIEW_FORUM_TOPICS'], $topics_count),
		'MOD_CP' 		=> ($auth->acl_gets('m_', $forum_id)) ? sprintf($user->lang['MCP'], "<a href=\"mcp.$phpEx?sid=$user->session_id&amp;f=$forum_id&amp;mode=forum_view\">", '</a>') : '', 
		'MODERATORS'	=> (!empty($moderators[$forum_id])) ? implode(', ', $moderators[$forum_id]) : '',

		'POST_IMG' 				=> ($forum_data['forum_status'] == ITEM_LOCKED) ? $user->img('btn_locked', $post_alt) : $user->img('btn_post', $post_alt),
		'FOLDER_IMG' 			=> $user->img('folder', 'NO_NEW_POSTS'),
		'FOLDER_NEW_IMG' 		=> $user->img('folder_new', 'NEW_POSTS'),
		'FOLDER_HOT_IMG' 		=> $user->img('folder_hot', 'NO_NEW_POSTS_HOT'),
		'FOLDER_HOT_NEW_IMG'	=> $user->img('folder_hot_new', 'NEW_POSTS_HOT'),
		'FOLDER_LOCKED_IMG' 	=> $user->img('folder_locked', 'NO_NEW_POSTS_LOCKED'),
		'FOLDER_LOCKED_NEW_IMG' => $user->img('folder_locked_new', 'NEW_POSTS_LOCKED'),
		'FOLDER_STICKY_IMG' 	=> $user->img('folder_sticky', 'POST_STICKY'),
		'FOLDER_STICKY_NEW_IMG' => $user->img('folder_sticky_new', 'POST_STICKY'),
		'FOLDER_ANNOUNCE_IMG' 	=> $user->img('folder_announce', 'POST_ANNOUNCEMENT'),
		'FOLDER_ANNOUNCE_NEW_IMG'=> $user->img('folder_announce_new', 'POST_ANNOUNCEMENT'),

		'REPORTED_IMG'			=> $user->img('icon_reported', 'TOPIC_REPORTED'),
		'UNAPPROVED_IMG'		=> $user->img('icon_unapproved', 'TOPIC_UNAPPROVED'),

		'L_NO_TOPICS' 			=> ($forum_data['forum_status'] == ITEM_LOCKED) ? $user->lang['POST_FORUM_LOCKED'] : $user->lang['NO_TOPICS'],

		'S_IS_POSTABLE'			=> TRUE,
		'S_SELECT_SORT_DIR'		=> $s_sort_dir,
		'S_SELECT_SORT_KEY'		=> $s_sort_key,
		'S_SELECT_SORT_DAYS'	=> $s_limit_days,
		'S_TOPIC_ICONS'			=> ($forum_data['enable_icons']) ? true : false, 
		'S_WATCH_FORUM' 		=> $s_watching_forum,
		'S_FORUM_ACTION' 		=> "viewforum.$phpExx$SIDx&amp;f=$forum_id&amp;start=$start",
		'S_DISPLAY_SEARCHBOX'	=> ($auth->acl_get('f_search', $forum_id)) ? true : false, 
		'S_SEARCHBOX_ACTION'	=> "search.$phpEx$SID&amp;f=$forum_id", 

		'U_POST_NEW_TOPIC'	=> "posting.$phpEx$SID&amp;mode=post&amp;f=$forum_id",
		'U_MARK_READ' 		=> "viewforum.$phpEx$SID&amp;f=$forum_id&amp;mark=topics")
	);

	// Grab icons
	$icons = array();
	obtain_icons($icons);

	// Grab all topic data
	$rowset = $announcement_list = $topic_list = array();

	switch (SQL_LAYER)
	{
		case 'oracle':
			break;

		default:
			$sql_from = (($config['load_db_lastread'] || $config['load_db_track']) && $user->data['user_id'] != ANONYMOUS) ? '(' . TOPICS_TABLE . ' t LEFT JOIN ' . TOPICS_TRACK_TABLE . ' tt ON (tt.topic_id = t.topic_id AND tt.user_id = ' . $user->data['user_id'] . '))' : TOPICS_TABLE . ' t ';
	}

	$sql_approved = ($auth->acl_get('m_approve', $forum_id)) ? '' : 'AND t.topic_approved = 1';
	$sql_select = (($config['load_db_lastread'] || $config['load_db_track']) && $user->data['user_id'] != ANONYMOUS) ? ', tt.mark_type, tt.mark_time' : '';

	// Obtain announcements ... removed sort ordering, sort by time in all cases
	$sql = "SELECT t.* $sql_select 
		FROM $sql_from 
		WHERE t.forum_id IN ($forum_id, 0)
			AND t.topic_type IN (" . POST_ANNOUNCE . ', ' . POST_GLOBAL . ')
		ORDER BY t.topic_time DESC';
	$result = $db->sql_query($sql);

	while ($row = $db->sql_fetchrow($result))
	{
		$rowset[$row['topic_id']] = $row;
		$announcement_list[] = $row['topic_id'];
	}
	$db->sql_freeresult($result);

	// If the user is trying to reach late pages, start searching from the end
	$store_reverse = FALSE;
	$sql_limit = $config['topics_per_page'];
	if ($start > $topics_count / 2)
	{
		$store_reverse = TRUE;

		if ($start + $config['topics_per_page'] > $topics_count)
		{
			$sql_limit = min($config['topics_per_page'], max(1, $topics_count - $start));
		}

		// Select the sort order
		$sql_sort_order = $sort_by_sql[$sort_key] . ' ' . (($sort_dir == 'd') ? 'ASC' : 'DESC');
		$sql_start = max(0, $topics_count - $sql_limit - $start);
	}
	else
	{
		// Select the sort order
		$sql_sort_order = $sort_by_sql[$sort_key] . ' ' . (($sort_dir == 'd') ? 'DESC' : 'ASC');
		$sql_start = $start;
	}

	// Obtain other topics
	$sql_rownum = (SQL_LAYER != 'oracle') ? '' : ', ROWNUM rnum ';
	$sql = "SELECT t.* $sql_select$sql_rownum 
		FROM $sql_from
		WHERE t.forum_id = $forum_id 
			AND t.topic_type NOT IN (" . POST_ANNOUNCE . ', ' . POST_GLOBAL . ") 
			$sql_approved 
			$sql_limit_time
		ORDER BY t.topic_type DESC, $sql_sort_order";
	$result = $db->sql_query_limit($sql, $sql_limit, $sql_start);

	while($row = $db->sql_fetchrow($result))
	{
		$rowset[$row['topic_id']] = $row;
		$topic_list[] = $row['topic_id'];
	}
	$db->sql_freeresult($result);

	$topic_list = ($store_reverse) ? array_merge($announcement_list, array_reverse($topic_list)) : array_merge($announcement_list, $topic_list);

	// Okay, lets dump out the page ...
	if (count($topic_list))
	{
		if ($config['load_db_lastread'])
		{
			$mark_time_forum = $forum_data['mark_time'];
		}
		else
		{
			$mark_time_forum = (isset($tracking_topics[$forum_id][0])) ? base_convert($tracking_topics[$forum_id][0], 36, 10) + $config['board_startdate'] : 0;
		}

		$mark_forum_read = true;

		$i = $s_type_switch = 0;
		foreach ($topic_list as $topic_id)
		{
			$row =& $rowset[$topic_id];

			if ($config['load_db_lastread'])
			{
				$mark_time_topic = $row['mark_time'];
			}
			else
			{
				$topic_id36 = base_convert($topic_id, 10, 36);
				$forum_id36 = ($row['topic_type'] == POST_GLOBAL) ? 0 : $row['forum_id'];
				$mark_time_topic = (isset($tracking_topics[$forum_id36][$topic_id36])) ? base_convert($tracking_topics[$forum_id36][$topic_id36], 36, 10) + $config['board_startdate'] : 0;
			}

			// Replies
			$replies = ($auth->acl_get('m_approve')) ? $row['topic_replies_real'] : $row['topic_replies'];

			// Topic type/folder
			$topic_type = '';
			if ($row['topic_status'] == ITEM_MOVED)
			{
				$topic_type = $user->lang['VIEW_TOPIC_MOVED'] . ' ';
				$topic_id = $row['topic_moved_id'];

				$folder_image = 'folder';
				$folder_alt = 'Topic_Moved';
				$newest_post_img = '';
			}
			else
			{
				switch ($row['topic_type'])
				{
					case POST_GLOBAL:
					case POST_ANNOUNCE:
						$topic_type = $user->lang['VIEW_TOPIC_ANNOUNCEMENT'];
						$folder = 'folder_announce';
						$folder_new = 'folder_announce_new';
						break;

					case POST_STICKY:
						$topic_type = $user->lang['VIEW_TOPIC_STICKY'];
						$folder = 'folder_sticky';
						$folder_new = 'folder_sticky_new';
						break;

					default:
						if ($replies >= $config['hot_threshold'])
						{
							$folder = 'folder_hot';
							$folder_new = 'folder_hot_new';
						}
						else
						{
							$folder = 'folder';
							$folder_new = 'folder_new';
						}
						break;
				}

				if ($row['topic_status'] == ITEM_LOCKED)
				{
					$topic_type = $user->lang['VIEW_TOPIC_LOCKED'];
					$folder = 'folder_locked';
					$folder_new = 'folder_locked_new';
				}

				if ($user->data['user_id'] != ANONYMOUS)
				{
					$unread_topic = $new_votes = true;
					if ($mark_time_topic >= $row['topic_last_post_time'] || $mark_time_forum >= $row['topic_last_post_time'] || ($row['topic_last_post_time'] == $row['poll_last_vote'] && $replies))
					{
						$unread_topic = false;
					}
/*
					if ($row['poll_start'] && ($mark_time_topic >= $row['poll_last_vote'] || $mark_time_forum >= $row['poll_last_vote']))
					{
						$new_votes = false;
					}*/
				}
				else
				{
					$unread_topic = $new_votes = false;
				}
 
//				$folder_new .= ($new_votes) ? '_vote' : '';

				$newest_post_img = ($unread_topic) ? "<a href=\"viewtopic.$phpEx$SID&amp;f=$forum_id&amp;t=$topic_id&amp;view=unread#unread\">" . $user->img('icon_post_newest', 'VIEW_NEWEST_POST') . '</a> ' : '';
				$folder_img = ($unread_topic) ? $folder_new : $folder;
				$folder_alt = ($unread_topic) ? 'NEW_POSTS' : (($row['topic_status'] == ITEM_LOCKED) ? 'TOPIC_LOCKED' : 'NO_NEW_POSTS');

				// Posted image?
				if (!empty($row['mark_type']))
				{
					$folder_img .= '_posted';
				}
			}

			if (!$row['poll_start'])
			{
				$topic_type .= $user->lang['VIEW_TOPIC_POLL'];
			}

			// Goto message generation
			if (($replies + 1) > $config['posts_per_page'])
			{
				$total_pages = ceil(($replies + 1) / $config['posts_per_page']);
				$goto_page = ' [ ' . $user->img('icon_post', 'GOTO_PAGE') . $user->lang['GOTO_PAGE'] . ': ';

				$times = 1;
				for($j = 0; $j < $replies + 1; $j += $config['posts_per_page'])
				{
					$goto_page .= "<a href=\"viewtopic.$phpEx$SID&amp;f=$forum_id&amp;t=$topic_id&amp;start=$j\">$times</a>";
					if ($times == 1 && $total_pages > 4)
					{
						$goto_page .= ' ... ';
						$times = $total_pages - 3;
						$j += ($total_pages - 4) * $config['posts_per_page'];
					}
					else if ($times < $total_pages)
					{
						$goto_page .= ', ';
					}
					$times++;
				}
				$goto_page .= ' ] ';
			}
			else
			{
				$goto_page = '';
			}

			// Generate all the URIs ...
			$view_topic_url = "viewtopic.$phpEx$SID&amp;f=$forum_id&amp;t=$topic_id";

			$last_post_img = "<a href=\"viewtopic.$phpEx$SID&amp;f=$forum_id&amp;p=" . $row['topic_last_post_id'] . '#' . $row['topic_last_post_id'] . '">' . $user->img('icon_post_latest', 'VIEW_LATEST_POST') . '</a>';

			$topic_author = ($row['topic_poster'] != ANONYMOUS) ? "<a href=\"memberlist.$phpEx$SID&amp;mode=viewprofile&amp;u=" . $row['topic_poster'] . '">' : '';
			$topic_author .= ($row['topic_poster'] != ANONYMOUS) ? $row['topic_first_poster_name'] : (($row['topic_first_poster_name'] != '') ? $row['topic_first_poster_name'] : $user->lang['GUEST']);
			$topic_author .= ($row['topic_poster'] != ANONYMOUS) ? '</a>' : '';

			$last_post_author = ($row['topic_last_poster_id'] == ANONYMOUS) ? (($row['topic_last_poster_name'] != '') ? $row['topic_last_poster_name'] . ' ' : $user->lang['GUEST'] . ' ') : "<a href=\"memberlist.$phpEx$SID&amp;mode=viewprofile&amp;u="  . $row['topic_last_poster_id'] . '">' . $row['topic_last_poster_name'] . '</a>';

			// This will allow the style designer to output a different header 
			// or even seperate the list of announcements from sticky and normal
			// topics
			$s_type_switch_test = ($row['topic_type'] == POST_ANNOUNCE || $row['topic_type'] == POST_GLOBAL) ? 1 : 0;

			// Send vars to template
			$template->assign_block_vars('topicrow', array(
				'FORUM_ID' 			=> $forum_id,
				'TOPIC_ID' 			=> $topic_id,
				'TOPIC_AUTHOR' 		=> $topic_author,
				'FIRST_POST_TIME' 	=> $user->format_date($row['topic_time'], $config['board_timezone']),
				'LAST_POST_TIME'	=> $user->format_date($row['topic_last_post_time']),
				'LAST_VIEW_TIME'	=> $user->format_date($row['topic_last_view_time']),
				'LAST_POST_AUTHOR' 	=> $last_post_author,
				'GOTO_PAGE' 		=> $goto_page, 
				'REPLIES' 			=> ($auth->acl_get('m_approve')) ? $row['topic_replies_real'] : $row['topic_replies'],
				'VIEWS' 			=> $row['topic_views'],
				'TOPIC_TITLE' 		=> (!empty($censors)) ? preg_replace($censors['match'], $censors['replace'], $row['topic_title']) : $row['topic_title'],
				'TOPIC_TYPE' 		=> $topic_type,

				'LAST_POST_IMG' 	=> $last_post_img,
				'NEWEST_POST_IMG' 	=> $newest_post_img,
				'TOPIC_FOLDER_IMG' 	=> $user->img($folder_img, $folder_alt),
				'TOPIC_ICON_IMG'	=> (!empty($icons[$row['icon_id']])) ? '<img src="' . $config['icons_path'] . '/' . $icons[$row['icon_id']]['img'] . '" width="' . $icons[$row['icon_id']]['width'] . '" height="' . $icons[$row['icon_id']]['height'] . '" alt="" title="" />' : '',
				'ATTACH_ICON_IMG'	=> ($auth->acl_get('f_download', $forum_id) && $row['topic_attachment']) ? $user->img('icon_attach', '') : '',

				'S_ROW_COUNT'			=> $i, 
				'S_TOPIC_TYPE_SWITCH'	=> ($s_type_switch == $s_type_switch_test) ? -1 : $s_type_switch_test, 
				'S_TOPIC_TYPE'			=> $row['topic_type'], 
				'S_USER_POSTED'			=> (!empty($row['mark_type'])) ? true : false, 

				'S_TOPIC_REPORTED'		=> (!empty($row['topic_reported']) && $auth->acl_gets('m_', $forum_id)) ? TRUE : FALSE,
				'S_TOPIC_UNAPPROVED'	=> (!$row['topic_approved'] && $auth->acl_gets('m_approve', $forum_id)) ? TRUE : FALSE,

				'U_VIEW_TOPIC'	=> $view_topic_url,
				'U_MCP_REPORT'		=> "mcp.$phpEx?sid={$user->session_id}&amp;mode=reports&amp;t=$topic_id",
				'U_MCP_QUEUE'		=> "mcp.$phpEx?sid={$user->session_id}&amp;mode=mod_queue&amp;t=$topic_id")
			);

			$s_type_switch = ($row['topic_type'] == POST_ANNOUNCE || $row['topic_type'] == POST_GLOBAL) ? 1 : 0;
			$i++;

			if ($config['load_db_lastread'])
			{
				if ((isset($row['mark_time']) && $row['topic_last_post_time'] > $row['mark_time']) || (empty($row['mark_time']) && $row['topic_last_post_time'] > $forum_data['mark_time']))
				{
					$mark_forum_read = false;
				}
			}
			else
			{
				if (($mark_time_topic && $row['topic_last_post_time'] > $mark_time_topic) || (!$mark_time_topic && $mark_time_forum && $row['topic_last_post_time'] > $mark_time_forum))
				{
					$mark_forum_read = false;
				}
			}

			unset($rowset[$key]);
		}
	}

	// This is rather a fudge but it's the best I can think of without requiring information
	// on all topics (as we do in 2.0.x). It looks for unread or new topics, if it doesn't find
	// any it updates the forum last read cookie. This requires that the user visit the forum
	// after reading a topic
	if ($user->data['user_id'] != ANONYMOUS && $mark_forum_read)
	{
		markread('mark', $forum_id);
	}
}


// Dump out the page header and load viewforum template
page_header($user->lang['VIEW_FORUM'] . ' - ' . $forum_data['forum_name']);

$template->set_filenames(array(
	'body' => 'viewforum_body.html')
);
make_jumpbox("viewforum.$phpEx$SID", $forum_id);

page_footer();

?>