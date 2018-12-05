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

namespace MvcCore\Ext\Routers\Extendeds;

/**
 * Responsibility: configurable protected properties with getters and setters,
 *				   internal protected properties and internal methods used 
 *				   in most extended router implementations bellow.
 * Trait for classes:
 * - `\MvcCore\Ext\Routers\Media`
 * - `\MvcCore\Ext\Routers\Localization`
 * - `\MvcCore\Ext\Routers\MediaAndLocalization`
 */
trait Preparing
{
	/**
	 * Prepare extended router media site version or localization processing.
	 * @return void
	 */
	protected function prepare () {
		$request = & $this->request;

		// store original path value for later use
		$request->SetOriginalPath($request->GetPath(TRUE));

		// switching media site version and targeting localized version 
		// will be only by GET method, other methods are not very useful 
		// to localize or target for media site version. It adds only another
		// not much useful route records to route processing:
		$this->isGet = $request->GetMethod() == \MvcCore\IRequest::METHOD_GET;
		
		// look into request params if there is any switching 
		// parameter for session strict mode
		$this->requestGlobalGet = array_merge([], $request->GetGlobalCollection('get')); // clone `$_GET`
		
		// Set up session object to look inside for something from previous requests. 
		// This command starts the session if not started yet.
		$this->setUpSession();

		// Try to recognize administration request by `admin` param in query string
		// and by any authenticated user. The boolean flag `$this->adminRequest`
		// is used only to not process strict session mode redirections and to serve
		// requested documents directly, so there is not necessary to check if user
		// has any privileges or not, because this is only router.
		if (isset($this->requestGlobalGet[static::$adminRequestQueryParamName])) {
			$authClass = static::$baseAuthClass;
			if (class_exists($authClass)) {
				$user = $authClass::GetInstance()->GetUser();
				if ($user !== NULL) 
					$this->adminRequest = TRUE;
			}
		}
	}

	/**
	 * If session namespace by this class is not initialized,
	 * initialize session namespace under this class name and 
	 * move expiration to configured value.
	 * @return void
	 */
	protected function setUpSession () {
		if ($this->session === NULL) {
			$sessionClass = $this->application->GetSessionClass();
			$this->session = $sessionClass::GetNamespace(__CLASS__);
			$this->session->SetExpirationSeconds($this->sessionExpirationSeconds);
		}
	}
}
