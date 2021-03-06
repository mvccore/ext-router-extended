<?php

/**
 * MvcCore
 *
 * This source file is subject to the BSD 3 License
 * For the full copyright and license information, please view
 * the LICENSE.md file that are distributed with this source code.
 *
 * @copyright	Copyright (c) 2016 Tom Flidr (https://github.com/mvccore)
 * @license		https://mvccore.github.io/docs/mvccore/5.0.0/LICENSE.md
 */

namespace MvcCore\Ext\Routers\Extended;

/**
 * Responsibility: configurable protected properties with getters and setters,
 *				   internal protected properties and internal methods used 
 *				   in most extended router implementations bellow.
 * Trait for classes:
 * - `\MvcCore\Ext\Routers\Media`
 * - `\MvcCore\Ext\Routers\Localization`
 * - `\MvcCore\Ext\Routers\MediaAndLocalization`
 * @mixin \MvcCore\Ext\Routers\Extended
 */
trait Redirect {

	/**
	 * Redirect to target media site version and localization version with path and query string.
	 * @param array $systemParams 
	 * @return array `string $urlBaseSection, string $urlPathWithQuerySection, array $systemParams, bool|NULL $urlPathWithQueryIsHome`
	 */
	protected abstract function redirectToVersionSections ($systemParams);

	/**
	 * Redirect to target media site version with path and by cloned request 
	 * object global `$_GET` collection. Return always `FALSE`.
	 * @param array $systemParams 
	 * @return bool
	 */
	protected function redirectToVersion ($systemParams) {
		// get domain with base path url section, 
		// path with query string url section 
		// system params for url prefixes
		// and if path with query string url section targeting `/` or `/index.php`
		list ($urlBaseSection, $urlPathWithQuerySection, $systemParams, $urlPathWithQueryIsHome) 
			= $this->redirectToVersionSections($systemParams);
		
		$targetUrl = $this->urlByRoutePrefixSystemParams(
			$urlBaseSection, $urlPathWithQuerySection, $systemParams, $urlPathWithQueryIsHome
		);

		$questionMarkPos = mb_strpos($targetUrl, '?');
		if ($questionMarkPos !== FALSE) $targetUrl = mb_substr($targetUrl, 0, $questionMarkPos);
		
		/** @var \MvcCore\Request $request */
		$request = $this->request;
		$fullOriginalUrl = $request->GetBaseUrl() . $request->GetOriginalPath() . $request->GetQuery(TRUE);
		
		if ($fullOriginalUrl === $targetUrl) return TRUE;
		
		$this->redirect($targetUrl, $this->redirectStatusCode, 'Extended router redirect');

		return FALSE;
	}
}
