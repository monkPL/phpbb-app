<?php
/**
*
* This file is part of the phpBB Forum Software package.
*
* @copyright (c) phpBB Limited <https://www.phpbb.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
* For full copyright and license information, please see
* the docs/CREDITS.txt file.
*
*/

namespace phpbb\controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RequestContext;

/**
* Controller helper class, contains methods that do things for controllers
*/
class helper
{
	/**
	* Template object
	* @var \phpbb\template\template
	*/
	protected $template;

	/**
	* User object
	* @var \phpbb\user
	*/
	protected $user;

	/**
	* config object
	* @var \phpbb\config\config
	*/
	protected $config;

	/* @var \phpbb\symfony_request */
	protected $symfony_request;

	/**
	* phpBB root path
	* @var string
	*/
	protected $phpbb_root_path;

	/**
	* PHP file extension
	* @var string
	*/
	protected $php_ext;

	/**
	* Constructor
	*
	* @param \phpbb\template\template $template Template object
	* @param \phpbb\user $user User object
	* @param \phpbb\config\config $config Config object
	* @param \phpbb\controller\provider $provider Path provider
	* @param \phpbb\extension\manager $manager Extension manager object
	* @param \phpbb\symfony_request $symfony_request Symfony Request object
	* @param string $phpbb_root_path phpBB root path
	* @param string $php_ext PHP file extension
	*/
	public function __construct(\phpbb\template\template $template, \phpbb\user $user, \phpbb\config\config $config, \phpbb\controller\provider $provider, \phpbb\extension\manager $manager, \phpbb\symfony_request $symfony_request, $phpbb_root_path, $php_ext)
	{
		$this->template = $template;
		$this->user = $user;
		$this->config = $config;
		$this->symfony_request = $symfony_request;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;
		$provider->find_routing_files($manager->get_finder());
		$this->route_collection = $provider->find($phpbb_root_path)->get_routes();
	}

	/**
	* Automate setting up the page and creating the response object.
	*
	* @param string $template_file The template handle to render
	* @param string $page_title The title of the page to output
	* @param int $status_code The status code to be sent to the page header
	* @param bool $display_online_list Do we display online users list
	*
	* @return Response object containing rendered page
	*/
	public function render($template_file, $page_title = '', $status_code = 200, $display_online_list = false)
	{
		page_header($page_title, $display_online_list);

		$this->template->set_filenames(array(
			'body'	=> $template_file,
		));

		page_footer(true, false, false);

		return new Response($this->template->assign_display('body'), $status_code);
	}

	/**
	* Generate a URL to a route
	*
	* @param string	$route		Name of the route to travel
	* @param array	$params		String or array of additional url parameters
	* @param bool	$is_amp		Is url using &amp; (true) or & (false)
	* @param string|bool	$session_id	Possibility to use a custom session id instead of the global one
	* @return string The URL already passed through append_sid()
	*/
	public function route($route, array $params = array(), $is_amp = true, $session_id = false)
	{
		$anchor = '';
		if (isset($params['#']))
		{
			$anchor = '#' . $params['#'];
			unset($params['#']);
		}

		$context = new RequestContext();
		$context->fromRequest($this->symfony_request);

		$script_name = $this->symfony_request->getScriptName();
		$page_name = substr($script_name, -1, 1) == '/' ? '' : basename($script_name);
		$context->setBaseUrl(str_replace('/' . $page_name, empty($this->config['enable_mod_rewrite']) ? '/app.' . $this->php_ext : '', $context->getBaseUrl()));

		$url_generator = new UrlGenerator($this->route_collection, $context);
		$route_url = $url_generator->generate($route, $params);

		if ($is_amp)
		{
			$route_url = str_replace(array('&amp;', '&'), array('&', '&amp;'), $route_url);
		}

		return append_sid($route_url . $anchor, false, $is_amp, $session_id, true);
	}

	/**
	* Output an error, effectively the same thing as trigger_error
	*
	* @param string $message The error message
	* @param int $code The error code (e.g. 404, 500, 503, etc.)
	* @return Response A Response instance
	*/
	public function error($message, $code = 500)
	{
		$this->template->assign_vars(array(
			'MESSAGE_TEXT'	=> $message,
			'MESSAGE_TITLE'	=> $this->user->lang('INFORMATION'),
		));

		return $this->render('message_body.html', $this->user->lang('INFORMATION'), $code);
	}

	/**
	* Return the current url
	*
	* @return string
	*/
	public function get_current_url()
	{
		return generate_board_url(true) . $this->symfony_request->getRequestUri();
	}
}
