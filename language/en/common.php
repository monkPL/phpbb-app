<?php
/**
*
* common [English]
*
* @package language
* @version $Id$
* @copyright (c) 2005 phpBB Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

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
//
// Some characters you may want to copy&paste:
// ’ » “ ” …
//

$lang = array_merge($lang, array(
	'TRANSLATION_INFO'	=> '',
	'DIRECTION'			=> 'ltr',
	'DATE_FORMAT'		=> '|d M Y|',	// 01 Jan 2007 (with Relative days enabled)
	'USER_LANG'			=> 'en-gb',

	'1_DAY'			=> '1 day',
	'1_MONTH'		=> '1 month',
	'1_YEAR'		=> '1 year',
	'2_WEEKS'		=> '2 weeks',
	'3_MONTHS'		=> '3 months',
	'6_MONTHS'		=> '6 months',
	'7_DAYS'		=> '7 days',

	'ACCOUNT_ALREADY_ACTIVATED'		=> 'Your account has already been activated.',
	'ACCOUNT_DEACTIVATED'			=> 'Your account has been manually deactivated and is only able to be reactivated by an administrator.',
	'ACCOUNT_NOT_ACTIVATED'			=> 'Your account has not been activated yet.',
	'ACP'							=> 'Administration Control Panel',
	'ACTIVE'						=> 'active',
	'ACTIVE_ERROR'					=> 'The specified username is currently inactive. If you have problems activating your account, please contact a board administrator.',
	'ADMINISTRATOR'					=> 'Administrator',
	'ADMINISTRATORS'				=> 'Administrators',
	'AGE'							=> 'Age',
	'AIM'							=> 'AIM',
	'ALLOWED'						=> 'Allowed',
	'ALL_FILES'						=> 'All files',
	'ALL_FORUMS'					=> 'All forums',
	'ALL_MESSAGES'					=> 'All messages',
	'ALL_POSTS'						=> 'All posts',
	'ALL_TIMES'						=> 'All times are %1$s %2$s',
	'ALL_TOPICS'					=> 'All Topics',
	'AND'							=> 'And',
	'ARE_WATCHING_FORUM'			=> 'You have subscribed to be notified of new posts in this forum.',
	'ARE_WATCHING_TOPIC'			=> 'You have subscribed to be notified of new posts in this topic.',
	'ASCENDING'						=> 'Ascending',
	'ATTACHMENTS'					=> 'Attachments',
	'ATTACHED_IMAGE_NOT_IMAGE'		=> 'The image file you tried to attach is invalid.',
	'AUTHOR'						=> 'Author',
	'AUTH_NO_PROFILE_CREATED'		=> 'The creation of a user profile was unsuccessful.',
	'AVATAR_DISALLOWED_EXTENSION'	=> 'This file cannot be displayed because the extension <strong>%s</strong> is not allowed.',
	'AVATAR_EMPTY_REMOTE_DATA'		=> 'The specified avatar could not be uploaded because the remote data appears to be invalid or corrupted.',
	'AVATAR_EMPTY_FILEUPLOAD'		=> 'The uploaded avatar file is empty.',
	'AVATAR_INVALID_FILENAME'		=> '%s is an invalid filename.',
	'AVATAR_NOT_UPLOADED'			=> 'Avatar could not be uploaded.',
	'AVATAR_NO_SIZE'				=> 'The width or height of the linked avatar could not be determined. Please enter them manually.',
	'AVATAR_PARTIAL_UPLOAD'			=> 'The specified file was only partially uploaded.',
	'AVATAR_PHP_SIZE_NA'			=> 'The avatar’s filesize is too large.<br />The maximum allowed filesize set in php.ini could not be determined.',
	'AVATAR_PHP_SIZE_OVERRUN'		=> 'The avatar’s filesize is too large. The maximum allowed upload size is %d MB.<br />Please note this is set in php.ini and cannot be overridden.',
	'AVATAR_URL_INVALID'			=> 'The URL you specified is invalid.',
	'AVATAR_URL_NOT_FOUND'			=> 'The file specified could not be found.',
	'AVATAR_WRONG_FILESIZE'			=> 'The avatar’s filesize must be between 0 and %1d %2s.',
	'AVATAR_WRONG_SIZE'				=> 'The submitted avatar is %5$d pixels wide and %6$d pixels high. Avatars must be at least %1$d pixels wide and %2$d pixels high, but no larger than %3$d pixels wide and %4$d pixels high.',

	'BACK_TO_TOP'			=> 'Top',
	'BACK_TO_PREV'			=> 'Back to previous page',
	'BAN_TRIGGERED_BY_EMAIL'=> 'A ban has been issued on your e-mail address.',
	'BAN_TRIGGERED_BY_IP'	=> 'A ban has been issued on your IP address.',
	'BAN_TRIGGERED_BY_USER'	=> 'A ban has been issued on your username.',
	'BBCODE_GUIDE'			=> 'BBCode guide',
	'BCC'					=> 'BCC',
	'BIRTHDAYS'				=> 'Birthdays',
	'BOARD_BAN_PERM'		=> 'You have been <strong>permanently</strong> banned from this board.<br /><br />Please contact the %2$sBoard Administrator%3$s for more information.',
	'BOARD_BAN_REASON'		=> 'Reason given for ban: <strong>%s</strong>',
	'BOARD_BAN_TIME'		=> 'You have been banned from this board until <strong>%1$s</strong>.<br /><br />Please contact the %2$sBoard Administrator%3$s for more information.',
	'BOARD_DISABLE'			=> 'Sorry but this board is currently unavailable.',
	'BOARD_DISABLED'		=> 'This board is currently disabled.',
	'BOARD_UNAVAILABLE'		=> 'Sorry but the board is temporarily unavailable, please try again in a few minutes.',
	'BROWSING_FORUM_GUEST'	=> 'Users browsing this forum: %1$s and %2$d guest',
	'BROWSING_FORUM_GUESTS'	=> 'Users browsing this forum: %1$s and %2$d guests',
	'BYTES'					=> 'Bytes',

	'CANCEL'				=> 'Cancel',
	'CHANGE'				=> 'Change',
	'CHANGE_FONT_SIZE'		=> 'Change font size',
	'CHANGING_PREFERENCES'	=> 'Changing board preferences',
	'CHANGING_PROFILE'		=> 'Changing profile settings',
	'CLICK_VIEW_PRIVMSG'	=> '%sGo to your inbox%s',
	'COLLAPSE_VIEW'			=> 'Collapse view',
	'CLOSE_WINDOW'			=> 'Close window',
	'COLOUR_SWATCH'			=> 'Colour swatch',
	'COMMA_SEPARATOR'		=> ', ',	// Used in pagination of ACP & prosilver, use localised comma if appropriate, eg: Ideographic or Arabic
	'CONFIRM'				=> 'Confirm',
	'CONFIRM_CODE'			=> 'Confirmation code',
	'CONFIRM_CODE_EXPLAIN'	=> 'Enter the code exactly as it appears. All letters are case insensitive, there is no zero.',
	'CONFIRM_CODE_WRONG'	=> 'The confirmation code you entered was incorrect.',
	'CONFIRM_OPERATION'		=> 'Are you sure you wish to carry out this operation?',
	'CONGRATULATIONS'		=> 'Congratulations to',
	'CONNECTION_FAILED'		=> 'Connection failed.',
	'CONNECTION_SUCCESS'	=> 'Connection was successful!',
	'COOKIES_DELETED'		=> 'All board cookies successfully deleted.',
	'CURRENT_TIME'			=> 'It is currently %s',

	'DAY'					=> 'Day',
	'DAYS'					=> 'Days',
	'DELETE'				=> 'Delete',
	'DELETE_ALL'			=> 'Delete all',
	'DELETE_COOKIES'		=> 'Delete all board cookies',
	'DELETE_MARKED'			=> 'Delete marked',
	'DELETE_POST'			=> 'Delete post',
	'DELIMITER'				=> 'Delimiter',
	'DESCENDING'			=> 'Descending',
	'DISABLED'				=> 'Disabled',
	'DISPLAY'				=> 'Display',
	'DISPLAY_GUESTS'		=> 'Display guests',
	'DISPLAY_MESSAGES'		=> 'Display messages from previous',
	'DISPLAY_POSTS'			=> 'Display posts from previous',
	'DISPLAY_TOPICS'		=> 'Display topics from previous',
	'DOWNLOADED'			=> 'Downloaded',
	'DOWNLOADING_FILE'		=> 'Downloading file',
	'DOWNLOAD_COUNT'		=> 'Downloaded %d time',
	'DOWNLOAD_COUNTS'		=> 'Downloaded %d times',
	'DOWNLOAD_COUNT_NONE'	=> 'Not downloaded yet',
	'VIEWED_COUNT'			=> 'Viewed %d time',
	'VIEWED_COUNTS'			=> 'Viewed %d times',
	'VIEWED_COUNT_NONE'		=> 'Not viewed yet',

	'EDIT_POST'							=> 'Edit post',
	'EMAIL'								=> 'E-mail',
	'EMAIL_ADDRESS'						=> 'E-mail address',
	'EMAIL_SMTP_ERROR_RESPONSE'			=> 'Ran into problems sending e-mail at <strong>Line %1$s</strong>. Response: %2$s.',
	'EMPTY_SUBJECT'						=> 'You must specify a subject when posting a new topic.',
	'EMPTY_MESSAGE_SUBJECT'				=> 'You must specify a subject when composing a new message.',
	'ENABLED'							=> 'Enabled',
	'ENCLOSURE'							=> 'Enclosure',
	'ERR_CHANGING_DIRECTORY'			=> 'Unable to change directory.',
	'ERR_CONNECTING_SERVER'				=> 'Error connecting to the server.',
	'ERR_JAB_AUTH'						=> 'Could not authorise on Jabber server.',
	'ERR_JAB_CONNECT'					=> 'Could not connect to Jabber server.',
	'ERR_UNABLE_TO_LOGIN'				=> 'The specified username or password is incorrect.',
	'ERR_WRONG_PATH_TO_PHPBB'			=> 'The phpBB path specified appears to be invalid.',
	'EXPAND_VIEW'						=> 'Expand view',
	'EXTENSION'							=> 'Extension',
	'EXTENSION_DISABLED_AFTER_POSTING'	=> 'The extension <strong>%s</strong> has been deactivated and can no longer be displayed.',

	'FAQ'					=> 'FAQ',
	'FAQ_EXPLAIN'			=> 'Frequently Asked Questions',
	'FILENAME'				=> 'Filename',
	'FILESIZE'				=> 'File size',
	'FILEDATE'				=> 'File date',
	'FILE_COMMENT'			=> 'File comment',
	'FILE_NOT_FOUND'		=> 'The requested file could not be found.',
	'FIND_USERNAME'			=> 'Find a member',
	'FOLDER'				=> 'Folder',
	'FORGOT_PASS'			=> 'I forgot my password',
	'FORM_INVALID'			=> 'The submitted form was invalid. Try submitting again.',
	'FORUM'					=> 'Forum',
	'FORUMS'				=> 'Forums',
	'FORUMS_MARKED'			=> 'All forums have been marked read.',
	'FORUM_CAT'				=> 'Forum category',
	'FORUM_INDEX'			=> 'Board index',
	'FORUM_LINK'			=> 'Forum link',
	'FORUM_LOCATION'		=> 'Forum location',
	'FORUM_LOCKED'			=> 'Forum locked',
	'FORUM_RULES'			=> 'Forum rules',
	'FORUM_RULES_LINK'		=> 'Please click here to view the forum rules',
	'FROM'					=> 'from',
	'FSOCK_DISABLED'		=> 'The operation could not be completed because the <var>fsockopen</var> function has been disabled or the server being queried could not be found.',

	'FTP_FSOCK_HOST'				=> 'FTP host',
	'FTP_FSOCK_HOST_EXPLAIN'		=> 'FTP server used to connect your site.',
	'FTP_FSOCK_PASSWORD'			=> 'FTP password',
	'FTP_FSOCK_PASSWORD_EXPLAIN'	=> 'Password for your FTP username.',
	'FTP_FSOCK_PORT'				=> 'FTP port',
	'FTP_FSOCK_PORT_EXPLAIN'		=> 'Port used to connect to your server.',
	'FTP_FSOCK_ROOT_PATH'			=> 'Path to phpBB',
	'FTP_FSOCK_ROOT_PATH_EXPLAIN'	=> 'Path from the root to your phpBB board.',
	'FTP_FSOCK_TIMEOUT'				=> 'FTP timeout',
	'FTP_FSOCK_TIMEOUT_EXPLAIN'		=> 'The amount of time, in seconds, that the system will wait for a reply from your server.',
	'FTP_FSOCK_USERNAME'			=> 'FTP username',
	'FTP_FSOCK_USERNAME_EXPLAIN'	=> 'Username used to connect to your server.',

	'FTP_HOST'					=> 'FTP host',
	'FTP_HOST_EXPLAIN'			=> 'FTP server used to connect your site.',
	'FTP_PASSWORD'				=> 'FTP password',
	'FTP_PASSWORD_EXPLAIN'		=> 'Password for your FTP username.',
	'FTP_PORT'					=> 'FTP port',
	'FTP_PORT_EXPLAIN'			=> 'Port used to connect to your server.',
	'FTP_ROOT_PATH'				=> 'Path to phpBB',
	'FTP_ROOT_PATH_EXPLAIN'		=> 'Path from the root to your phpBB board.',
	'FTP_TIMEOUT'				=> 'FTP timeout',
	'FTP_TIMEOUT_EXPLAIN'		=> 'The amount of time, in seconds, that the system will wait for a reply from your server.',
	'FTP_USERNAME'				=> 'FTP username',
	'FTP_USERNAME_EXPLAIN'		=> 'Username used to connect to your server.',

	'GENERAL_ERROR'				=> 'General Error',
	'GO'						=> 'Go',
	'GOTO_PAGE'					=> 'Go to page',
	'GROUP'						=> 'Group',
	'GROUPS'					=> 'Groups',
	'GROUP_ERR_TYPE'			=> 'Inappropriate group type specified.',
	'GROUP_ERR_USERNAME'		=> 'No group name specified.',
	'GROUP_ERR_USER_LONG'		=> 'Group names cannot exceed 60 characters. The specified group name is too long.',
	'GUEST'						=> 'Guest',
	'GUEST_USERS_ONLINE'		=> 'There are %d guest users online',
	'GUEST_USERS_TOTAL'			=> '%d guests',
	'GUEST_USERS_ZERO_ONLINE'	=> 'There are 0 guest users online',
	'GUEST_USERS_ZERO_TOTAL'	=> '0 guests',
	'GUEST_USER_ONLINE'			=> 'There is %d guest user online',
	'GUEST_USER_TOTAL'			=> '%d guest',
	'G_ADMINISTRATORS'			=> 'Administrators',
	'G_BOTS'					=> 'Bots',
	'G_GUESTS'					=> 'Guests',
	'G_REGISTERED'				=> 'Registered users',
	'G_REGISTERED_COPPA'		=> 'Registered COPPA users',
	'G_GLOBAL_MODERATORS'		=> 'Global moderators',

	'HIDDEN_USERS_ONLINE'		=> '%d hidden users online',
	'HIDDEN_USERS_TOTAL'		=> '%d hidden and ',
	'HIDDEN_USERS_ZERO_ONLINE'	=> '0 hidden users online',
	'HIDDEN_USERS_ZERO_TOTAL'	=> '0 hidden and ',
	'HIDDEN_USER_ONLINE'		=> '%d hidden user online',
	'HIDDEN_USER_TOTAL'			=> '%d hidden and ',
	'HIDE_GUESTS'				=> 'Hide guests',
	'HIDE_ME'					=> 'Hide my online status this session',
	'HOURS'						=> 'Hours',
	'HOME'						=> 'Home',

	'ICQ'						=> 'ICQ',
	'ICQ_STATUS'				=> 'ICQ status',	
	'IF'						=> 'If',
	'IMAGE'						=> 'Image',
	'IMAGE_FILETYPE_INVALID'	=> 'Image file type %d for mimetype %s not supported.',
	'IMAGE_FILETYPE_MISMATCH'	=> 'Image file type mismatch: expected extension %1$s but extension %2$s given.',
	'IN'						=> 'in',
	'INDEX'						=> 'Index page',
	'INFORMATION'				=> 'Information',
	'INTERESTS'					=> 'Interests',
	'INVALID_DIGEST_CHALLENGE'	=> 'Invalid digest challenge.',
	'INVALID_EMAIL_LOG'			=> '<strong>%s</strong> possibly an invalid e-mail address?',
	'IP'						=> 'IP',
	'IP_BLACKLISTED'			=> 'Your IP %1$s has been blocked because it is blacklisted. For details please see <a href="%2$s">%2$s</a>.',

	'JABBER'				=> 'Jabber',
	'JOINED'				=> 'Joined',
	'JUMP_PAGE'				=> 'Enter the page number you wish to go to.',
	'JUMP_TO'				=> 'Jump to',
	'JUMP_TO_PAGE'			=> 'Click to jump to page…',

	'KB'					=> 'KB',

	'LAST_POST'							=> 'Last post',
	'LAST_UPDATED'						=> 'Last updated',
	'LAST_VISIT'						=> 'Last visit',
	'LDAP_NO_LDAP_EXTENSION'			=> 'LDAP extension not available.',
	'LDAP_NO_SERVER_CONNECTION'			=> 'Could not connect to LDAP server.',
	'LEGEND'							=> 'Legend',
	'LOCATION'							=> 'Location',
	'LOCK_POST'							=> 'Lock post',
	'LOCK_POST_EXPLAIN'					=> 'Prevent editing',
	'LOCK_TOPIC'						=> 'Lock topic',
	'LOGIN'								=> 'Login',
	'LOGIN_CHECK_PM'					=> 'Log in to check your private messages.',
	'LOGIN_CONFIRMATION'				=> 'Confirmation of login',
	'LOGIN_CONFIRM_EXPLAIN'				=> 'To prevent brute forcing accounts the board requires you to enter a confirmation code after a maximum amount of failed logins. The code is displayed in the image you should see below. If you are visually impaired or cannot otherwise read this code please contact the %sBoard Administrator%s.',
	'LOGIN_ERROR_ATTEMPTS'				=> 'You exceeded the maximum allowed number of login attempts. In addition to your username and password you now also have to enter the confirm code from the image you see below.',
	'LOGIN_ERROR_EXTERNAL_AUTH_APACHE'	=> 'You have not been authenticated by Apache.',
	'LOGIN_ERROR_PASSWORD'				=> 'You have specified an incorrect password. Please check your password and try again. If you continue to have problems please contact the %sBoard Administrator%s.',
	'LOGIN_ERROR_PASSWORD_CONVERT'		=> 'It was not possible to convert your password when updating this bulletin board’s software. Please %srequest a new password%s. If you continue to have problems please contact the %sBoard Administrator%s.',
	'LOGIN_ERROR_USERNAME'				=> 'You have specified an incorrect username. Please check your username and try again. If you continue to have problems please contact the %sBoard Administrator%s.',
	'LOGIN_FORUM'						=> 'To view or post in this forum you must enter its password.',
	'LOGIN_INFO'						=> 'In order to login you must be registered. Registering takes only a few moments but gives you increased capabilities. The board administrator may also grant additional permissions to registered users. Before you register please ensure you are familiar with our terms of use and related policies. Please ensure you read any forum rules as you navigate around the board.',
	'LOGIN_VIEWFORUM'					=> 'The board requires you to be registered and logged in to view this forum.',
	'LOGIN_EXPLAIN_EDIT'				=> 'In order to edit posts in this forum you have to be registered and logged in.',
	'LOGIN_EXPLAIN_VIEWONLINE'			=> 'In order to view the online list you have to be registered and logged in.',
	'LOGOUT'							=> 'Logout',
	'LOGOUT_USER'						=> 'Logout [ %s ]',
	'LOG_ME_IN'							=> 'Log me on automatically each visit',

	'MARK'					=> 'Mark',
	'MARK_ALL'				=> 'Mark all',
	'MARK_FORUMS_READ'		=> 'Mark forums read',
	'MB'					=> 'MB',
	'MCP'					=> 'Moderator Control Panel',
	'MEMBERLIST'			=> 'Members',
	'MEMBERLIST_EXPLAIN'	=> 'View complete list of members',
	'MERGE'					=> 'Merge',
	'MERGE_POSTS'			=> 'Merge posts',
	'MERGE_TOPIC'			=> 'Merge topic',
	'MESSAGE'				=> 'Message',
	'MESSAGES'				=> 'Messages',
	'MESSAGE_BODY'			=> 'Message body',
	'MINUTES'				=> 'Minutes',
	'MODERATE'				=> 'Moderate',
	'MODERATOR'				=> 'Moderator',
	'MODERATORS'			=> 'Moderators',
	'MONTH'					=> 'Month',
	'MOVE'					=> 'Move',
	'MSNM'					=> 'MSNM/WLM',	

	'NA'						=> 'N/A',
	'NEWEST_USER'				=> 'Our newest member <strong>%s</strong>',
	'NEW_MESSAGE'				=> 'New message',
	'NEW_MESSAGES'				=> 'New messages',
	'NEW_PM'					=> '<strong>%d</strong> new message',
	'NEW_PMS'					=> '<strong>%d</strong> new messages',
	'NEW_POST'					=> 'New post',
	'NEW_POSTS'					=> 'New posts',
	'NEXT'						=> 'Next',		// Used in pagination
	'NEXT_STEP'					=> 'Next',
	'NEVER'						=> 'Never',
	'NO'						=> 'No',
	'NOT_ALLOWED_MANAGE_GROUP'	=> 'You are not allowed to manage this group.',
	'NOT_AUTHORISED'			=> 'You are not authorised to access this area.',
	'NOT_WATCHING_FORUM'		=> 'You are no longer subscribed to updates on this forum.',
	'NOT_WATCHING_TOPIC'		=> 'You are no longer subscribed to this topic.',
	'NOTIFY_ADMIN'				=> 'Please notify the board administrator or webmaster.',
	'NOTIFY_ADMIN_EMAIL'		=> 'Please notify the board administrator or webmaster: <a href="mailto:%1$s">%1$s</a>',
	'NO_ACCESS_ATTACHMENT'		=> 'You are not allowed to access this file.',
	'NO_ACTION'					=> 'No action specified.',
	'NO_ADMINISTRATORS'			=> 'No administrators assigned at this board.',
	'NO_AUTH_ADMIN'				=> 'Access to the Administration Control Panel is not allowed as you do not have administrative permissions.',
	'NO_AUTH_ADMIN_USER_DIFFER'	=> 'You are not able to re-authenticate as a different user.',
	'NO_AUTH_OPERATION'			=> 'You do not have the necessary permissions to complete this operation.',
	'NO_CONNECT_TO_SMTP_HOST'	=> 'Could not connect to smtp host : %1$s : %2$s',
	'NO_BIRTHDAYS'				=> 'No birthdays today',
	'NO_EMAIL_MESSAGE'			=> 'E-mail message was blank.',
	'NO_EMAIL_RESPONSE_CODE'	=> 'Could not get mail server response codes.',
	'NO_EMAIL_SUBJECT'			=> 'No e-mail subject specified.',
	'NO_FORUM'					=> 'The forum you selected does not exist.',
	'NO_FORUMS'					=> 'This board has no forums.',
	'NO_GROUP'					=> 'The requested usergroup does not exist.',
	'NO_GROUP_MEMBERS'			=> 'This group currently has no members.',
	'NO_IPS_DEFINED'			=> 'No IP addresses or hostnames defined',
	'NO_MEMBERS'				=> 'No members found for this search criterion.',
	'NO_MESSAGES'				=> 'No messages',
	'NO_MODE'					=> 'No mode specified.',
	'NO_MODERATORS'				=> 'No moderators assigned at this board.',
	'NO_NEW_MESSAGES'			=> 'No new messages',
	'NO_NEW_PM'					=> '<strong>0</strong> new messages',
	'NO_NEW_POSTS'				=> 'No new posts',
	'NO_ONLINE_USERS'			=> 'No registered users',
	'NO_POSTS'					=> 'No posts',
	'NO_POSTS_TIME_FRAME'		=> 'No posts exist inside this topic for the selected time frame.',
	'NO_SUBJECT'				=> 'No subject specified',								// Used for posts having no subject defined but displayed within management pages.
	'NO_SUCH_SEARCH_MODULE'		=> 'The specified search backend doesn’t exist.',
	'NO_SUPPORTED_AUTH_METHODS'	=> 'No supported authentication methods.',
	'NO_TOPIC'					=> 'The requested topic does not exist.',
	'NO_TOPIC_FORUM'			=> 'The topic or forum no longer exists.',
	'NO_TOPICS'					=> 'There are no topics or posts in this forum.',
	'NO_TOPICS_TIME_FRAME'		=> 'No topics exist inside this forum for the selected time frame.',
	'NO_UNREAD_PM'				=> '<strong>0</strong> unread messages',
	'NO_UPLOAD_FORM_FOUND'		=> 'Upload initiated but no valid file upload form found.',
	'NO_USER'					=> 'The requested user does not exist.',
	'NO_USERS'					=> 'The requested users do not exist.',
	'NO_USER_SPECIFIED'			=> 'No username was specified.',

	'OCCUPATION'				=> 'Occupation',
	'OFFLINE'					=> 'Offline',
	'ONLINE'					=> 'Online',
	'ONLINE_BUDDIES'			=> 'Online friends',
	'ONLINE_USERS_TOTAL'		=> 'In total there are <strong>%d</strong> users online :: ',
	'ONLINE_USERS_ZERO_TOTAL'	=> 'In total there are <strong>0</strong> users online :: ',
	'ONLINE_USER_TOTAL'			=> 'In total there is <strong>%d</strong> user online :: ',
	'OPTIONS'					=> 'Options',

	'PAGE_OF'				=> 'Page <strong>%1$d</strong> of <strong>%2$d</strong>',
	'PASSWORD'				=> 'Password',
	'PLAY_QUICKTIME_FILE'	=> 'Play Quicktime file',
	'PM'					=> 'PM',
	'POSTING_MESSAGE'		=> 'Posting message in %s',
	'POSTING_PRIVATE_MESSAGE'	=> 'Composing private message',
	'POST'					=> 'Post',
	'POST_ANNOUNCEMENT'		=> 'Announce',
	'POST_STICKY'			=> 'Sticky',
	'POSTED'				=> 'Posted',
	'POSTED_IN_FORUM'		=> 'in',
	'POSTED_ON_DATE'		=> 'on',
	'POSTS'					=> 'Posts',
	'POSTS_UNAPPROVED'		=> 'At least one post in this topic has not been approved.',
	'POST_BY_AUTHOR'		=> 'by',
	'POST_BY_FOE'			=> 'This post was made by <strong>%1$s</strong> who is currently on your ignore list. %2$sDisplay this post%3$s.',
	'POST_DAY'				=> '%.2f posts per day',
	'POST_DETAILS'			=> 'Post details',
	'POST_NEW_TOPIC'		=> 'Post new topic',
	'POST_PCT'				=> '%.2f%% of all posts',
	'POST_PCT_ACTIVE'		=> '%.2f%% of user’s posts',
	'POST_PCT_ACTIVE_OWN'	=> '%.2f%% of your posts',
	'POST_REPLY'			=> 'Post a reply',
	'POST_REPORTED'			=> 'Click to view report',
	'POST_SUBJECT'			=> 'Post subject',
	'POST_TIME'				=> 'Post time',
	'POST_TOPIC'			=> 'Post a new topic',
	'POST_UNAPPROVED'		=> 'This post is waiting for approval',
	'PREVIEW'				=> 'Preview',
	'PREVIOUS'				=> 'Previous',		// Used in pagination
	'PREVIOUS_STEP'			=> 'Previous',
	'PRIVACY'				=> 'Privacy policy',
	'PRIVATE_MESSAGE'		=> 'Private message',
	'PRIVATE_MESSAGES'		=> 'Private messages',
	'PRIVATE_MESSAGING'		=> 'Private messaging',
	'PROFILE'				=> 'User Control Panel',

	'READING_FORUM'				=> 'Viewing topics in %s',
	'READING_GLOBAL_ANNOUNCE'	=> 'Reading global announcement',
	'READING_LINK'				=> 'Following forum link %s',
	'READING_TOPIC'				=> 'Reading topic in %s',
	'READ_PROFILE'				=> 'Profile',
	'REASON'					=> 'Reason',
	'RECORD_ONLINE_USERS'		=> 'Most users ever online was <strong>%1$s</strong> on %2$s',
	'REDIRECT'					=> 'Redirect',
	'REDIRECTS'					=> 'Total redirects',
	'REGISTER'					=> 'Register',
	'REGISTERED_USERS'			=> 'Registered users:',
	'REG_USERS_ONLINE'			=> 'There are %d registered users and ',
	'REG_USERS_TOTAL'			=> '%d registered, ',
	'REG_USERS_ZERO_ONLINE'		=> 'There are 0 registered users and ',
	'REG_USERS_ZERO_TOTAL'		=> '0 registered, ',
	'REG_USER_ONLINE'			=> 'There is %d registered user and ',
	'REG_USER_TOTAL'			=> '%d registered, ',
	'REMOVE'					=> 'Remove',
	'REMOVE_INSTALL'			=> 'Please delete, move or rename the install directory before you use your board. If this directory is still present, only the Administration Control Panel (ACP) will be accessible.',
	'REPLIES'					=> 'Replies',
	'REPLY_WITH_QUOTE'			=> 'Reply with quote',
	'REPLYING_GLOBAL_ANNOUNCE'	=> 'Replying to global announcement',
	'REPLYING_MESSAGE'			=> 'Replying to message in %s',
	'REPORT_BY'					=> 'Report by',
	'REPORT_POST'				=> 'Report this post',
	'REPORTING_POST'			=> 'Reporting post',
	'RESEND_ACTIVATION'			=> 'Resend activation e-mail',
	'RESET'						=> 'Reset',
	'RESTORE_PERMISSIONS'		=> 'Restore permissions',
	'RETURN_INDEX'				=> '%sReturn to the index page%s',
	'RETURN_FORUM'				=> '%sReturn to the forum last visited%s',
	'RETURN_PAGE'				=> '%sReturn to the previous page%s',
	'RETURN_TOPIC'				=> '%sReturn to the topic last visited%s',
	'RETURN_TO'					=> 'Return to',
	'RULES_ATTACH_CAN'			=> 'You <strong>can</strong> post attachments in this forum',
	'RULES_ATTACH_CANNOT'		=> 'You <strong>cannot</strong> post attachments in this forum',
	'RULES_DELETE_CAN'			=> 'You <strong>can</strong> delete your posts in this forum',
	'RULES_DELETE_CANNOT'		=> 'You <strong>cannot</strong> delete your posts in this forum',
	'RULES_DOWNLOAD_CAN'		=> 'You <strong>can</strong> download attachments in this forum',
	'RULES_DOWNLOAD_CANNOT'		=> 'You <strong>cannot</strong> download attachments in this forum',
	'RULES_EDIT_CAN'			=> 'You <strong>can</strong> edit your posts in this forum',
	'RULES_EDIT_CANNOT'			=> 'You <strong>cannot</strong> edit your posts in this forum',
	'RULES_LOCK_CAN'			=> 'You <strong>can</strong> lock your topics in this forum',
	'RULES_LOCK_CANNOT'			=> 'You <strong>cannot</strong> lock your topics in this forum',
	'RULES_POST_CAN'			=> 'You <strong>can</strong> post new topics in this forum',
	'RULES_POST_CANNOT'			=> 'You <strong>cannot</strong> post new topics in this forum',
	'RULES_REPLY_CAN'			=> 'You <strong>can</strong> reply to topics in this forum',
	'RULES_REPLY_CANNOT'		=> 'You <strong>cannot</strong> reply to topics in this forum',
	'RULES_VOTE_CAN'			=> 'You <strong>can</strong> vote in polls in this forum',
	'RULES_VOTE_CANNOT'			=> 'You <strong>cannot</strong> vote in polls in this forum',

	'SEARCH'					=> 'Search',
	'SEARCH_MINI'				=> 'Search…',
	'SEARCH_ADV'				=> 'Advanced search',
	'SEARCH_ADV_EXPLAIN'		=> 'View the advanced search options',
	'SEARCH_KEYWORDS'			=> 'Search for keywords',
	'SEARCHING_FORUMS'			=> 'Searching forums',
	'SEARCH_ACTIVE_TOPICS'		=> 'View active topics',
	'SEARCH_FOR'				=> 'Search for',
	'SEARCH_FORUM'				=> 'Search this forum…',	
	'SEARCH_NEW'				=> 'View new posts',
	'SEARCH_POSTS_BY'			=> 'Search posts by',
	'SEARCH_SELF'				=> 'View your posts',
	'SEARCH_TOPIC'				=> 'Search this topic…',
	'SEARCH_UNANSWERED'			=> 'View unanswered posts',
	'SECONDS'					=> 'Seconds',
	'SELECT'					=> 'Select',
	'SELECT_ALL_CODE'			=> 'Select all',
	'SELECT_DESTINATION_FORUM'	=> 'Please select a destination forum',
	'SELECT_FORUM'				=> 'Select a forum',
	'SEND_EMAIL'				=> 'E-mail',
	'SEND_EMAIL_USER'			=> 'E-mail',				// Used as: {L_SEND_EMAIL_USER} {USERNAME} -> E-mail UserX
	'SEND_PRIVATE_MESSAGE'		=> 'Send private message',
	'SETTINGS'					=> 'Settings',
	'SIGNATURE'					=> 'Signature',
	'SKIP'						=> 'Skip to content',
	'SMTP_NO_AUTH_SUPPORT'		=> 'SMTP server does not support authentication.',
	'SORRY_AUTH_READ'			=> 'You are not authorised to read this forum.',
	'SORRY_AUTH_VIEW_ATTACH'	=> 'You are not authorised to download this attachment.',
	'SORT_BY'					=> 'Sort by',
	'SORT_JOINED'				=> 'Joined date',
	'SORT_LOCATION'				=> 'Location',
	'SORT_RANK'					=> 'Rank',
	'SORT_TOPIC_TITLE'			=> 'Topic title',
	'SORT_USERNAME'				=> 'Username',
	'SPLIT_TOPIC'				=> 'Split topic',
	'SQL_ERROR_OCCURRED'		=> 'An SQL error occurred while fetching this page. Please contact the %sBoard Administrator%s if this problem persists.',
	'STATISTICS'				=> 'Statistics',
	'START_WATCHING_FORUM'		=> 'Subscribe forum',
	'START_WATCHING_TOPIC'		=> 'Subscribe topic',
	'STOP_WATCHING_FORUM'		=> 'Unsubscribe forum',
	'STOP_WATCHING_TOPIC'		=> 'Unsubscribe topic',
	'SUBFORUM'					=> 'Subforum',
	'SUBFORUMS'					=> 'Subforums',
	'SUBJECT'					=> 'Subject',
	'SUBMIT'					=> 'Submit',

	'TERMS_USE'			=> 'Terms of use',
	'TEST_CONNECTION'	=> 'Test connection',
	'THE_TEAM'			=> 'The team',
	'TIME'				=> 'Time',

	'TOO_LONG_AIM'					=> 'The screenname you entered is too long.',
	'TOO_LONG_CONFIRM_CODE'			=> 'The confirm code you entered is too long.',
	'TOO_LONG_DATEFORMAT'			=> 'The date format you entered is too long.',
	'TOO_LONG_ICQ'					=> 'The ICQ number you entered is too long.',
	'TOO_LONG_INTERESTS'			=> 'The interests you entered is too long.',
	'TOO_LONG_JABBER'				=> 'The Jabber account name you entered is too long.',
	'TOO_LONG_LOCATION'				=> 'The location you entered is too long.',
	'TOO_LONG_MSN'					=> 'The MSNM/WLM name you entered is too long.',
	'TOO_LONG_NEW_PASSWORD'			=> 'The password you entered is too long.',
	'TOO_LONG_OCCUPATION'			=> 'The occupation you entered is too long.',
	'TOO_LONG_PASSWORD_CONFIRM'		=> 'The password confirmation you entered is too long.',
	'TOO_LONG_USER_PASSWORD'		=> 'The password you entered is too long.',
	'TOO_LONG_USERNAME'				=> 'The username you entered is too long.',
	'TOO_LONG_EMAIL'				=> 'The e-mail address you entered is too long.',
	'TOO_LONG_EMAIL_CONFIRM'		=> 'The e-mail address confirmation you entered is too long.',
	'TOO_LONG_WEBSITE'				=> 'The website address you entered is too long.',
	'TOO_LONG_YIM'					=> 'The Yahoo! Messenger name you entered is too long.',

	'TOO_MANY_VOTE_OPTIONS'			=> 'You have tried to vote for too many options.',

	'TOO_SHORT_AIM'					=> 'The screenname you entered is too short.',
	'TOO_SHORT_CONFIRM_CODE'		=> 'The confirm code you entered is too short.',
	'TOO_SHORT_DATEFORMAT'			=> 'The date format you entered is too short.',
	'TOO_SHORT_ICQ'					=> 'The ICQ number you entered is too short.',
	'TOO_SHORT_INTERESTS'			=> 'The interests you entered is too short.',
	'TOO_SHORT_JABBER'				=> 'The Jabber account name you entered is too short.',
	'TOO_SHORT_LOCATION'			=> 'The location you entered is too short.',
	'TOO_SHORT_MSN'					=> 'The MSNM/WLM name you entered is too short.',
	'TOO_SHORT_NEW_PASSWORD'		=> 'The password you entered is too short.',
	'TOO_SHORT_OCCUPATION'			=> 'The occupation you entered is too short.',
	'TOO_SHORT_PASSWORD_CONFIRM'	=> 'The password confirmation you entered is too short.',
	'TOO_SHORT_USER_PASSWORD'		=> 'The password you entered is too short.',
	'TOO_SHORT_USERNAME'			=> 'The username you entered is too short.',
	'TOO_SHORT_EMAIL'				=> 'The e-mail address you entered is too short.',
	'TOO_SHORT_EMAIL_CONFIRM'		=> 'The e-mail address confirmation you entered is too short.',
	'TOO_SHORT_WEBSITE'				=> 'The website address you entered is too short.',
	'TOO_SHORT_YIM'					=> 'The Yahoo! Messenger name you entered is too short.',

	'TOPIC'				=> 'Topic',
	'TOPICS'			=> 'Topics',
	'TOPIC_ICON'		=> 'Topic icon',
	'TOPIC_LOCKED'		=> 'This topic is locked, you cannot edit posts or make further replies.',
	'TOPIC_LOCKED_SHORT'=> 'Topic locked',
	'TOPIC_MOVED'		=> 'Moved topic',
	'TOPIC_REVIEW'		=> 'Topic review',
	'TOPIC_TITLE'		=> 'Topic title',
	'TOPIC_UNAPPROVED'	=> 'This topic has not been approved',
	'TOTAL_ATTACHMENTS'	=> 'Attachment(s)',
	'TOTAL_LOG'			=> '1 log',
	'TOTAL_LOGS'		=> '%d logs',
	'TOTAL_NO_PM'		=> '0 private messages in total',
	'TOTAL_PM'			=> '1 private message in total',
	'TOTAL_PMS'			=> '%d private messages in total',
	'TOTAL_POSTS'		=> 'Total posts',
	'TOTAL_POSTS_OTHER'	=> 'Total posts <strong>%d</strong>',
	'TOTAL_POSTS_ZERO'	=> 'Total posts <strong>0</strong>',
	'TOPIC_REPORTED'	=> 'This topic has been reported',
	'TOTAL_TOPICS_OTHER'=> 'Total topics <strong>%d</strong>',
	'TOTAL_TOPICS_ZERO'	=> 'Total topics <strong>0</strong>',
	'TOTAL_USERS_OTHER'	=> 'Total members <strong>%d</strong>',
	'TOTAL_USERS_ZERO'	=> 'Total members <strong>0</strong>',
	'TRACKED_PHP_ERROR'	=> 'Tracked PHP errors: %s',

	'UNABLE_GET_IMAGE_SIZE'	=> 'It was not possible to determine the dimensions of the image.',
	'UNABLE_TO_DELIVER_FILE'=> 'Unable to deliver file.',
	'UNKNOWN_BROWSER'		=> 'Unknown browser',
	'UNMARK_ALL'			=> 'Unmark all',
	'UNREAD_MESSAGES'		=> 'Unread messages',
	'UNREAD_PM'				=> '<strong>%d</strong> unread message',
	'UNREAD_PMS'			=> '<strong>%d</strong> unread messages',
	'UNWATCHED_FORUMS'			=> 'You are no longer subscribed to the selected forums.',
	'UNWATCHED_TOPICS'			=> 'You are no longer subscribed to the selected topics.',
	'UNWATCHED_FORUMS_TOPICS'	=> 'You are no longer subscribed to the selected entries.',
	'UPDATE'				=> 'Update',
	'UPLOAD_IN_PROGRESS'	=> 'The upload is currently in progress.',
	'URL_REDIRECT'			=> 'If your browser does not support meta redirection %splease click HERE to be redirected%s.',
	'USERGROUPS'			=> 'Groups',
	'USERNAME'				=> 'Username',
	'USERNAMES'				=> 'Usernames',
	'USER_AVATAR'			=> 'User avatar',
	'USER_CANNOT_READ'		=> 'You cannot read posts in this forum.',
	'USER_POST'				=> '%d Post',
	'USER_POSTS'			=> '%d Posts',
	'USERS'					=> 'Users',
	'USE_PERMISSIONS'		=> 'Test out user’s permissions',

	'VARIANT_DATE_SEPARATOR'	=> ' / ',	// Used in date format dropdown, eg: "Today, 13:37 / 01 Jan 2007, 13:37" ... to join a relative date with calendar date
	'VIEWED'					=> 'Viewed',
	'VIEWING_FAQ'				=> 'Viewing FAQ',
	'VIEWING_MEMBERS'			=> 'Viewing member details',
	'VIEWING_ONLINE'			=> 'Viewing who is online',
	'VIEWING_MCP'				=> 'Viewing moderator control panel',
	'VIEWING_MEMBER_PROFILE'	=> 'Viewing member profile',
	'VIEWING_PRIVATE_MESSAGES'	=> 'Viewing private messages',
	'VIEWING_REGISTER'			=> 'Registering account',
	'VIEWING_UCP'				=> 'Viewing user control panel',
	'VIEWS'						=> 'Views',
	'VIEW_BOOKMARKS'			=> 'View bookmarks',
	'VIEW_FORUM_LOGS'			=> 'View Logs',
	'VIEW_LATEST_POST'			=> 'View the latest post',
	'VIEW_NEWEST_POST'			=> 'View first unread post',
	'VIEW_NOTES'				=> 'View user notes',
	'VIEW_ONLINE_TIME'			=> 'based on users active over the past %d minute',
	'VIEW_ONLINE_TIMES'			=> 'based on users active over the past %d minutes',
	'VIEW_TOPIC'				=> 'View topic',
	'VIEW_TOPIC_ANNOUNCEMENT'	=> 'Announcement: ',
	'VIEW_TOPIC_GLOBAL'			=> 'Global Announcement: ',
	'VIEW_TOPIC_LOCKED'			=> 'Locked: ',
	'VIEW_TOPIC_LOGS'			=> 'View logs',
	'VIEW_TOPIC_MOVED'			=> 'Moved: ',
	'VIEW_TOPIC_POLL'			=> 'Poll: ',
	'VIEW_TOPIC_STICKY'			=> 'Sticky: ',
	'VISIT_WEBSITE'				=> 'Visit website',

	'WARNINGS'			=> 'Warnings',
	'WARN_USER'			=> 'Warn user',
	'WELCOME_SUBJECT'	=> 'Welcome to %s forums',
	'WEBSITE'			=> 'Website',
	'WHOIS'				=> 'Whois',
	'WHO_IS_ONLINE'		=> 'Who is online',
	'WRONG_PASSWORD'	=> 'You entered an incorrect password.',

	'WRONG_DATA_ICQ'			=> 'The number you entered is not a valid ICQ number.',
	'WRONG_DATA_JABBER'			=> 'The name you entered is not a valid Jabber account name.',
	'WRONG_DATA_LANG'			=> 'The language you specified is not valid.',
	'WRONG_DATA_WEBSITE'		=> 'The website address has to be a valid URL, including the protocol. For example http://www.example.com/.',
	'WROTE'						=> 'wrote',

	'YEAR'				=> 'Year',
	'YEAR_MONTH_DAY'	=> '(YYYY-MM-DD)',
	'YES'				=> 'Yes',
	'YIM'				=> 'YIM',	
	'YOU_LAST_VISIT'	=> 'Last visit was: %s',
	'YOU_NEW_PM'		=> 'A new private message is waiting for you in your Inbox.',
	'YOU_NEW_PMS'		=> 'New private messages are waiting for you in your Inbox.',
	'YOU_NO_NEW_PM'		=> 'No new private messages are waiting for you.',

	'datetime'			=> array(
		'TODAY'		=> 'Today',
		'TOMORROW'	=> 'Tomorrow',
		'YESTERDAY'	=> 'Yesterday',

		'Sunday'	=> 'Sunday',
		'Monday'	=> 'Monday',
		'Tuesday'	=> 'Tuesday',
		'Wednesday'	=> 'Wednesday',
		'Thursday'	=> 'Thursday',
		'Friday'	=> 'Friday',
		'Saturday'	=> 'Saturday',

		'Sun'		=> 'Sun',
		'Mon'		=> 'Mon',
		'Tue'		=> 'Tue',
		'Wed'		=> 'Wed',
		'Thu'		=> 'Thu',
		'Fri'		=> 'Fri',
		'Sat'		=> 'Sat',

		'January'	=> 'January',
		'February'	=> 'February',
		'March'		=> 'March',
		'April'		=> 'April',
		'May'		=> 'May',
		'June'		=> 'June',
		'July'		=> 'July',
		'August'	=> 'August',
		'September' => 'September',
		'October'	=> 'October',
		'November'	=> 'November',
		'December'	=> 'December',

		'Jan'		=> 'Jan',
		'Feb'		=> 'Feb',
		'Mar'		=> 'Mar',
		'Apr'		=> 'Apr',
		'May_short'	=> 'May',	// Short representation of "May". May_short used because in English the short and long date are the same for May.
		'Jun'		=> 'Jun',
		'Jul'		=> 'Jul',
		'Aug'		=> 'Aug',
		'Sep'		=> 'Sep',
		'Oct'		=> 'Oct',
		'Nov'		=> 'Nov',
		'Dec'		=> 'Dec',
	),

	'tz'				=> array(
		'-12'	=> 'UTC - 12 hours',
		'-11'	=> 'UTC - 11 hours',
		'-10'	=> 'UTC - 10 hours',
		'-9.5'	=> 'UTC - 9:30 hours',
		'-9'	=> 'UTC - 9 hours',
		'-8'	=> 'UTC - 8 hours',
		'-7'	=> 'UTC - 7 hours',
		'-6'	=> 'UTC - 6 hours',
		'-5'	=> 'UTC - 5 hours',
		'-4'	=> 'UTC - 4 hours',
		'-3.5'	=> 'UTC - 3:30 hours',
		'-3'	=> 'UTC - 3 hours',
		'-2'	=> 'UTC - 2 hours',
		'-1'	=> 'UTC - 1 hour',
		'0'		=> 'UTC',
		'1'		=> 'UTC + 1 hour',
		'2'		=> 'UTC + 2 hours',
		'3'		=> 'UTC + 3 hours',
		'3.5'	=> 'UTC + 3:30 hours',
		'4'		=> 'UTC + 4 hours',
		'4.5'	=> 'UTC + 4:30 hours',
		'5'		=> 'UTC + 5 hours',
		'5.5'	=> 'UTC + 5:30 hours',
		'5.75'	=> 'UTC + 5:45 hours',
		'6'		=> 'UTC + 6 hours',
		'6.5'	=> 'UTC + 6:30 hours',
		'7'		=> 'UTC + 7 hours',
		'8'		=> 'UTC + 8 hours',
		'8.75'	=> 'UTC + 8:45 hours',
		'9'		=> 'UTC + 9 hours',
		'9.5'	=> 'UTC + 9:30 hours',
		'10'	=> 'UTC + 10 hours',
		'10.5'	=> 'UTC + 10:30 hours',
		'11'	=> 'UTC + 11 hours',
		'11.5'	=> 'UTC + 11:30 hours',
		'12'	=> 'UTC + 12 hours',
		'12.75'	=> 'UTC + 12:45 hours',
		'13'	=> 'UTC + 13 hours',
		'14'	=> 'UTC + 14 hours',
		'dst'	=> '[ <abbr title="Daylight Saving Time">DST</abbr> ]',
	),

	'tz_zones'	=> array(
		'-12'	=> '[UTC - 12] Baker Island Time',
		'-11'	=> '[UTC - 11] Niue Time, Samoa Standard Time',
		'-10'	=> '[UTC - 10] Hawaii-Aleutian Standard Time, Cook Island Time',
		'-9.5'	=> '[UTC - 9:30] Marquesas Islands Time',
		'-9'	=> '[UTC - 9] Alaska Standard Time, Gambier Island Time',
		'-8'	=> '[UTC - 8] Pacific Standard Time',
		'-7'	=> '[UTC - 7] Mountain Standard Time',
		'-6'	=> '[UTC - 6] Central Standard Time',
		'-5'	=> '[UTC - 5] Eastern Standard Time',
		'-4'	=> '[UTC - 4] Atlantic Standard Time',
		'-3.5'	=> '[UTC - 3:30] Newfoundland Standard Time',
		'-3'	=> '[UTC - 3] Amazon Standard Time, Central Greenland Time',
		'-2'	=> '[UTC - 2] Fernando de Noronha Time, South Georgia &amp; the South Sandwich Islands Time',
		'-1'	=> '[UTC - 1] Azores Standard Time, Cape Verde Time, Eastern Greenland Time',
		'0'		=> '[UTC] Western European Time, Greenwich Mean Time',
		'1'		=> '[UTC + 1] Central European Time, West African Time',
		'2'		=> '[UTC + 2] Eastern European Time, Central African Time',
		'3'		=> '[UTC + 3] Moscow Standard Time, Eastern African Time',
		'3.5'	=> '[UTC + 3:30] Iran Standard Time',
		'4'		=> '[UTC + 4] Gulf Standard Time, Samara Standard Time',
		'4.5'	=> '[UTC + 4:30] Afghanistan Time',
		'5'		=> '[UTC + 5] Pakistan Standard Time, Yekaterinburg Standard Time',
		'5.5'	=> '[UTC + 5:30] Indian Standard Time, Sri Lanka Time',
		'5.75'	=> '[UTC + 5:45] Nepal Time',
		'6'		=> '[UTC + 6] Bangladesh Time, Bhutan Time, Novosibirsk Standard Time',
		'6.5'	=> '[UTC + 6:30] Cocos Islands Time, Myanmar Time',
		'7'		=> '[UTC + 7] Indochina Time, Krasnoyarsk Standard Time',
		'8'		=> '[UTC + 8] Chinese Standard Time, Australian Western Standard Time, Irkutsk Standard Time',
		'8.75'	=> '[UTC + 8:45] Southeastern Western Australia Standard Time',
		'9'		=> '[UTC + 9] Japan Standard Time, Korea Standard Time, Chita Standard Time',
		'9.5'	=> '[UTC + 9:30] Australian Central Standard Time',
		'10'	=> '[UTC + 10] Australian Eastern Standard Time, Vladivostok Standard Time',
		'10.5'	=> '[UTC + 10:30] Lord Howe Standard Time',
		'11'	=> '[UTC + 11] Solomon Island Time, Magadan Standard Time',
		'11.5'	=> '[UTC + 11:30] Norfolk Island Time',
		'12'	=> '[UTC + 12] New Zealand Time, Fiji Time, Kamchatka Standard Time',
		'12.75'	=> '[UTC + 12:45] Chatham Islands Time',
		'13'	=> '[UTC + 13] Tonga Time, Phoenix Islands Time',
		'14'	=> '[UTC + 14] Line Island Time',
	),

	// The value is only an example and will get replaced by the current time on view
	'dateformats'	=> array(
		'd M Y, H:i'			=> '01 Jan 2007, 13:37',
		'd M Y H:i'				=> '01 Jan 2007 13:37',
		'M jS, \'y, H:i'		=> 'Jan 1st, \'07, 13:37',
		'D M d, Y g:i a'		=> 'Mon Jan 01, 2007 1:37 pm',
		'F jS, Y, g:i a'		=> 'January 1st, 2007, 1:37 pm',
		'|d M Y|, H:i'			=> 'Today, 13:37 / 01 Jan 2007, 13:37',
		'|F jS, Y|, g:i a'		=> 'Today, 1:37 pm / January 1st, 2007, 1:37 pm'
	),

	// The default dateformat which will be used on new installs in this language
	// Translators should change this if a the usual date format is different
	'default_dateformat'	=> 'D M d, Y g:i a', // Mon Jan 01, 2007 1:37 pm

));

?>