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

/**
 * Responsibility: configurable protected properties with getters and setters,
 *				   internal protected properties and internal methods used 
 *				   in most extended routers implementations bellow.
 * Trait for classes:
 * - `\MvcCore\Ext\Routers\Media`
 * - `\MvcCore\Ext\Routers\Localization`
 * - `\MvcCore\Ext\Routers\MediaAndLocalization`
 */
interface IExtended
{
	/**
	 * Get `TRUE` to route site version only for `GET` requests. 
	 * `FALSE` to process advanced routing on all requests.
	 * @return bool
	 */
	public function GetRouteGetRequestsOnly ();

	/**
	 * Set `TRUE` to route site version only for `GET` requests. 
	 * `FALSE` to process advanced routing on all requests.
	 * @param bool $routeGetRequestsOnly 
	 * @return \MvcCore\IRouter|\MvcCore\Ext\Routers\IExtended
	 */
	public function & SetRouteGetRequestsOnly ($routeGetRequestsOnly = TRUE);

	/**
	 * Get `TRUE` (default is `FALSE`) to prevent user to be able to switch site version 
	 * only by requesting different url with different site version prefix. If he does it
	 * and this configuration is `TRUE`, he is redirected back to his remembered site 
	 * version by session. 
	 * But if you really want to switch site version for your users, you need to add into 
	 * url special param to switch the version. But if you are creating url in controller 
	 * or in template, it's added automatically, when you put into second argument `$params` 
	 * key with different site version:
	 * `$this->Url('self', ['media_version' => 'mobile', 'lang'	=> 'de']);`.
	 * @return bool
	 */
	public function GetStricModeBySession ();

	/**
	 * Set  `TRUE` (default is `FALSE`) to prevent user to be able to switch site version 
	 * only by requesting different url with different site version prefix. If he does it
	 * and this configuration is `TRUE`, he is redirected back to his remembered site 
	 * version by session. 
	 * But if you really want to switch site version for your users, you need to add into 
	 * url special param to switch the version. But if you are creating url in controller 
	 * or in template, it's added automatically, when you put into second argument `$params` 
	 * key with different site version:
	 * `$this->Url('self', ['media_version' => 'mobile', 'lang'	=> 'de']);`.
	 * @param bool $stricModeBySession
	 * @return \MvcCore\IRouter|\MvcCore\Ext\Routers\IExtended
	 */
	public function & SetStricModeBySession ($stricModeBySession = TRUE);

	/**
	 * Get session expiration in seconds to remember previously detected site version by user
	 * agent or headers from previous requests. To not recognize site version by user agent 
	 * or headers every time, because it's time consuming. Default value is `0` - "until the  
	 * browser is closed". Session record is always used to compare, if user is requesting the  
	 * same or different site version. If request by url is into the same site version, 
	 * session record expiration is enlarged by this value. If request by url is into 
	 * current session place, session different site version, then new different 
	 * site version is stored in expiration is enlarged by this value and user is 
	 * redirected to different place. But if router is configured into session strict mode, 
	 * than to redirect user into new site version, there is necessary to add special 
	 * url switch param (always automatically added by `Url()` method). Because without it, 
	 * user is redirected strictly back into the same version.
	 * @return int
	 */
	public function GetSessionExpirationSeconds ();

	/**
	 * Set session expiration in seconds to remember previously detected site version by user
	 * agent or headers from previous requests. To not recognize site version by user agent 
	 * or headers every time, because it's time consuming. Default value is `0` - "until the  
	 * browser is closed". Session record is always used to compare, if user is requesting the  
	 * same or different site version. If request by url is into the same site version, 
	 * session record expiration is enlarged by this value. If request by url is into 
	 * current session place, session different site version, then new different 
	 * site version is stored in expiration is enlarged by this value and user is 
	 * redirected to different place. But if router is configured into session strict mode, 
	 * than to redirect user into new site version, there is necessary to add special 
	 * url switch param (always automatically added by `Url()` method). Because without it, 
	 * user is redirected strictly back into the same version.
	 * @param int $sessionExpirationSeconds
	 * @return \MvcCore\IRouter|\MvcCore\Ext\Routers\IExtended
	 */
	public function & SetSessionExpirationSeconds ($sessionExpirationSeconds = 0);
}
