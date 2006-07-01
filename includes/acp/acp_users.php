<?php
/** 
*
* @package acp
* @version $Id$
* @copyright (c) 2005 phpBB Group 
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
* @package acp
*/
class acp_users
{
	var $u_action;

	function main($id, $mode)
	{
		global $config, $db, $user, $auth, $template, $cache;
		global $phpbb_root_path, $phpbb_admin_path, $phpEx, $table_prefix, $file_uploads;

		$user->add_lang(array('posting', 'ucp', 'acp/users'));
		$this->tpl_name = 'acp_users';
		$this->page_title = 'ACP_USER_' . strtoupper($mode);

		include($phpbb_root_path . 'includes/functions_user.' . $phpEx);
		include($phpbb_root_path . 'includes/functions_profile_fields.' . $phpEx);

		$error		= array();
		$username	= request_var('username', '', true);
		$user_id	= request_var('u', 0);
		$action		= request_var('action', '');

		$submit		= (isset($_POST['update'])) ? true : false;

		// Whois (special case)
		if ($action == 'whois')
		{
			$this->page_title = 'WHOIS';
			$this->tpl_name = 'simple_body';

			$user_ip = request_var('user_ip', '');
			$domain = gethostbyaddr($user_ip);
			$ipwhois = '';

			if ($ipwhois = user_ipwhois($user_ip))
			{
				$ipwhois = preg_replace('#(\s)([\w\-\._\+]+@[\w\-\.]+)(\s)#', '\1<a href="mailto:\2">\2</a>\3', $ipwhois);
				$ipwhois = preg_replace('#(\s)(http:/{2}[^\s]*)(\s)#', '\1<a href="\2" target="_blank">\2</a>\3', $ipwhois);
			}

			$template->assign_vars(array(
				'MESSAGE_TITLE'		=> sprintf($user->lang['IP_WHOIS_FOR'], $domain),
				'MESSAGE_TEXT'		=> nl2br($ipwhois))
			);

			return;
		}

		// Show user selection mask
		if (!$username && !$user_id)
		{
			$this->page_title = 'SELECT_USER';

			$template->assign_vars(array(
				'U_ACTION'			=> $this->u_action,
				'ANONYMOUS_USER_ID'	=> ANONYMOUS,

				'S_SELECT_USER'		=> true,
				'U_FIND_USERNAME'	=> append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=searchuser&amp;form=select_user&amp;field=username'),
				)
			);

			return;
		}

		if (!$user_id)
		{
			$sql = 'SELECT user_id
				FROM ' . USERS_TABLE . "
				WHERE username = '" . $db->sql_escape($username) . "'";
			$result = $db->sql_query($sql);
			$user_id = (int) $db->sql_fetchfield('user_id');
			$db->sql_freeresult($result);

			if (!$user_id)
			{
				trigger_error($user->lang['NO_USER'] . adm_back_link($this->u_action));
			}
		}

		// Generate content for all modes
		$sql = 'SELECT u.*, s.*
			FROM ' . USERS_TABLE . ' u
				LEFT JOIN ' . SESSIONS_TABLE . ' s ON (s.session_user_id = u.user_id)
			WHERE u.user_id = ' . $user_id . '
			ORDER BY s.session_time DESC';
		$result = $db->sql_query($sql);
		$user_row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		if (!$user_row)
		{
			trigger_error($user->lang['NO_USER'] . adm_back_link($this->u_action));
		}

		// Generate overall "header" for user admin
		$s_form_options = '';

		// Include info file...
		include_once($phpbb_root_path . 'includes/acp/info/acp_users.' . $phpEx);
		$forms_ary = acp_users_info::module();

		foreach ($forms_ary['modes'] as $value => $ary)
		{
			if (!$this->is_authed($ary['auth']))
			{
				continue;
			}
			
			$selected = ($mode == $value) ? ' selected="selected"' : '';
			$s_form_options .= '<option value="' . $value . '"' . $selected . '>' . $user->lang['ACP_USER_' . strtoupper($value)]  . '</option>';
		}

		$template->assign_vars(array(
			'U_BACK'			=> $this->u_action,
			'U_MODE_SELECT'		=> append_sid("{$phpbb_admin_path}index.$phpEx", "i=$id&amp;u=$user_id"),
			'U_ACTION'			=> $this->u_action . '&amp;u=' . $user_id,
			'S_FORM_OPTIONS'	=> $s_form_options)
		);

		// Prevent normal users/admins change/view founders if they are not a founder by themselves
		if ($user->data['user_type'] != USER_FOUNDER && $user_row['user_type'] == USER_FOUNDER)
		{
			trigger_error($user->lang['NOT_MANAGE_FOUNDER'] . adm_back_link($this->u_action . '&amp;u=' . $user_id));
		}

		switch ($mode)
		{
			case 'overview':
				
				$delete			= request_var('delete', 0);
				$delete_type	= request_var('delete_type', '');
				$ip				= request_var('ip', 'ip');

				if ($submit)
				{
					// You can't delete the founder
					if ($delete && $user_row['user_type'] != USER_FOUNDER)
					{
						if (!$auth->acl_get('a_userdel'))
						{
							trigger_error($user->lang['NO_ADMIN'] . adm_back_link($this->u_action . '&amp;u=' . $user_id));
						}

						// Check if the user wants to remove himself or the guest user account
						if ($user_id == ANONYMOUS)
						{
							trigger_error($user->lang['CANNOT_REMOVE_ANONYMOUS'] . adm_back_link($this->u_action . '&amp;u=' . $user_id));
						}

						if ($user_id == $user->data['user_id'])
						{
							trigger_error($user->lang['CANNOT_REMOVE_YOURSELF'] . adm_back_link($this->u_action . '&amp;u=' . $user_id));
						}

						if (confirm_box(true))
						{
							user_delete($delete_type, $user_id);

							add_log('admin', 'LOG_USER_DELETED', $user_row['username']);
							trigger_error($user->lang['USER_DELETED'] . adm_back_link($this->u_action));
						}
						else
						{
							confirm_box(false, $user->lang['CONFIRM_OPERATION'], build_hidden_fields(array(
								'u'				=> $user_id,
								'i'				=> $id,
								'mode'			=> $mode,
								'action'		=> $action,
								'update'		=> true,
								'delete'		=> 1,
								'delete_type'	=> $delete_type))
							);
						}
					}

					// Handle quicktool actions
					switch ($action)
					{
						case 'banuser':
						case 'banemail':
						case 'banip':
							$ban = array();

							switch ($action)
							{
								case 'banuser':
									$ban[] = $user_row['username'];
									$reason = 'USER_ADMIN_BAN_NAME_REASON';
									$log = 'LOG_USER_BAN_USER';
								break;

								case 'banemail':
									$ban[] = $user_row['user_email'];
									$reason = 'USER_ADMIN_BAN_EMAIL_REASON';
									$log = 'LOG_USER_BAN_EMAIL';
								break;

								case 'banip':
									$ban[] = $user_row['user_ip'];

									$sql = 'SELECT DISTINCT poster_ip
										FROM ' . POSTS_TABLE . "
										WHERE poster_id = $user_id";
									$result = $db->sql_query($sql);

									while ($row = $db->sql_fetchrow($result))
									{
										$ban[] = $row['poster_ip'];
									}
									$db->sql_freeresult($result);

									$reason = 'USER_ADMIN_BAN_IP_REASON';
									$log = 'LOG_USER_BAN_IP';
								break;
							}

							user_ban(substr($action, 3), $ban, 0, 0, 0, $user->lang[$reason]);

							add_log('admin', $log, $user->lang[$reason]);
							add_log('user', $user_id, $log, $user->lang[$reason]);

							trigger_error($user->lang['BAN_SUCCESSFUL'] . adm_back_link($this->u_action . '&amp;u=' . $user_id));

						break;

						case 'reactivate':

							if ($config['email_enable'])
							{
								include_once($phpbb_root_path . 'includes/functions_messenger.' . $phpEx);

								$server_url = generate_board_url();

								$user_actkey = gen_rand_string(10);
								$key_len = 54 - (strlen($server_url));
								$key_len = ($key_len > 6) ? $key_len : 6;
								$user_actkey = substr($user_actkey, 0, $key_len);

								if ($user_row['user_type'] != USER_INACTIVE)
								{
									user_active_flip($user_id, $user_row['user_type'], $user_actkey, $user_row['username']);
								}

								$messenger = new messenger(false);

								$messenger->template('user_resend_inactive', $user_row['user_lang']);

								$messenger->replyto($config['board_contact']);
								$messenger->to($user_row['user_email'], $user_row['username']);

								$messenger->headers('X-AntiAbuse: Board servername - ' . $config['server_name']);
								$messenger->headers('X-AntiAbuse: User_id - ' . $user->data['user_id']);
								$messenger->headers('X-AntiAbuse: Username - ' . $user->data['username']);
								$messenger->headers('X-AntiAbuse: User IP - ' . $user->ip);

								$messenger->assign_vars(array(
									'SITENAME'		=> $config['sitename'],
									'WELCOME_MSG'	=> sprintf($user->lang['WELCOME_SUBJECT'], $config['sitename']),
									'USERNAME'		=> html_entity_decode($user_row['username']),
									'EMAIL_SIG'		=> str_replace('<br />', "\n", "-- \n" . $config['board_email_sig']),

									'U_ACTIVATE'	=> "$server_url/ucp.$phpEx?mode=activate&u={$user_row['user_id']}&k=$user_actkey")
								);

								$messenger->send(NOTIFY_EMAIL);

								add_log('admin', 'LOG_USER_REACTIVATE', $user_row['username']);
								add_log('user', $user_id, 'LOG_USER_REACTIVATE_USER');

								trigger_error($user->lang['FORCE_REACTIVATION_SUCCESS'] . adm_back_link($this->u_action . '&amp;u=' . $user_id));
							}

						break;

						case 'active':

							user_active_flip($user_id, $user_row['user_type'], false, $user_row['username']);

							$message = ($user_row['user_type'] == USER_INACTIVE) ? 'USER_ADMIN_ACTIVATED' : 'USER_ADMIN_DEACTIVED';
							$log = ($user_row['user_type'] == USER_INACTIVE) ? 'LOG_USER_ACTIVE' : 'LOG_USER_INACTIVE';

							add_log('user', $user_id, $log . '_USER');

							if ($user_row['user_type'] == USER_INACTIVE)
							{
								set_config('num_users', $config['num_users'] + 1, true);
							}
							else
							{
								set_config('num_users', $config['num_users'] - 1, true);
							}

							// Update latest username
							update_last_username();

							trigger_error($user->lang[$message] . adm_back_link($this->u_action . '&amp;u=' . $user_id));

						break;

						case 'delsig':

							$sql_ary = array(
								'user_sig'					=> '',
								'user_sig_bbcode_uid'		=> '',
								'user_sig_bbcode_bitfield'	=> 0
							);

							$sql = 'UPDATE ' . USERS_TABLE . ' SET ' . $db->sql_build_array('UPDATE', $sql_ary) . "
								WHERE user_id = $user_id";
							$db->sql_query($sql);
						
							add_log('admin', 'LOG_USER_DEL_SIG', $user_row['username']);
							add_log('user', $user_id, 'LOG_USER_DEL_SIG_USER');

							trigger_error($user->lang['USER_ADMIN_SIG_REMOVED'] . adm_back_link($this->u_action . '&amp;u=' . $user_id));

						break;

						case 'delavatar':
							
							$sql_ary = array(
								'user_avatar'			=> '',
								'user_avatar_type'		=> 0,
								'user_avatar_width'		=> 0,
								'user_avatar_height'	=> 0,
							);

							$sql = 'UPDATE ' . USERS_TABLE . '
								SET ' . $db->sql_build_array('UPDATE', $sql_ary) . "
								WHERE user_id = $user_id";
							$db->sql_query($sql);

							// Delete old avatar if present
							if ($user_row['user_avatar'] && $user_row['user_avatar_type'] != AVATAR_GALLERY)
							{
								avatar_delete($user_row['user_avatar']);
							}

							add_log('admin', 'LOG_USER_DEL_AVATAR', $user_row['username']);
							add_log('user', $user_id, 'LOG_USER_DEL_AVATAR_USER');

							trigger_error($user->lang['USER_ADMIN_AVATAR_REMOVED'] . adm_back_link($this->u_action . '&amp;u=' . $user_id));
						break;

						case 'delposts':

							if (confirm_box(true))
							{
								$sql = 'SELECT topic_id, COUNT(post_id) AS total_posts
									FROM ' . POSTS_TABLE . "
									WHERE poster_id = $user_id
									GROUP BY topic_id";
								$result = $db->sql_query($sql);

								$topic_id_ary = array();
								while ($row = $db->sql_fetchrow($result))
								{
									$topic_id_ary[$row['topic_id']] = $row['total_posts'];
								}
								$db->sql_freeresult($result);

								if (sizeof($topic_id_ary))
								{
									$sql = 'SELECT topic_id, topic_replies, topic_replies_real
										FROM ' . TOPICS_TABLE . '
										WHERE topic_id IN (' . implode(', ', array_keys($topic_id_ary)) . ')';
									$result = $db->sql_query($sql);

									$del_topic_ary = array();
									while ($row = $db->sql_fetchrow($result))
									{
										if (max($row['topic_replies'], $row['topic_replies_real']) + 1 == $topic_id_ary[$row['topic_id']])
										{
											$del_topic_ary[] = $row['topic_id'];
										}
									}
									$db->sql_freeresult($result);

									if (sizeof($del_topic_ary))
									{
										$sql = 'DELETE FROM ' . TOPICS_TABLE . '
											WHERE topic_id IN (' . implode(', ', $del_topic_ary) . ')';
										$db->sql_query($sql);
									}
								}

								// Delete posts, attachments, etc.
								delete_posts('poster_id', $user_id);

								add_log('admin', 'LOG_USER_DEL_POSTS', $user_row['username']);
								trigger_error($user->lang['USER_POSTS_DELETED'] . adm_back_link($this->u_action . '&amp;u=' . $user_id));
							}
							else
							{
								confirm_box(false, $user->lang['CONFIRM_OPERATION'], build_hidden_fields(array(
									'u'				=> $user_id,
									'i'				=> $id,
									'mode'			=> $mode,
									'action'		=> $action,
									'update'		=> true))
								);
							}

						break;

						case 'delattach':

							if (confirm_box(true))
							{
								delete_attachments('user', $user_id);

								add_log('admin', 'LOG_USER_DEL_ATTACH', $user_row['username']);
								trigger_error($user->lang['USER_ATTACHMENTS_REMOVED'] . adm_back_link($this->u_action . '&amp;u=' . $user_id));
							}
							else
							{
								confirm_box(false, $user->lang['CONFIRM_OPERATION'], build_hidden_fields(array(
									'u'				=> $user_id,
									'i'				=> $id,
									'mode'			=> $mode,
									'action'		=> $action,
									'update'		=> true))
								);
							}
						
						break;
						
						case 'moveposts':
								
							$new_forum_id = request_var('new_f', 0);

							if (!$new_forum_id)
							{
								$this->page_title = 'USER_ADMIN_MOVE_POSTS';

								$template->assign_vars(array(
									'S_SELECT_FORUM'		=> true,
									'U_ACTION'				=> $this->u_action . "&amp;action=$action&amp;u=$user_id",
									'U_BACK'				=> $this->u_action . "&amp;u=$user_id",
									'S_FORUM_OPTIONS'		=> make_forum_select(false, false, false, true))
								);

								return;
							}

							// Two stage?
							// Move topics comprising only posts from this user
							$topic_id_ary = $move_topic_ary = $move_post_ary = $new_topic_id_ary = array();
							$forum_id_ary = array($new_forum_id);

							$sql = 'SELECT topic_id, COUNT(post_id) AS total_posts
								FROM ' . POSTS_TABLE . "
								WHERE poster_id = $user_id
									AND forum_id <> $new_forum_id
								GROUP BY topic_id";
							$result = $db->sql_query($sql);

							while ($row = $db->sql_fetchrow($result))
							{
								$topic_id_ary[$row['topic_id']] = $row['total_posts'];
							}
							$db->sql_freeresult($result);

							if (sizeof($topic_id_ary))
							{
								$sql = 'SELECT topic_id, forum_id, topic_title, topic_replies, topic_replies_real
									FROM ' . TOPICS_TABLE . '
									WHERE topic_id IN (' . implode(', ', array_keys($topic_id_ary)) . ')';
								$result = $db->sql_query($sql);

								while ($row = $db->sql_fetchrow($result))
								{
									if (max($row['topic_replies'], $row['topic_replies_real']) + 1 == $topic_id_ary[$row['topic_id']])
									{
										$move_topic_ary[] = $row['topic_id'];
									}
									else
									{
										$move_post_ary[$row['topic_id']]['title'] = $row['topic_title'];
										$move_post_ary[$row['topic_id']]['attach'] = ($row['attach']) ? 1 : 0;
									}

									$forum_id_ary[] = $row['forum_id'];
								}
								$db->sql_freeresult($result);
							}

							// Entire topic comprises posts by this user, move these topics
							if (sizeof($move_topic_ary))
							{
								move_topics($move_topic_ary, $new_forum_id, false);
							}

							if (sizeof($move_post_ary))
							{
								// Create new topic
								// Update post_ids, report_ids, attachment_ids
								foreach ($move_post_ary as $topic_id => $post_ary)
								{
									// Create new topic
									$sql = 'INSERT INTO ' . TOPICS_TABLE . ' ' . $db->sql_build_array('INSERT', array(
										'topic_poster'				=> $user_id,
										'topic_time'				=> time(),
										'forum_id' 					=> $new_forum_id,
										'icon_id'					=> 0,
										'topic_approved'			=> 1,
										'topic_title' 				=> $post_ary['title'],
										'topic_first_poster_name'	=> $user_row['username'],
										'topic_type'				=> POST_NORMAL,
										'topic_time_limit'			=> 0,
										'topic_attachment'			=> $post_ary['attach'])
									);
									$db->sql_query($sql);

									$new_topic_id = $db->sql_nextid();

									// Move posts
									$sql = 'UPDATE ' . POSTS_TABLE . "
										SET forum_id = $new_forum_id, topic_id = $new_topic_id
										WHERE topic_id = $topic_id
											AND poster_id = $user_id";
									$db->sql_query($sql);

									if ($post_ary['attach'])
									{
										$sql = 'UPDATE ' . ATTACHMENTS_TABLE . "
											SET topic_id = $new_topic_id
											WHERE topic_id = $topic_id
												AND poster_id = $user_id";
										$db->sql_query($sql);
									}

									$new_topic_id_ary[] = $new_topic_id;
								}
							}

							$forum_id_ary = array_unique($forum_id_ary);
							$topic_id_ary = array_unique(array_merge($topic_id_ary, $new_topic_id_ary));

							if (sizeof($topic_id_ary))
							{
								sync('reported', 'topic_id', $topic_id_ary);
								sync('topic', 'topic_id', $topic_id_ary);
							}

							if (sizeof($forum_id_ary))
							{
								sync('forum', 'forum_id', $forum_id_ary);
							}

							$sql = 'SELECT forum_name
								FROM ' . FORUMS_TABLE . "
								WHERE forum_id = $new_forum_id";
							$result = $db->sql_query($sql, 3600);
							$forum_info = $db->sql_fetchrow($result);
							$db->sql_freeresult($result);

							add_log('admin', 'LOG_USER_MOVE_POSTS', $user_row['username'], $forum_info['forum_name']);
							add_log('user', $user_id, 'LOG_USER_MOVE_POSTS_USER', $forum_info['forum_name']);

							trigger_error($user->lang['USER_POSTS_MOVED'] . adm_back_link($this->u_action . '&amp;u=' . $user_id));

						break;
					}

					$data = array();

					// Handle registration info updates
					$var_ary = array(
						'user'				=> (string) $user_row['username'],
						'user_founder'		=> (int) (($user_row['user_type'] == USER_FOUNDER) ? 1 : 0),
						'user_email'		=> (string) $user_row['user_email'],
						'email_confirm'		=> (string) '',
						'user_password'		=> (string) '',
						'password_confirm'	=> (string) '',
						'warnings'			=> (int) $user_row['user_warnings'],
					);

					// Get the data from the form. Use data from the database if no info is provided
					foreach ($var_ary as $var => $default)
					{
						$data[$var] = request_var($var, $default);
					}

					// We use user within the form to circumvent auto filling
					$data['username'] = $data['user'];
					unset($data['user']);

					// Validation data
					$var_ary = array(
						'password_confirm'	=> array('string', true, $config['min_pass_chars'], $config['max_pass_chars']),
						'user_password'		=> array('string', true, $config['min_pass_chars'], $config['max_pass_chars']),
						'warnings'			=> array('num'),
					);

					// Check username if altered
					if ($data['username'] != $user_row['username'])
					{
						$var_ary += array(
							'username'			=> array(
								array('string', false, $config['min_name_chars'], $config['max_name_chars']),
								array('username', $user_row['username'])),
						);
					}

					// Check email if altered
					if ($data['user_email'] != $user_row['user_email'])
					{
						$var_ary += array(
							'user_email'		=> array(
								array('string', false, 6, 60),
								array('email', $user_row['user_email'])
								), 
							'email_confirm'		=> array('string', true, 6, 60)
						);
					}

					$error = validate_data($data, $var_ary);

					if ($data['user_password'] && $data['password_confirm'] != $data['user_password'])
					{
						$error[] = 'NEW_PASSWORD_ERROR';
					}

					if ($data['user_email'] != $user_row['user_email'] && $data['email_confirm'] != $data['user_email'])
					{
						$error[] = 'NEW_EMAIL_ERROR';
					}

					// Which updates do we need to do?
					$update_warning = ($user_row['user_warnings'] != $data['warnings']) ? true : false;
					$update_username = ($user_row['username'] != $data['username']) ? $data['username'] : false;
					$update_password = ($data['user_password'] && $user_row['user_password'] != md5($data['user_password'])) ? true : false;
					$update_email = ($data['user_email'] != $user_row['user_email']) ? $data['user_email'] : false;

					if (!sizeof($error))
					{
						$sql_ary = array();

						if ($user_row['user_type'] != USER_FOUNDER || $user->data['user_type'] == USER_FOUNDER)
						{
							if ($update_warning)
							{
								$sql_ary['user_warnings'] = $data['warnings'];
							}

							// Only allow founders updating the founder status...
							if ($user->data['user_type'] == USER_FOUNDER)
							{
								// Setting a normal member to be a founder
								if ($data['user_founder'] && $user_row['user_type'] != USER_FOUNDER)
								{
									$sql_ary['user_type'] = USER_FOUNDER;
								}
								else if (!$data['user_founder'] && $user_row['user_type'] == USER_FOUNDER)
								{
									// Check if at least one founder is present
									$sql = 'SELECT user_id
										FROM ' . USERS_TABLE . '
										WHERE user_type = ' . USER_FOUNDER . '
											AND user_id <> ' . $user_id;
									$result = $db->sql_query_limit($sql, 1);
									$row = $db->sql_fetchrow($result);
									$db->sql_freeresult($result);

									if ($row)
									{
										$sql_ary['user_type'] = USER_NORMAL;
									}
									else
									{
										trigger_error($user->lang['AT_LEAST_ONE_FOUNDER'] . adm_back_link($this->u_action . '&amp;u=' . $user_id));
									}
								}
							}
						}

						if ($update_username !== false)
						{
							$sql_ary['username'] = $update_username;

							add_log('user', $user_id, 'LOG_USER_UPDATE_NAME', $user_row['username'], $update_username);
						}

						if ($update_email !== false)
						{
							$sql_ary += array(
								'user_email'		=> $update_email,
								'user_email_hash'	=> crc32(strtolower($update_email)) . strlen($update_email)
							);

							add_log('user', $user_id, 'LOG_USER_UPDATE_EMAIL', $user_row['username'], $user_row['user_email'], $update_email);
						}

						if ($update_password)
						{
							$sql_ary += array(
								'user_password' => md5($data['user_password']),
								'user_passchg'	=> time(),
							);

							$user->reset_login_keys($user_id);
							add_log('user', $user_id, 'LOG_USER_NEW_PASSWORD', $user_row['username']);
						}

						if (sizeof($sql_ary))
						{
							$sql = 'UPDATE ' . USERS_TABLE . '
								SET ' . $db->sql_build_array('UPDATE', $sql_ary) . '
								WHERE user_id = ' . $user_id;
							$db->sql_query($sql);
						}

						/**
						* @todo adjust every data based in the number of user warnings
						*/
						if ($update_warning)
						{
						}

						if ($update_username)
						{
							user_update_name($user_row['username'], $update_username);
						}

						// Let the users permissions being updated
						$auth->acl_clear_prefetch($user_id);

						add_log('admin', 'LOG_USER_USER_UPDATE', $data['username']);

						trigger_error($user->lang['USER_OVERVIEW_UPDATED'] . adm_back_link($this->u_action . '&amp;u=' . $user_id));
					}

					// Replace "error" strings with their real, localised form
					$error = preg_replace('#^([A-Z_]+)$#e', "(!empty(\$user->lang['\\1'])) ? \$user->lang['\\1'] : '\\1'", $error);
				}

				$user_char_ary = array('.*' => 'USERNAME_CHARS_ANY', '[\w]+' => 'USERNAME_ALPHA_ONLY', '[\w_\+\. \-\[\]]+' => 'USERNAME_ALPHA_SPACERS');
				$quick_tool_ary = array('banuser' => 'BAN_USER', 'banemail' => 'BAN_EMAIL', 'banip' => 'BAN_IP', 'active' => (($user_row['user_type'] == USER_INACTIVE) ? 'ACTIVATE' : 'DEACTIVATE'), 'delsig' => 'DEL_SIG', 'delavatar' => 'DEL_AVATAR', 'moveposts' => 'MOVE_POSTS', 'delposts' => 'DEL_POSTS', 'delattach' => 'DEL_ATTACH');
				
				if ($config['email_enable'])
				{
					$quick_tool_ary['reactivate'] = 'FORCE';
				}

				$s_action_options = '<option class="sep" value="">' . $user->lang['SELECT_OPTION'] . '</option>';
				foreach ($quick_tool_ary as $value => $lang)
				{
					$s_action_options .= '<option value="' . $value . '">' . $user->lang['USER_ADMIN_' . $lang]  . '</option>';
				}

				$template->assign_vars(array(
					'L_NAME_CHARS_EXPLAIN'		=> sprintf($user->lang[$user_char_ary[$config['allow_name_chars']] . '_EXPLAIN'], $config['min_name_chars'], $config['max_name_chars']),
					'L_CHANGE_PASSWORD_EXPLAIN'	=> sprintf($user->lang['CHANGE_PASSWORD_EXPLAIN'], $config['min_pass_chars'], $config['max_pass_chars']),
					'S_FOUNDER'					=> ($user->data['user_type'] == USER_FOUNDER) ? true : false,

					'S_OVERVIEW'		=> true,
					'S_USER_IP'			=> ($user_row['user_ip']) ? true : false,
					'S_USER_FOUNDER'	=> ($user_row['user_type'] == USER_FOUNDER) ? true : false,
					'S_ACTION_OPTIONS'	=> $s_action_options,

					'U_SHOW_IP'		=> $this->u_action . "&amp;u=$user_id&amp;ip=" . (($ip == 'ip') ? 'hostname' : 'ip'),
					'U_WHOIS'		=> $this->u_action . "&amp;action=whois&amp;user_ip={$user_row['user_ip']}",

					'U_SWITCH_PERMISSIONS'	=> ($auth->acl_get('a_switchperm') && $user->data['user_id'] != $user_row['user_id']) ? append_sid("{$phpbb_root_path}ucp.$phpEx", "mode=switch_perm&amp;u={$user_row['user_id']}") : '',

					'USER'				=> $user_row['username'],
					'USER_REGISTERED'	=> $user->format_date($user_row['user_regdate']),
					'REGISTERED_IP'		=> ($ip == 'hostname') ? gethostbyaddr($user_row['user_ip']) : $user_row['user_ip'],
					'USER_LASTACTIVE'	=> ($user_row['user_lastvisit']) ? $user->format_date($user_row['user_lastvisit']) : ' - ',
					'USER_EMAIL'		=> $user_row['user_email'],
					'USER_WARNINGS'		=> $user_row['user_warnings'],
					)
				);

			break;

			case 'feedback':

				$user->add_lang('mcp');
				
				// Set up general vars
				$start		= request_var('start', 0);
				$deletemark = (isset($_POST['delmarked'])) ? true : false;
				$deleteall	= (isset($_POST['delall'])) ? true : false;
				$marked		= request_var('mark', array(0));
				$message	= request_var('message', '', true);

				// Sort keys
				$sort_days	= request_var('st', 0);
				$sort_key	= request_var('sk', 't');
				$sort_dir	= request_var('sd', 'd');

				// Delete entries if requested and able
				if (($deletemark || $deleteall) && $auth->acl_get('a_clearlogs'))
				{
					$where_sql = '';
					if ($deletemark && $marked)
					{
						$sql_in = array();
						foreach ($marked as $mark)
						{
							$sql_in[] = $mark;
						}
						$where_sql = ' AND log_id IN (' . implode(', ', $sql_in) . ')';
						unset($sql_in);
					}

					if ($where_sql || $deleteall)
					{
						$sql = 'DELETE FROM ' . LOG_TABLE . '
							WHERE log_type = ' . LOG_USERS . "
							$where_sql";
						$db->sql_query($sql);

						add_log('admin', 'LOG_CLEAR_USER', $user_row['username']);
					}
				}

				if ($submit && $message)
				{
					add_log('admin', 'LOG_USER_FEEDBACK', $user_row['username']);
					add_log('user', $user_id, 'LOG_USER_GENERAL', $message);

					trigger_error($user->lang['USER_FEEDBACK_ADDED'] . adm_back_link($this->u_action . '&amp;u=' . $user_id));
				}
				
				// Sorting
				$limit_days = array(0 => $user->lang['ALL_ENTRIES'], 1 => $user->lang['1_DAY'], 7 => $user->lang['7_DAYS'], 14 => $user->lang['2_WEEKS'], 30 => $user->lang['1_MONTH'], 90 => $user->lang['3_MONTHS'], 180 => $user->lang['6_MONTHS'], 365 => $user->lang['1_YEAR']);
				$sort_by_text = array('u' => $user->lang['SORT_USERNAME'], 't' => $user->lang['SORT_DATE'], 'i' => $user->lang['SORT_IP'], 'o' => $user->lang['SORT_ACTION']);
				$sort_by_sql = array('u' => 'l.user_id', 't' => 'l.log_time', 'i' => 'l.log_ip', 'o' => 'l.log_operation');

				$s_limit_days = $s_sort_key = $s_sort_dir = $u_sort_param = '';
				gen_sort_selects($limit_days, $sort_by_text, $sort_days, $sort_key, $sort_dir, $s_limit_days, $s_sort_key, $s_sort_dir, $u_sort_param);

				// Define where and sort sql for use in displaying logs
				$sql_where = ($sort_days) ? (time() - ($sort_days * 86400)) : 0;
				$sql_sort = $sort_by_sql[$sort_key] . ' ' . (($sort_dir == 'd') ? 'DESC' : 'ASC');

				// Grab log data
				$log_data = array();
				$log_count = 0;
				view_log('user', $log_data, $log_count, $config['topics_per_page'], $start, 0, 0, $user_id, $sql_where, $sql_sort);

				$template->assign_vars(array(
					'S_FEEDBACK'	=> true,
					'S_ON_PAGE'		=> on_page($log_count, $config['topics_per_page'], $start),
					'PAGINATION'	=> generate_pagination($this->u_action . "&amp;u=$user_id&amp;$u_sort_param", $log_count, $config['topics_per_page'], $start, true),

					'S_LIMIT_DAYS'	=> $s_limit_days,
					'S_SORT_KEY'	=> $s_sort_key,
					'S_SORT_DIR'	=> $s_sort_dir,
					'S_CLEARLOGS'	=> $auth->acl_get('a_clearlogs'))
				);

				foreach ($log_data as $row)
				{
					$template->assign_block_vars('log', array(
						'USERNAME'		=> $row['username'],
						'IP'			=> $row['ip'],
						'DATE'			=> $user->format_date($row['time']),
						'ACTION'		=> nl2br($row['action']),
						'ID'			=> $row['id'])
					);
				}

			break;

			case 'profile':

				$cp = new custom_profile();

				$cp_data = $cp_error = array();
				$data = array();

				$sql = 'SELECT lang_id
					FROM ' . LANG_TABLE . "
					WHERE lang_iso = '" . $db->sql_escape($user_row['user_lang']) . "'";
				$result = $db->sql_query($sql);
				$row = $db->sql_fetchrow($result);
				$db->sql_freeresult($result);

				$user_row['iso_lang_id'] = $row['lang_id'];

				if ($submit)
				{
					$var_ary = array(
						'icq'			=> (string) '',
						'aim'			=> (string) '',
						'msn'			=> (string) '',
						'yim'			=> (string) '',
						'jabber'		=> (string) '',
						'website'		=> (string) '',
						'location'		=> (string) '',
						'occupation'	=> (string) '',
						'interests'		=> (string) '',
						'bday_day'		=> 0,
						'bday_month'	=> 0,
						'bday_year'		=> 0,
					);

					foreach ($var_ary as $var => $default)
					{
						$data[$var] = (in_array($var, array('location', 'occupation', 'interests'))) ? request_var($var, $default, true) : $data[$var] = request_var($var, $default);
					}

					$var_ary = array(
						'icq'			=> array(
							array('string', true, 3, 15),
							array('match', true, '#^[0-9]+$#i')),
						'aim'			=> array('string', true, 3, 17),
						'msn'			=> array('string', true, 5, 255),
						'jabber'		=> array(
							array('string', true, 5, 255),
							array('match', true, '#^[a-z0-9\.\-_\+]+?@(.*?\.)*?[a-z0-9\-_]+?\.[a-z]{2,4}(/.*)?$#i')),
						'yim'			=> array('string', true, 5, 255),
						'website'		=> array(
							array('string', true, 12, 255),
							array('match', true, '#^http[s]?://(.*?\.)*?[a-z0-9\-]+\.[a-z]{2,4}#i')),
						'location'		=> array('string', true, 2, 255),
						'occupation'	=> array('string', true, 2, 500),
						'interests'		=> array('string', true, 2, 500),
						'bday_day'		=> array('num', true, 1, 31),
						'bday_month'	=> array('num', true, 1, 12),
						'bday_year'		=> array('num', true, 1901, gmdate('Y', time())),
					);

					$error = validate_data($data, $var_ary);

					// validate custom profile fields
					$cp->submit_cp_field('profile', $user_row['iso_lang_id'], $cp_data, $cp_error);

					if (sizeof($cp_error))
					{
						$error = array_merge($error, $cp_error);
					}

					if (!sizeof($error))
					{
						$sql_ary = array(
							'user_icq'		=> $data['icq'],
							'user_aim'		=> $data['aim'],
							'user_msnm'		=> $data['msn'],
							'user_yim'		=> $data['yim'],
							'user_jabber'	=> $data['jabber'],
							'user_website'	=> $data['website'],
							'user_from'		=> $data['location'],
							'user_occ'		=> $data['occupation'],
							'user_interests'=> $data['interests'],
							'user_birthday'	=> sprintf('%2d-%2d-%4d', $data['bday_day'], $data['bday_month'], $data['bday_year']),
						);

						$sql = 'UPDATE ' . USERS_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', $sql_ary) . "
							WHERE user_id = $user_id";
						$db->sql_query($sql);

						// Update Custom Fields
						if (sizeof($cp_data))
						{
							$sql = 'UPDATE ' . PROFILE_FIELDS_DATA_TABLE . '
								SET ' . $db->sql_build_array('UPDATE', $cp_data) . "
								WHERE user_id = $user_id";
							$db->sql_query($sql);

							if (!$db->sql_affectedrows())
							{
								$cp_data['user_id'] = (int) $user_id;

								$db->return_on_error = true;

								$sql = 'INSERT INTO ' . PROFILE_FIELDS_DATA_TABLE . ' ' . $db->sql_build_array('INSERT', $cp_data);
								$db->sql_query($sql);

								$db->return_on_error = false;
							}
						}

						trigger_error($user->lang['USER_PROFILE_UPDATED'] . adm_back_link($this->u_action . '&amp;u=' . $user_id));
					}

					// Replace "error" strings with their real, localised form
					$error = preg_replace('#^([A-Z_]+)$#e', "(!empty(\$user->lang['\\1'])) ? \$user->lang['\\1'] : '\\1'", $error);
				}

				if (!isset($data['bday_day']))
				{
					if ($user_row['user_birthday'])
					{
						list($data['bday_day'], $data['bday_month'], $data['bday_year']) = explode('-', $user_row['user_birthday']);
					}
					else
					{
						$data['bday_day'] = $data['bday_month'] = $data['bday_year'] = 0;
					}
				}

				$s_birthday_day_options = '<option value="0"' . ((!$data['bday_day']) ? ' selected="selected"' : '') . '>--</option>';
				for ($i = 1; $i < 32; $i++)
				{
					$selected = ($i == $data['bday_day']) ? ' selected="selected"' : '';
					$s_birthday_day_options .= "<option value=\"$i\"$selected>$i</option>";
				}

				$s_birthday_month_options = '<option value="0"' . ((!$data['bday_month']) ? ' selected="selected"' : '') . '>--</option>';
				for ($i = 1; $i < 13; $i++)
				{
					$selected = ($i == $data['bday_month']) ? ' selected="selected"' : '';
					$s_birthday_month_options .= "<option value=\"$i\"$selected>$i</option>";
				}
				$s_birthday_year_options = '';

				$now = getdate();
				$s_birthday_year_options = '<option value="0"' . ((!$data['bday_year']) ? ' selected="selected"' : '') . '>--</option>';
				for ($i = $now['year'] - 100; $i < $now['year']; $i++)
				{
					$selected = ($i == $data['bday_year']) ? ' selected="selected"' : '';
					$s_birthday_year_options .= "<option value=\"$i\"$selected>$i</option>";
				}
				unset($now);

				$template->assign_vars(array(
					'ICQ'			=> (isset($data['icq'])) ? $data['icq'] : $user_row['user_icq'],
					'YIM'			=> (isset($data['yim'])) ? $data['yim'] : $user_row['user_yim'],
					'AIM'			=> (isset($data['aim'])) ? $data['aim'] : $user_row['user_aim'],
					'MSN'			=> (isset($data['msn'])) ? $data['msn'] : $user_row['user_msnm'],
					'JABBER'		=> (isset($data['jabber'])) ? $data['jabber'] : $user_row['user_jabber'],
					'WEBSITE'		=> (isset($data['website'])) ? $data['website']: $user_row['user_website'],
					'LOCATION'		=> (isset($data['location'])) ? $data['location'] : $user_row['user_from'],
					'OCCUPATION'	=> (isset($data['occupation'])) ? $data['occupation'] : $user_row['user_occ'],
					'INTERESTS'		=> (isset($data['interests'])) ? $data['interests'] : $user_row['user_interests'],

					'S_BIRTHDAY_DAY_OPTIONS'	=> $s_birthday_day_options,
					'S_BIRTHDAY_MONTH_OPTIONS'	=> $s_birthday_month_options,
					'S_BIRTHDAY_YEAR_OPTIONS'	=> $s_birthday_year_options,
						
					'S_PROFILE'		=> true)
				);

				// Get additional profile fields and assign them to the template block var 'profile_fields'
				$user->get_profile_fields($user_id);

				$cp->generate_profile_fields('profile', $user_row['iso_lang_id']);

			break;

			case 'prefs':

				$data = array();

				if ($submit)
				{
					$var_ary = array(
						'dateformat'		=> (string) $config['default_dateformat'],
						'lang'				=> (string) $config['default_lang'],
						'tz'				=> (float) $config['board_timezone'],
						'style'				=> (int) $config['default_style'],
						'dst'				=> (bool) $config['board_dst'],
						'viewemail'			=> false,
						'massemail'			=> true,
						'hideonline'		=> false,
						'notifymethod'		=> 0,
						'notifypm'			=> true,
						'popuppm'			=> false,
						'allowpm'			=> true,

						'topic_sk'			=> (string) 't',
						'topic_sd'			=> (string) 'd',
						'topic_st'			=> 0,

						'post_sk'			=> (string) 't',
						'post_sd'			=> (string) 'a',
						'post_st'			=> 0,

						'view_images'		=> true,
						'view_flash'		=> false,
						'view_smilies'		=> true,
						'view_sigs'			=> true,
						'view_avatars'		=> true,
						'view_wordcensor'	=> false,

						'bbcode'	=> true,
						'smilies'	=> true,
						'sig'		=> true,
						'notify'	=> false,
					);

					foreach ($var_ary as $var => $default)
					{
						$data[$var] = request_var($var, $default);
					}

					$var_ary = array(
						'dateformat'	=> array('string', false, 3, 30),
						'lang'			=> array('match', false, '#^[a-z_]{2,}$#i'),
						'tz'			=> array('num', false, -14, 14),

						'topic_sk'		=> array('string', false, 1, 1),
						'topic_sd'		=> array('string', false, 1, 1),
						'post_sk'		=> array('string', false, 1, 1),
						'post_sd'		=> array('string', false, 1, 1),
					);

					$error = validate_data($data, $var_ary);

					if (!sizeof($error))
					{
						$this->optionset($user_row, 'popuppm', $data['popuppm']);
						$this->optionset($user_row, 'viewimg', $data['view_images']);
						$this->optionset($user_row, 'viewflash', $data['view_flash']);
						$this->optionset($user_row, 'viewsmilies', $data['view_smilies']);
						$this->optionset($user_row, 'viewsigs', $data['view_sigs']);
						$this->optionset($user_row, 'viewavatars', $data['view_avatars']);
						$this->optionset($user_row, 'viewcensors', $data['view_wordcensor']);
						$this->optionset($user_row, 'bbcode', $data['bbcode']);
						$this->optionset($user_row, 'smilies', $data['smilies']);
						$this->optionset($user_row, 'attachsig', $data['sig']);

						$sql_ary = array(
							'user_options'			=> $user_row['user_options'],

							'user_allow_pm'			=> $data['allowpm'],
							'user_allow_viewemail'	=> $data['viewemail'],
							'user_allow_massemail'	=> $data['massemail'],
							'user_allow_viewonline'	=> !$data['hideonline'],
							'user_notify_type'		=> $data['notifymethod'],
							'user_notify_pm'		=> $data['notifypm'],

							'user_dst'				=> $data['dst'],
							'user_dateformat'		=> $data['dateformat'],
							'user_lang'				=> $data['lang'],
							'user_timezone'			=> $data['tz'],
							'user_style'			=> $data['style'],

							'user_topic_sortby_type'	=> $data['topic_sk'],
							'user_post_sortby_type'		=> $data['post_sk'],
							'user_topic_sortby_dir'		=> $data['topic_sd'],
							'user_post_sortby_dir'		=> $data['post_sd'],

							'user_topic_show_days'	=> $data['topic_st'],
							'user_post_show_days'	=> $data['post_st'],

							'user_notify'	=> $data['notify'],
						);

						$sql = 'UPDATE ' . USERS_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', $sql_ary) . "
							WHERE user_id = $user_id";
						$db->sql_query($sql);

						trigger_error($user->lang['USER_PREFS_UPDATED'] . adm_back_link($this->u_action . '&amp;u=' . $user_id));
					}

					// Replace "error" strings with their real, localised form
					$error = preg_replace('#^([A-Z_]+)$#e', "(!empty(\$user->lang['\\1'])) ? \$user->lang['\\1'] : '\\1'", $error);
				}

				$notify_method = (isset($data['notifymethod'])) ? $data['notifymethod'] : $user_row['user_notify_type'];
				$dateformat = (isset($data['dateformat'])) ? $data['dateformat'] : $user_row['user_dateformat'];
				$lang = (isset($data['lang'])) ? $data['lang'] : $user_row['user_lang'];
				$style = (isset($data['style'])) ? $data['style'] : $user_row['user_style'];
				$tz = (isset($data['tz'])) ? $data['tz'] : $user_row['user_timezone'];

				$dateformat_options = '';

				foreach ($user->lang['dateformats'] as $format => $null)
				{
					$dateformat_options .= '<option value="' . $format . '"' . (($format == $dateformat) ? ' selected="selected"' : '') . '>';
					$dateformat_options .= $user->format_date(time(), $format, true) . ((strpos($format, '|') !== false) ? ' [' . $user->lang['RELATIVE_DAYS'] . ']' : '');
					$dateformat_options .= '</option>';
				}

				$s_custom = false;

				$dateformat_options .= '<option value="custom"';
				if (!in_array($dateformat, array_keys($user->lang['dateformats'])))
				{
					$dateformat_options .= ' selected="selected"';
					$s_custom = true;
				}
				$dateformat_options .= '>' . $user->lang['CUSTOM_DATEFORMAT'] . '</option>';

				$topic_sk = (isset($data['topic_sk'])) ? $data['topic_sk'] : (($user_row['user_topic_sortby_type']) ? $user_row['user_topic_sortby_type'] : 't');
				$post_sk = (isset($data['post_sk'])) ? $data['post_sk'] : (($user_row['user_post_sortby_type']) ? $user_row['user_post_sortby_type'] : 't');

				$topic_sd = (isset($data['topic_sd'])) ? $data['topic_sd'] : (($user_row['user_topic_sortby_dir']) ? $user_row['user_topic_sortby_dir'] : 'd');
				$post_sd = (isset($data['post_sd'])) ? $data['post_sd'] : (($user_row['user_post_sortby_dir']) ? $user_row['user_post_sortby_dir'] : 'd');
				
				$topic_st = (isset($data['topic_st'])) ? $data['topic_st'] : (($user_row['user_topic_show_days']) ? $user_row['user_topic_show_days'] : 0);
				$post_st = (isset($data['post_st'])) ? $data['post_st'] : (($user_row['user_post_show_days']) ? $user_row['user_post_show_days'] : 0);

				$sort_dir_text = array('a' => $user->lang['ASCENDING'], 'd' => $user->lang['DESCENDING']);

				// Topic ordering options
				$limit_topic_days = array(0 => $user->lang['ALL_TOPICS'], 1 => $user->lang['1_DAY'], 7 => $user->lang['7_DAYS'], 14 => $user->lang['2_WEEKS'], 30 => $user->lang['1_MONTH'], 90 => $user->lang['3_MONTHS'], 180 => $user->lang['6_MONTHS'], 365 => $user->lang['1_YEAR']);
				$sort_by_topic_text = array('a' => $user->lang['AUTHOR'], 't' => $user->lang['POST_TIME'], 'r' => $user->lang['REPLIES'], 's' => $user->lang['SUBJECT'], 'v' => $user->lang['VIEWS']);

				// Post ordering options
				$limit_post_days = array(0 => $user->lang['ALL_POSTS'], 1 => $user->lang['1_DAY'], 7 => $user->lang['7_DAYS'], 14 => $user->lang['2_WEEKS'], 30 => $user->lang['1_MONTH'], 90 => $user->lang['3_MONTHS'], 180 => $user->lang['6_MONTHS'], 365 => $user->lang['1_YEAR']);
				$sort_by_post_text = array('a' => $user->lang['AUTHOR'], 't' => $user->lang['POST_TIME'], 's' => $user->lang['SUBJECT']);

				$_options = array('topic', 'post');
				foreach ($_options as $sort_option)
				{
					${'s_limit_' . $sort_option . '_days'} = '<select name="' . $sort_option . '_st">';
					foreach (${'limit_' . $sort_option . '_days'} as $day => $text)
					{
						$selected = (${$sort_option . '_st'} == $day) ? ' selected="selected"' : '';
						${'s_limit_' . $sort_option . '_days'} .= '<option value="' . $day . '"' . $selected . '>' . $text . '</option>';
					}
					${'s_limit_' . $sort_option . '_days'} .= '</select>';

					${'s_sort_' . $sort_option . '_key'} = '<select name="' . $sort_option . '_sk">';
					foreach (${'sort_by_' . $sort_option . '_text'} as $key => $text)
					{
						$selected = (${$sort_option . '_sk'} == $key) ? ' selected="selected"' : '';
						${'s_sort_' . $sort_option . '_key'} .= '<option value="' . $key . '"' . $selected . '>' . $text . '</option>';
					}
					${'s_sort_' . $sort_option . '_key'} .= '</select>';

					${'s_sort_' . $sort_option . '_dir'} = '<select name="' . $sort_option . '_sd">';
					foreach ($sort_dir_text as $key => $value)
					{
						$selected = (${$sort_option . '_sd'} == $key) ? ' selected="selected"' : '';
						${'s_sort_' . $sort_option . '_dir'} .= '<option value="' . $key . '"' . $selected . '>' . $value . '</option>';
					}
					${'s_sort_' . $sort_option . '_dir'} .= '</select>';
				}

				$template->assign_vars(array(
					'S_PREFS'			=> true,
					'S_JABBER_DISABLED'	=> ($config['jab_enable'] && $user->data['user_jabber'] && @extension_loaded('xml')) ? false : true,
					
					'VIEW_EMAIL'		=> (isset($data['viewemail'])) ? $data['viewemail'] : $user_row['user_allow_viewemail'],
					'MASS_EMAIL'		=> (isset($data['massemail'])) ? $data['massemail'] : $user_row['user_allow_massemail'],
					'ALLOW_PM'			=> (isset($data['allowpm'])) ? $data['allowpm'] : $user_row['user_allow_pm'],
					'HIDE_ONLINE'		=> (isset($data['hideonline'])) ? $data['hideonline'] : !$user_row['user_allow_viewonline'],
					'NOTIFY_EMAIL'		=> ($notify_method == NOTIFY_EMAIL) ? true : false,
					'NOTIFY_IM'			=> ($notify_method == NOTIFY_IM) ? true : false,
					'NOTIFY_BOTH'		=> ($notify_method == NOTIFY_BOTH) ? true : false,
					'NOTIFY_PM'			=> (isset($data['notifypm'])) ? $data['notifypm'] : $user_row['user_notify_pm'],
					'POPUP_PM'			=> (isset($data['popuppm'])) ? $data['popuppm'] : $this->optionget($user_row, 'popuppm'),
					'DST'				=> (isset($data['dst'])) ? $data['dst'] : $user_row['user_dst'],
					'BBCODE'			=> (isset($data['bbcode'])) ? $data['bbcode'] : $this->optionget($user_row, 'bbcode'),
					'SMILIES'			=> (isset($data['smilies'])) ? $data['smilies'] : $this->optionget($user_row, 'smilies'),
					'ATTACH_SIG'		=> (isset($data['sig'])) ? $data['sig'] : $this->optionget($user_row, 'attachsig'),
					'NOTIFY'			=> (isset($data['notify'])) ? $data['notify'] : $user_row['user_notify'],
					'VIEW_IMAGES'		=> (isset($data['view_images'])) ? $data['view_images'] : $this->optionget($user_row, 'viewimg'),
					'VIEW_FLASH'		=> (isset($data['view_flash'])) ? $data['view_flash'] : $this->optionget($user_row, 'viewflash'),
					'VIEW_SMILIES'		=> (isset($data['view_smilies'])) ? $data['view_smilies'] : $this->optionget($user_row, 'viewsmilies'),
					'VIEW_SIGS'			=> (isset($data['view_sigs'])) ? $data['view_sigs'] : $this->optionget($user_row, 'viewsigs'),
					'VIEW_AVATARS'		=> (isset($data['view_avatars'])) ? $data['view_avatars'] : $this->optionget($user_row, 'viewavatars'),
					'VIEW_WORDCENSOR'	=> (isset($data['view_wordcensor'])) ? $data['view_wordcensor'] : $this->optionget($user_row, 'viewcensors'),
					
					'S_TOPIC_SORT_DAYS'		=> $s_limit_topic_days,
					'S_TOPIC_SORT_KEY'		=> $s_sort_topic_key,
					'S_TOPIC_SORT_DIR'		=> $s_sort_topic_dir,
					'S_POST_SORT_DAYS'		=> $s_limit_post_days,
					'S_POST_SORT_KEY'		=> $s_sort_post_key,
					'S_POST_SORT_DIR'		=> $s_sort_post_dir,

					'DATE_FORMAT'			=> $dateformat,
					'S_DATEFORMAT_OPTIONS'	=> $dateformat_options,
					'S_CUSTOM_DATEFORMAT'	=> $s_custom,
					'DEFAULT_DATEFORMAT'	=> $config['default_dateformat'],
					'A_DEFAULT_DATEFORMAT'	=> addslashes($config['default_dateformat']),

					'S_LANG_OPTIONS'	=> language_select($lang),
					'S_STYLE_OPTIONS'	=> style_select($style),
					'S_TZ_OPTIONS'		=> tz_select($tz, true),
					)
				);

			break;

			case 'avatar':

				$avatar_select = basename(request_var('avatar_select', ''));
				$category = basename(request_var('category', ''));
				$can_upload = (file_exists($phpbb_root_path . $config['avatar_path']) && is_writeable($phpbb_root_path . $config['avatar_path']) && $file_uploads) ? true : false;

				$data = array();

				if ($submit)
				{
					$delete = request_var('delete', '');

					$var_ary = array(
						'uploadurl'		=> (string) '',
						'remotelink'	=> (string) '',
						'width'			=> (string) '',
						'height'		=> (string) '',
					);

					foreach ($var_ary as $var => $default)
					{
						$data[$var] = request_var($var, $default);
					}

					$var_ary = array(
						'uploadurl'		=> array('string', true, 5, 255),
						'remotelink'	=> array('string', true, 5, 255),
						'width'			=> array('string', true, 1, 3),
						'height'		=> array('string', true, 1, 3),
					);

					$error = validate_data($data, $var_ary);

					if (!sizeof($error))
					{
						$data['user_id'] = $user_id;

						if ((!empty($_FILES['uploadfile']['name']) || $data['uploadurl']) && $can_upload && $config['allow_avatar_upload'])
						{
							list($type, $filename, $width, $height) = avatar_upload($data, $error);
						}
						else if ($data['remotelink'] && $config['allow_avatar_remote'])
						{
							list($type, $filename, $width, $height) = avatar_remote($data, $error);
						}
						else if ($avatar_select && $config['allow_avatar_local'])
						{
							$type = AVATAR_GALLERY;
							$filename = $avatar_select;
							
							// check avatar gallery
							if (!is_dir($phpbb_root_path . $config['avatar_gallery_path'] . '/' . $category))
							{
								$type = $width = $height = 0;
								$filename = '';
							}
							else
							{
								list($width, $height) = getimagesize($phpbb_root_path . $config['avatar_gallery_path'] . '/' . $category . '/' . $filename);
								$filename = $category . '/' . $filename;
							}
						}
						else if ($delete)
						{
							$filename = '';
							$type = $width = $height = 0;
						}
						else
						{
							$data = array();
						}
					}

					if (!sizeof($error))
					{
						// Do we actually have any data to update?
						if (sizeof($data))
						{
							$sql_ary = array(
								'user_avatar'			=> $filename,
								'user_avatar_type'		=> $type,
								'user_avatar_width'		=> $width,
								'user_avatar_height'	=> $height,
							);

							$sql = 'UPDATE ' . USERS_TABLE . '
								SET ' . $db->sql_build_array('UPDATE', $sql_ary) . '
								WHERE user_id = ' . $user_id;
							$db->sql_query($sql);

							// Delete old avatar if present
							if ($user_row['user_avatar'] && $filename != $user_row['user_avatar'] && $user_row['user_avatar_type'] != AVATAR_GALLERY)
							{
								avatar_delete($user_row['user_avatar']);
							}
						}

						trigger_error($user->lang['USER_AVATAR_UPDATED'] . adm_back_link($this->u_action . '&amp;u=' . $user_id));
					}

					// Replace "error" strings with their real, localised form
					$error = preg_replace('#^([A-Z_]+)$#e', "(!empty(\$user->lang['\\1'])) ? \$user->lang['\\1'] : '\\1'", $error);
				}

				// Generate users avatar
				if ($user_row['user_avatar'])
				{
					$avatar_img = '';

					switch ($user_row['user_avatar_type'])
					{
						case AVATAR_UPLOAD:
							$avatar_img = $phpbb_root_path . $config['avatar_path'] . '/';
						break;
				
						case AVATAR_GALLERY:
							$avatar_img = $phpbb_root_path . $config['avatar_gallery_path'] . '/';
						break;
					}

					$avatar_img .= $user_row['user_avatar'];
					$avatar_img = '<img src="' . $avatar_img . '" width="' . $user_row['user_avatar_width'] . '" height="' . $user_row['user_avatar_height'] . '" alt="" />';
				}
				else
				{
					$avatar_img = '<img src="' . $phpbb_admin_path . 'images/no_avatar.gif" alt="" />';
				}

				$display_gallery = (isset($_POST['display_gallery'])) ? true : false;

				if ($config['allow_avatar_local'] && $display_gallery)
				{
					avatar_gallery($category, $avatar_select, 4);
				}

				$template->assign_vars(array(
					'S_AVATAR'			=> true,
					'S_CAN_UPLOAD'		=> ($can_upload && $config['allow_avatar_upload']) ? true : false,
					'S_ALLOW_REMOTE'	=> ($config['allow_avatar_remote']) ? true : false,
					'S_DISPLAY_GALLERY'	=> ($config['allow_avatar_local'] && !$display_gallery) ? true : false,
					'S_IN_GALLERY'		=> ($config['allow_avatar_local'] && $display_gallery) ? true : false,

					'AVATAR_IMAGE'			=> $avatar_img,
					'AVATAR_MAX_FILESIZE'	=> $config['avatar_filesize'],
					'USER_AVATAR_WIDTH'		=> $user_row['user_avatar_width'],
					'USER_AVATAR_HEIGHT'	=> $user_row['user_avatar_height'],

					'L_AVATAR_EXPLAIN'	=> sprintf($user->lang['AVATAR_EXPLAIN'], $config['avatar_max_width'], $config['avatar_max_height'], round($config['avatar_filesize'] / 1024)))
				);

			break;

			case 'rank':

				if ($submit)
				{
					$rank_id = request_var('user_rank', 0);

					$sql = 'UPDATE ' . USERS_TABLE . "
						SET user_rank = $rank_id
						WHERE user_id = $user_id";
					$db->sql_query($sql);

					trigger_error($user->lang['USER_RANK_UPDATED'] . adm_back_link($this->u_action . '&amp;u=' . $user_id));
				}
				
				$sql = 'SELECT * 
					FROM ' . RANKS_TABLE . '
					WHERE rank_special = 1
					ORDER BY rank_title';
				$result = $db->sql_query($sql);

				$s_rank_options = '<option value="0"' . ((!$user_row['user_rank']) ? ' selected="selected"' : '') . '>' . $user->lang['NO_SPECIAL_RANK'] . '</option>';

				while ($row = $db->sql_fetchrow($result))
				{
					$selected = ($user_row['user_rank'] && $row['rank_id'] == $user_row['user_rank']) ? ' selected="selected"' : '';
					$s_rank_options .= '<option value="' . $row['rank_id'] . '"' . $selected . '>' . $row['rank_title'] . '</option>';
				}
				$db->sql_freeresult($result);

				$template->assign_vars(array(
					'S_RANK'			=> true,
					'S_RANK_OPTIONS'	=> $s_rank_options)
				);

			break;
			
			case 'sig':
			
				include_once($phpbb_root_path . 'includes/functions_posting.' . $phpEx);

				$enable_bbcode	= ($config['allow_sig_bbcode']) ? request_var('enable_bbcode', $this->optionget($user_row, 'bbcode')) : false;
				$enable_smilies	= ($config['allow_sig_smilies']) ? request_var('enable_smilies', $this->optionget($user_row, 'smilies')) : false;
				$enable_urls	= request_var('enable_urls', true);
				$signature		= request_var('signature', $user_row['user_sig'], true);
				
				$preview		= (isset($_POST['preview'])) ? true : false;

				if ($submit || $preview)
				{
					include_once($phpbb_root_path . 'includes/message_parser.' . $phpEx);

					$message_parser = new parse_message($signature);

					// Allowing Quote BBCode
					$message_parser->parse($enable_bbcode, $enable_urls, $enable_smilies, $config['allow_sig_img'], $config['allow_sig_flash'], true, true, 'sig');
						
					if (sizeof($message_parser->warn_msg))
					{
						$error[] = implode('<br />', $message_parser->warn_msg);
					}
						
					if (!sizeof($error) && $submit)
					{
						$sql_ary = array(
							'user_sig'					=> (string) $message_parser->message, 
							'user_sig_bbcode_uid'		=> (string) $message_parser->bbcode_uid, 
							'user_sig_bbcode_bitfield'	=> (int) $message_parser->bbcode_bitfield
						);

						$sql = 'UPDATE ' . USERS_TABLE . ' 
							SET ' . $db->sql_build_array('UPDATE', $sql_ary) . ' 
							WHERE user_id = ' . $user_id;
						$db->sql_query($sql);

						trigger_error($user->lang['USER_SIG_UPDATED'] . adm_back_link($this->u_action . '&amp;u=' . $user_id));
					}
	
					// Replace "error" strings with their real, localised form
					$error = preg_replace('#^([A-Z_]+)$#e', "(!empty(\$user->lang['\\1'])) ? \$user->lang['\\1'] : '\\1'", $error);
				}
				
				$signature_preview = '';
				
				if ($preview)
				{
					// Now parse it for displaying
					$signature_preview = $message_parser->format_display($enable_bbcode, $enable_urls, $enable_smilies, false);
					unset($message_parser);
				}

				decode_message($signature, $user_row['user_sig_bbcode_uid']);

				$template->assign_vars(array(
					'S_SIGNATURE'		=> true,

					'SIGNATURE'			=> $signature,
					'SIGNATURE_PREVIEW'	=> $signature_preview,

					'S_BBCODE_CHECKED'		=> (!$enable_bbcode) ? 'checked="checked"' : '',
					'S_SMILIES_CHECKED'		=> (!$enable_smilies) ? 'checked="checked"' : '',
					'S_MAGIC_URL_CHECKED'	=> (!$enable_urls) ? 'checked="checked"' : '',

					'BBCODE_STATUS'			=> ($config['allow_sig_bbcode']) ? sprintf($user->lang['BBCODE_IS_ON'], '<a href="' . append_sid("{$phpbb_root_path}faq.$phpEx", 'mode=bbcode') . '" onclick="target=\'_phpbbcode\';">', '</a>') : sprintf($user->lang['BBCODE_IS_OFF'], '<a href="' . append_sid("{$phpbb_root_path}faq.$phpEx", 'mode=bbcode') . '" onclick="target=\'_phpbbcode\';">', '</a>'),
					'SMILIES_STATUS'		=> ($config['allow_sig_smilies']) ? $user->lang['SMILIES_ARE_ON'] : $user->lang['SMILIES_ARE_OFF'],
					'IMG_STATUS'			=> ($config['allow_sig_img']) ? $user->lang['IMAGES_ARE_ON'] : $user->lang['IMAGES_ARE_OFF'],
					'FLASH_STATUS'			=> ($config['allow_sig_flash']) ? $user->lang['FLASH_IS_ON'] : $user->lang['FLASH_IS_OFF'],

					'L_SIGNATURE_EXPLAIN'	=> sprintf($user->lang['SIGNATURE_EXPLAIN'], $config['max_sig_chars']),

					'S_BBCODE_ALLOWED'		=> $config['allow_sig_bbcode'], 
					'S_SMILIES_ALLOWED'		=> $config['allow_sig_smilies'],)
				);

			break;

			case 'attach':

				$start		= request_var('start', 0);
				$deletemark = (isset($_POST['delmarked'])) ? true : false;
				$marked		= request_var('mark', array(0));

				// Sort keys
				$sort_key	= request_var('sk', 'a');
				$sort_dir	= request_var('sd', 'd');

				if ($deletemark && sizeof($marked))
				{
					if (confirm_box(true))
					{
						$sql = 'SELECT real_filename
							FROM ' . ATTACHMENTS_TABLE . '
							WHERE attach_id IN (' . implode(', ', $marked) . ')';
						$result = $db->sql_query($sql);

						$log_attachments = array();
						while ($row = $db->sql_fetchrow($result))
						{
							$log_attachments[] = $row['real_filename'];
						}
						$db->sql_freeresult($result);

						delete_attachments('attach', $marked);

						$log = (sizeof($log_attachments) == 1) ? 'ATTACHMENT_DELETED' : 'ATTACHMENTS_DELETED';
						$message = (sizeof($log_attachments) == 1) ? $user->lang['ATTACHMENT_DELETED'] : $user->lang['ATTACHMENTS_DELETED'];

						add_log('admin', $log, implode(', ', $log_attachments));
						trigger_error($message . adm_back_link($this->u_action . '&amp;u=' . $user_id));
					}
					else
					{
						confirm_box(false, $user->lang['CONFIRM_OPERATION'], build_hidden_fields(array(
							'u'				=> $user_id,
							'i'				=> $id,
							'mode'			=> $mode,
							'action'		=> $action,
							'deletemark'	=> true,
							'mark'			=> $marked))
						);
					}
				}

				$sk_text = array('a' => $user->lang['SORT_FILENAME'], 'c' => $user->lang['SORT_EXTENSION'], 'd' => $user->lang['SORT_SIZE'], 'e' => $user->lang['SORT_DOWNLOADS'], 'f' => $user->lang['SORT_POST_TIME'], 'g' => $user->lang['SORT_TOPIC_TITLE']);
				$sk_sql = array('a' => 'a.real_filename', 'c' => 'a.extension', 'd' => 'a.filesize', 'e' => 'a.download_count', 'f' => 'a.filetime', 'g' => 't.topic_title');

				$sd_text = array('a' => $user->lang['ASCENDING'], 'd' => $user->lang['DESCENDING']);

				$s_sort_key = '';
				foreach ($sk_text as $key => $value)
				{
					$selected = ($sort_key == $key) ? ' selected="selected"' : '';
					$s_sort_key .= '<option value="' . $key . '"' . $selected . '>' . $value . '</option>';
				}

				$s_sort_dir = '';
				foreach ($sd_text as $key => $value)
				{
					$selected = ($sort_dir == $key) ? ' selected="selected"' : '';
					$s_sort_dir .= '<option value="' . $key . '"' . $selected . '>' . $value . '</option>';
				}

				$order_by = $sk_sql[$sort_key] . '  ' . (($sort_dir == 'a') ? 'ASC' : 'DESC');

				$sql = 'SELECT COUNT(attach_id) as num_attachments
					FROM ' . ATTACHMENTS_TABLE . "
					WHERE poster_id = $user_id";
				$result = $db->sql_query_limit($sql, 1);
				$num_attachments = (int) $db->sql_fetchfield('num_attachments');
				$db->sql_freeresult($result);

				$sql = 'SELECT a.*, t.topic_title, p.message_subject as message_title
					FROM ' . ATTACHMENTS_TABLE . ' a 
						LEFT JOIN ' . TOPICS_TABLE . ' t ON (a.topic_id = t.topic_id
							AND a.in_message = 0)
						LEFT JOIN ' . PRIVMSGS_TABLE . ' p ON (a.post_msg_id = p.msg_id
							AND a.in_message = 1)
					WHERE a.poster_id = ' . $user_id . "
					ORDER BY $order_by";
				$result = $db->sql_query_limit($sql, $config['posts_per_page'], $start);

				while ($row = $db->sql_fetchrow($result))
				{
					if ($row['in_message'])
					{
						$view_topic = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=pm&amp;p={$row['post_msg_id']}");
					}
					else
					{
						$view_topic = append_sid("{$phpbb_root_path}viewtopic.$phpEx", "t={$row['topic_id']}&amp;p={$row['post_msg_id']}#{$row['post_msg_id']}");
					}

					$template->assign_block_vars('attach', array(
						'REAL_FILENAME'		=> $row['real_filename'],
						'COMMENT'			=> nl2br($row['comment']),
						'EXTENSION'			=> $row['extension'],
						'SIZE'				=> ($row['filesize'] >= 1048576) ? ($row['filesize'] >> 20) . ' ' . $user->lang['MB'] : (($row['filesize'] >= 1024) ? ($row['filesize'] >> 10) . ' ' . $user->lang['KB'] : $row['filesize'] . ' ' . $user->lang['BYTES']),
						'DOWNLOAD_COUNT'	=> $row['download_count'],
						'POST_TIME'			=> $user->format_date($row['filetime']),
						'TOPIC_TITLE'		=> ($row['in_message']) ? $row['message_title'] : $row['topic_title'],

						'ATTACH_ID'			=> $row['attach_id'],
						'POST_ID'			=> $row['post_msg_id'],
						'TOPIC_ID'			=> $row['topic_id'],
				
						'S_IN_MESSAGE'		=> $row['in_message'],

						'U_DOWNLOAD'		=> append_sid("{$phpbb_root_path}download.$phpEx", 'id=' . $row['attach_id']),
						'U_VIEW_TOPIC'		=> $view_topic)
					);
				}
				$db->sql_freeresult($result);
		
				$template->assign_vars(array(
					'S_ATTACHMENTS'		=> true,
					'S_ON_PAGE'			=> on_page($num_attachments, $config['topics_per_page'], $start),
					'S_SORT_KEY'		=> $s_sort_key,
					'S_SORT_DIR'		=> $s_sort_dir,

					'PAGINATION'		=> generate_pagination($this->u_action . "&amp;sk=$sort_key&amp;sd=$sort_dir", $num_attachments, $config['topics_per_page'], $start, true))
				);

			break;
		
			case 'groups':

				$user->add_lang(array('groups', 'acp/groups'));
				$group_id = request_var('g', 0);

				switch ($action)
				{
					case 'demote':
					case 'promote':
					case 'default':
						group_user_attributes($action, $group_id, $user_id);

						if ($action == 'default')
						{
							$user_row['group_id'] = $group_id;
						}
					break;

					case 'delete':

						if (confirm_box(true))
						{
							if (!$group_id)
							{
								trigger_error($user->lang['NO_GROUP'] . adm_back_link($this->u_action . '&amp;u=' . $user_id));
							}

							if ($error = group_user_del($group_id, $user_id))
							{
								trigger_error($user->lang[$error] . adm_back_link($this->u_action . '&amp;u=' . $user_id));
							}
						
							$error = array();
						}
						else
						{
							confirm_box(false, $user->lang['CONFIRM_OPERATION'], build_hidden_fields(array(
								'u'				=> $user_id,
								'i'				=> $id,
								'mode'			=> $mode,
								'action'		=> $action,
								'g'				=> $group_id))
							);
						}
	
					break;
				}

				// Add user to group?
				if ($submit)
				{
					if (!$group_id)
					{
						trigger_error($user->lang['NO_GROUP'] . adm_back_link($this->u_action . '&amp;u=' . $user_id));
					}

					// Add user/s to group
					if ($error = group_user_add($group_id, $user_id))
					{
						trigger_error($user->lang[$error] . adm_back_link($this->u_action . '&amp;u=' . $user_id));
					}

					$error = array();
				}


				$sql = 'SELECT ug.*, g.*
					FROM ' . GROUPS_TABLE . ' g, ' . USER_GROUP_TABLE . " ug
					WHERE ug.user_id = $user_id
						AND g.group_id = ug.group_id
					ORDER BY g.group_type DESC, ug.user_pending ASC, g.group_name";
				$result = $db->sql_query($sql);

				$i = 0;
				$group_data = $id_ary = array();
				while ($row = $db->sql_fetchrow($result))
				{
					$type = ($row['group_type'] == GROUP_SPECIAL) ? 'special' : (($row['user_pending']) ? 'pending' : 'normal');

					$group_data[$type][$i]['group_id']		= $row['group_id'];
					$group_data[$type][$i]['group_name']	= $row['group_name'];
					$group_data[$type][$i]['group_leader']	= ($row['group_leader']) ? 1 : 0;

					$id_ary[] = $row['group_id'];

					$i++;
				}
				$db->sql_freeresult($result);

				// Select box for other groups
				$sql = 'SELECT group_id, group_name, group_type
					FROM ' . GROUPS_TABLE . '
					' . ((sizeof($id_ary)) ? 'WHERE group_id NOT IN (' . implode(', ', $id_ary) . ')' : '') . '
					ORDER BY group_type DESC, group_name ASC';
				$result = $db->sql_query($sql);

				$s_group_options = '';
				while ($row = $db->sql_fetchrow($result))
				{
					if ($config['coppa_hide_groups'] && in_array($row['group_name'], array('INACTIVE_COPPA', 'REGISTERED_COPPA')))
					{
						continue;
					}
					
					$s_group_options .= '<option' . (($row['group_type'] == GROUP_SPECIAL) ? ' class="sep"' : '') . ' value="' . $row['group_id'] . '">' . (($row['group_type'] == GROUP_SPECIAL) ? $user->lang['G_' . $row['group_name']] : $row['group_name']) . '</option>';
				}
				$db->sql_freeresult($result);

				$current_type = '';
				foreach ($group_data as $group_type => $data_ary)
				{
					if ($current_type != $group_type)
					{
						$template->assign_block_vars('group', array(
							'S_NEW_GROUP_TYPE'		=> true,
							'GROUP_TYPE'			=> $user->lang['USER_GROUP_' . strtoupper($group_type)])
						);
					}

					foreach ($data_ary as $data)
					{
						$template->assign_block_vars('group', array(
							'U_EDIT_GROUP'		=> append_sid("{$phpbb_admin_path}index.$phpEx", "i=groups&amp;mode=manage&amp;action=edit&amp;u=$user_id&amp;g={$data['group_id']}&amp;back_link=acp_users_groups"),
							'U_DEFAULT'			=> $this->u_action . "&amp;action=default&amp;u=$user_id&amp;g=" . $data['group_id'],
							'U_DEMOTE_PROMOTE'	=> $this->u_action . '&amp;action=' . (($data['group_leader']) ? 'demote' : 'promote') . "&amp;u=$user_id&amp;g=" . $data['group_id'],
							'U_DELETE'			=> $this->u_action . "&amp;action=delete&amp;u=$user_id&amp;g=" . $data['group_id'],

							'GROUP_NAME'		=> ($group_type == 'special') ? $user->lang['G_' . $data['group_name']] : $data['group_name'],
							'L_DEMOTE_PROMOTE'	=> ($data['group_leader']) ? $user->lang['GROUP_DEMOTE'] : $user->lang['GROUP_PROMOTE'],

							'S_NO_DEFAULT'		=> ($user_row['group_id'] != $data['group_id']) ? true : false,
							'S_SPECIAL_GROUP'	=> ($group_type == 'special') ? true : false,
							)
						);
					}
				}

				$template->assign_vars(array(
					'S_GROUPS'			=> true,
					'S_GROUP_OPTIONS'	=> $s_group_options)
				);

			break;

			case 'perm':

				include_once($phpbb_root_path . 'includes/acp/auth.' . $phpEx);

				$auth_admin = new auth_admin();

				$user->add_lang('acp/permissions');
				$user->add_lang('acp/permissions_phpbb');

				// Select auth options
				$sql = 'SELECT auth_option, is_local, is_global
					FROM ' . ACL_OPTIONS_TABLE . "
					WHERE auth_option LIKE '%\_'
						AND is_global = 1
					ORDER BY auth_option";
				$result = $db->sql_query($sql);

				while ($row = $db->sql_fetchrow($result))
				{
					$hold_ary = $auth_admin->get_mask('view', $user_id, false, false, $row['auth_option'], 'global', ACL_NEVER);
					$auth_admin->display_mask('view', $row['auth_option'], $hold_ary, 'user', false, false);
				}
				$db->sql_freeresult($result);

				$sql = 'SELECT auth_option, is_local, is_global
					FROM ' . ACL_OPTIONS_TABLE . "
					WHERE auth_option LIKE '%\_'
						AND is_local = 1
					ORDER BY is_global DESC, auth_option";
				$result = $db->sql_query($sql);

				while ($row = $db->sql_fetchrow($result))
				{
					$hold_ary = $auth_admin->get_mask('view', $user_id, false, false, $row['auth_option'], 'local', ACL_NEVER);
					$auth_admin->display_mask('view', $row['auth_option'], $hold_ary, 'user', true, false);
				}
				$db->sql_freeresult($result);

				$template->assign_vars(array(
					'S_PERMISSIONS'				=> true,
					'U_USER_PERMISSIONS'		=> append_sid("{$phpbb_admin_path}index.$phpEx" ,'i=permissions&amp;mode=setting_user_global&amp;user_id[]=' . $user_id),
					'U_USER_FORUM_PERMISSIONS'	=> append_sid("{$phpbb_admin_path}index.$phpEx", 'i=permissions&amp;mode=setting_user_local&amp;user_id[]=' . $user_id))
				);
			
			break;

		}

		// Assign general variables
		$template->assign_vars(array(
			'S_ERROR'			=> (sizeof($error)) ? true : false,
			'ERROR_MSG'			=> (sizeof($error)) ? implode('<br />', $error) : '')
		);
	}

	/**
	* Optionset replacement for this module based on $user->optionset
	*/
	function optionset(&$user_row, $key, $value, $data = false)
	{
		global $user;

		$var = ($data) ? $data : $user_row['user_options'];

		if ($value && !($var & 1 << $user->keyoptions[$key]))
		{
			$var += 1 << $user->keyoptions[$key];
		}
		else if (!$value && ($var & 1 << $user->keyoptions[$key]))
		{
			$var -= 1 << $user->keyoptions[$key];
		}
		else
		{
			return ($data) ? $var : false;
		}

		if (!$data)
		{
			$user_row['user_options'] = $var;
			return true;
		}
		else
		{
			return $var;
		}
	}

	/**
	* Optionget replacement for this module based on $user->optionget
	*/
	function optionget(&$user_row, $key, $data = false)
	{
		global $user;

		$var = ($data) ? $data : $user_row['user_options'];
		return ($var & 1 << $user->keyoptions[$key]) ? true : false;
	}

	/**
	* Check if user is allowed to call this user mode
	*/
	function is_authed($module_auth)
	{
		global $config, $auth;

		$module_auth = trim($module_auth);

		if (!$module_auth)
		{
			return true;
		}

		$is_auth = false;
		eval('$is_auth = (int) (' . preg_replace(array('#acl_([a-z_]+)(,\$id)?#', '#\$id#', '#cfg_([a-z_]+)#'), array('(int) $auth->acl_get("\\1"\\2)', 'true', '(int) $config["\\1"]'), $module_auth) . ');');

		return $is_auth;
	}
}

?>