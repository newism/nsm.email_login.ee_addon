<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * NSM Email Login Extension
 *
 * @package			NsmEmailLogin
 * @version			1.0.1
 * @author			Leevi Graham <http://leevigraham.com>
 * @copyright 		Copyright (c) 2007-2010 Newism <http://newism.com.au>
 * @license 		Commercial - please see LICENSE file included with this distribution
 * @link			http://ee-garage.com/nsm-email-login
 * @see 			http://expressionengine.com/user_guide/development/
 */

class Nsm_email_login_ext
{
	public $version			= '1.0.1';
	public $name			= 'NSM Email Login';
	public $description		= 'Allow members to login with their email or username.';
	public $docs_url		= 'http://ee-garage.com/nsm-email-login';
	public $settings_exist	= FALSE;

	// At leaset one hook is needed to install an extension
	// In some cases you may want settings but not actually use any hooks
	// In those cases we just use a dummy hook
	public $hooks = array('member_member_login_start');

	// ====================================
	// = Delegate & Constructor Functions =
	// ====================================

	/**
	 * PHP5 constructor function.
	 *
	 * @access public
	 * @return void
	 **/
	function __construct()
	{}






	// ===============================
	// = Hook Functions =
	// ===============================

	/**
	 * Try and match the username to a members email.
	 *
	 * @access public
	 * @return void
	 * @see http://expressionengine.com/user_guide/development/extension_hooks/module/member_auth/index.html#member_member_login_start
	 */
	public function member_member_login_start()
	{
		$EE = get_instance();
		// Get the username from the post array
		$username = $EE->input->post('username');
		// Does it have an @ symbol making it an email
		if(strpos($username,"@") !== false && $username)
		{
			// Select the username from the members table trying to match it to email
			$query = $EE->db->select('username')
				->where('email', $username)
				->get('members', 1);

			// result? set the post.
			if($query->num_rows())
				$_POST['username'] = $query->row()->username;
		}
	}




	// ===============================
	// = Class and Private Functions =
	// ===============================

	/**
	 * Called by ExpressionEngine when the user activates the extension.
	 *
	 * @access		public
	 * @return		void
	 **/
	public function activate_extension()
	{
		$this->_registerHooks();
	}

	/**
	 * Called by ExpressionEngine when the user disables the extension.
	 *
	 * @access		public
	 * @return		void
	 **/
	public function disable_extension()
	{
		$this->_unregisterHooks();
	}

	/**
	 * Called by ExpressionEngine updates the extension
	 *
	 * @access public
	 * @return void
	 **/
	public function update_extension($current=FALSE){}
	




	// ======================
	// = Hook Functions     =
	// ======================

	/**
	 * Sets up and subscribes to the hooks specified by the $hooks array.
	 *
	 * @access private
	 * @param array $hooks A flat array containing the names of any hooks that this extension subscribes to. By default, this parameter is set to FALSE.
	 * @return void
	 * @see http://expressionengine.com/public_beta/docs/development/extension_hooks/index.html
	 **/
	private function _registerHooks($hooks = FALSE)
	{
		$EE =& get_instance();

		if($hooks == FALSE && isset($this->hooks) == FALSE)
			return;

		if (!$hooks)
			$hooks = $this->hooks;

		$hook_template = array(
			'class'    => __CLASS__,
			'settings' => "a:0:{}",
			'version'  => $this->version,
		);

		foreach ($hooks as $key => $hook)
		{
			if (is_array($hook))
			{
				$data['hook'] = $key;
				$data['method'] = (isset($hook['method']) === TRUE) ? $hook['method'] : $key;
				$data = array_merge($data, $hook);
			}
			else
			{
				$data['hook'] = $data['method'] = $hook;
			}

			$hook = array_merge($hook_template, $data);
			$EE->db->insert('exp_extensions', $hook);
		}
	}

	/**
	 * Removes all subscribed hooks for the current extension.
	 * 
	 * @access private
	 * @return void
	 * @see http://expressionengine.com/public_beta/docs/development/extension_hooks/index.html
	 **/
	private function _unregisterHooks()
	{
		$EE =& get_instance();
		$EE->db->where('class', __CLASS__);
		$EE->db->delete('extensions'); 
	}
}