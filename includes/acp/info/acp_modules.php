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
* @package module_install
*/
class acp_modules_info
{
	function module()
	{
		return array(
			'filename'	=> 'acp_modules',
			'title'		=> 'ACP_MODULE_MANAGEMENT',
			'version'	=> '1.0.0',
			'modes'		=> array(
				'acp'		=> array('title' => 'ACP', 'auth' => 'acl_a_modules'),
				'ucp'		=> array('title' => 'UCP', 'auth' => 'acl_a_modules'),
				'mcp'		=> array('title' => 'MCP', 'auth' => 'acl_a_modules'),
			),
		);
	}

	function install()
	{
	}

	function uninstall()
	{
	}
}

?>