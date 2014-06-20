<?php
/**
 * @file cc_piwik.php
 * @brief Main CC Piwik file, to include in your code.
 * @author Romain Ducher <r.ducher@agence-codecouleurs.fr>
 */

namespace cc_piwik;

/** Front class, to use in your code */
class CC_Piwik {
	####################
	# Class' internals #
	####################
	
	/** Piwik base URL */
	protected $piwik_url;
	
	/** Piwik authentication token */
	protected $token_auth;
	
	/** Format of the datas to return */
	protected $format;
	
	/** Name for the 'module' argument in URLs. */
	protected static $module = 'API';
	
	/**
	 * Constructor
	 * @param string $piwik_url Piwik base URL
	 * @param string $token_auth Piwik authentication token
	 */
    public function __construct($piwik_url, $token_auth = '', $format = 'JSON') {
		$this->set_piwik_url($piwik_url);
		$this->set_token_auth($token_auth);
		$this->set_format($format);
	}
	
	/**
	 * Executing a Piwik API request.
	 *
	 * The method :
	 * 1°) Parses the calling function name to retrieve the 'method' argument.
	 * 2°) Build the Piwik URL to ask.
	 * 3°) Returns the body of the Piwik response.
	 * @param string $function Calling CC_Piwik method's __FUNCTION__.
	 * @param array $args Calling CC_Piwik method's arguments.
	 * @return string Raw content of Piwik's response body.
	 */
	protected function ask_piwik($function, array $args) {
		return file_get_contents($this->build_endpoint_url(static::$module, $this->get_method($function), $args));
	}
	
	/**
	 * Parsing the calling function name to retrieve the 'method' argument.
	 * @param string $function  calling CC_Piwik method's __FUNCTION__.
	 * @return string "Class.Action"
	 */
	protected function get_method($function) {
		return str_replace('_', '.', $function);
	}
	
	/**
	 * Building the Piwik URL to ask.
	 * @param string $module 'module' Piwik API's argument
	 * @param string $method 'method' Piwik API's argument
	 * @param array $args Other endpoint arguments
	 * @return string The URL to use for asking Piwik some datas.
	 */
	protected function build_endpoint_url($module, $method, array $other_args) {
		$get_args = array_merge(
			$other_args,
			array(
				'module'     => $module,
				'method'     => $method,
				'format'     => $this->format,
				'token_auth' => $this->token_auth,
			)
		);
		
		$query_args = array();
		foreach ($get_args as $name => $value) {
			$query_args[] = $name.'='.urlencode($value);
		}
		
		return $this->piwik_url.'?'.implode('&', $query_args);
	}
	
	#######################
	# Getters and setters #
	#######################
	
	// piwik_url
	
	/**
	 * Getter for $this->piwik_url.
	 * @return $this->piwik_url
	 */
	public function get_piwik_url() {
		return $this->piwik_url;
	}
	
	/*
	 * Setter for $this->piwik_url.
	 * @param string $new_value New value for $this->piwik_url.
	 */
	public function set_piwik_url($new_value) {
		$this->piwik_url = $new_value;
	}
	
	// token_auth
	
	/**
	 * Getter for $this->token_auth.
	 * @return $this->token_auth
	 */
	public function get_token_auth() {
		return $this->token_auth;
	}
	
	/*
	 * Setter for $this->token_auth.
	 * @param string $new_value New value for $this->.token_auth
	 */
	public function set_token_auth($new_value) {
		$this->token_auth = $new_value;
	}
	
	/**
	 * Setting the token_auth by asking it to the Piwik API.
	 * @param string $userLogin User login
	 * @param string $password User's password clear or encrypted with md5.
	 * @param bool $password_is_clear true if $password is clear,
	 * false if it is encrypted with md5.
	 */
	public function set_token_auth_from_credentials($userLogin, $password, $password_is_clear = true) {
		$cc_piwik = clone $this;
		$cc_piwik->set_format('JSON');
		$piwik_res = json_decode($cc_piwik->usersManager_getTokenAuth($userLogin, $password_is_clear ? md5($password) : $password));
		if (isset($piwik_res['value'])) {
			$this->set_token_auth($piwik_res['value']);
		}
	}
	
	// format
	
	/**
	 * Getter for $this->format.
	 * @return $this->format
	 */
	public function get_format() {
		return $this->format;
	}
	
	/*
	 * Setter for $this->format.
	 * @param string $new_value New value for $this->format.
	 */
	public function set_format($new_value) {
		$this->format = $new_value;
	}
	
	#######################
	# Module UsersManager #
	#######################
	
	public function UsersManager_addUser($userLogin, $password, $email, $alias = '') {
		return $this->ask_piwik(__FUNCTION__, func_get_args());
	}
	
	public function UsersManager_getUser($userLogin) {
		return $this->ask_piwik(__FUNCTION__, func_get_args());
	}
	
	public function UsersManager_updateUser($userLogin, $password = '', $email = '', $alias = '') {
		return $this->ask_piwik(__FUNCTION__, func_get_args());
	}
	
	public function UsersManager_deleteUser($userLogin) {
		return $this->ask_piwik(__FUNCTION__, func_get_args());
	}
	
	public function UsersManager_getTokenAuth($userLogin, $md5Password) {
		return $this->ask_piwik(__FUNCTION__, func_get_args());
	}
}
