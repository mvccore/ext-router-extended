<?php

/**
 * MvcCore
 *
 * This source file is subject to the BSD 3 License
 * For the full copyright and license information, please view
 * the LICENSE.md file that are distributed with this source code.
 *
 * @copyright	Copyright (c) 2016 Tom Flídr (https://github.com/mvccore/mvccore)
 * @license		https://mvccore.github.io/docs/mvccore/4.0.0/LICENCE.md
 */

namespace MvcCore\Ext\Routers;

trait Extended
{
	/**
	 * `TRUE` (default is `FALSE`) to prevent user to be able to switch site version 
	 * only by requesting different url with different site version prefix. If he does it
	 * and this configuration is `TRUE`, he is redirected back to his remembered site 
	 * version by session. 
	 * But if you realy want to switch site version for your users, you need to add into 
	 * url special param to switch the version. But if you are creating url in controller 
	 * or in template, it's added automaticly, when you put into second argument `$params` 
	 * key with different site version:
	 * `$this->Url('self', ['media_version' => 'mobile', 'lang'	=> 'de']);`.
	 * @var bool
	 */
	protected $stricModeBySession = FALSE;

	/**
	 * Session expiration in seconds to remember previously detected site version by user
	 * agent or headers from previous requests. To not recognize site version by user agent 
	 * or headers everytime, because it's time consuming. Default value is `0` - "until the  
	 * browser is closed". Session record is always used to compare, if user is requesting the  
	 * same or different site version. If request by url is into the same site version, 
	 * session record expiration is enlarged by this value. If request by url is into 
	 * current session place, session different site version, then new different 
	 * site version is stored in expiration is enlarged by this value and user is 
	 * redirected to different place. But if router is configured into session strict mode, 
	 * than to redirect user into new site version, there is necesary to add special 
	 * url switch param (always automaticly added by `Url()` method). Because without it, 
	 * user is redirected strictly back into the same version.
	 * @var int
	 */
	protected $sessionExpirationSeconds = 0;

	
	/*************************************************************************************
	 *                                Internal Properties                                *
	 ************************************************************************************/

	/**
	 * `TRUE` if request is GET, `FALSE` otherwise.
	 * @var bool|NULL
	 */
	protected $isGet = NULL;

	/**
	 * Reference to global `$_GET` array from request object.
	 * @var array
	 */
	protected $requestGlobalGet = [];

	/**
	 * Session namespace to store previously recognized site version by user agent
	 * or by http headers to not do this every time, because it's time consuming.
	 * @var \MvcCore\Session|\MvcCore\Interfaces\ISession|NULL
	 */
	protected $session = NULL;

	/**
	 * Reference to `\MvcCore\Application::GetInstance();` 
	 * to not call this very time we need app instance.
	 * @var \MvcCore\Application|\MvcCore\Interfaces\IApplication|NULL
	 */
	protected static $application = NULL;

	
	/*************************************************************************************
	 *                                  Public Methods                                   *
	 ************************************************************************************/

	/**
	 * Set  `TRUE` (default is `FALSE`) to prevent user to be able to switch site version 
	 * only by requesting different url with different site version prefix. If he does it
	 * and this configuration is `TRUE`, he is redirected back to his remembered site 
	 * version by session. 
	 * But if you realy want to switch site version for your users, you need to add into 
	 * url special param to switch the version. But if you are creating url in controller 
	 * or in template, it's added automaticly, when you put into second argument `$params` 
	 * key with different site version:
	 * `$this->Url('self', ['media_version' => 'mobile', 'lang'	=> 'de']);`.
	 * @param bool $stricModeBySession
	 * @return \MvcCore\Router|\MvcCore\Interfaces\IRouter|\MvcCore\Ext\Routers\IExtended
	 */
	public function SetStricModeBySession ($stricModeBySession = TRUE) {
		$this->stricModeBySession = $stricModeBySession;
		return $this;
	}

	/**
	 * Set session expiration in seconds to remember previously detected site version by user
	 * agent or headers from previous requests. To not recognize site version by user agent 
	 * or headers everytime, because it's time consuming. Default value is `0` - "until the  
	 * browser is closed". Session record is always used to compare, if user is requesting the  
	 * same or different site version. If request by url is into the same site version, 
	 * session record expiration is enlarged by this value. If request by url is into 
	 * current session place, session different site version, then new different 
	 * site version is stored in expiration is enlarged by this value and user is 
	 * redirected to different place. But if router is configured into session strict mode, 
	 * than to redirect user into new site version, there is necesary to add special 
	 * url switch param (always automaticly added by `Url()` method). Because without it, 
	 * user is redirected strictly back into the same version.
	 * @param int $sessionExpirationSeconds
	 * @return \MvcCore\Router|\MvcCore\Interfaces\IRouter|\MvcCore\Ext\Routers\IExtended
	 */
	public function & SetSessionExpirationSeconds ($sessionExpirationSeconds = 0) {
		$this->sessionExpirationSeconds = $sessionExpirationSeconds;
		return $this;
	}

	
	/*************************************************************************************
	 *                                 Protected Methods                                 *
	 ************************************************************************************/

	/**
	 * If session namespace by this class is not initialized,
	 * initialize session namespace under this class name and 
	 * move expiration to configured value.
	 * @return void
	 */
	protected function setUpSession () {
		if ($this->session === NULL) {
			$sessionClass = static::$application->GetSessionClass();
			$this->session = $sessionClass::GetNamespace(__CLASS__);
			$this->session->SetExpirationSeconds($this->sessionExpirationSeconds);
		}
	}

	/**
	 * If local request object global collection `$_GET` contains any items
	 * and if controller and action in collection have the same values as 
	 * default controller and action values, unset them from request global 
	 * `$_GET` collection.
	 * @return void
	 */
	protected function removeDefaultCtrlActionFromGlobalGet () {
		if ($this->requestGlobalGet) {
			$app = \MvcCore\Application::GetInstance();
			$toolClass = $app->GetToolClass();
			list($dfltCtrlPc, $dftlActionPc) = $app->GetDefaultControllerAndActionNames();
			$dfltCtrlDc = $toolClass::GetDashedFromPascalCase($dfltCtrlPc);
			$dftlActionDc = $toolClass::GetDashedFromPascalCase($dftlActionPc);
			if (isset($this->requestGlobalGet['controller']) && isset($this->requestGlobalGet['action']))
				if ($this->requestGlobalGet['controller'] == $dfltCtrlDc && $this->requestGlobalGet['action'] == $dftlActionDc)
					unset($this->requestGlobalGet['controller'], $this->requestGlobalGet['action']);
		}
	}
}
