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
trait Url
{
	protected abstract function urlByRouteSections (\MvcCore\IRoute & $route, array & $params = [], $urlParamRouteName = NULL);

	/**
	 * Get `TRUE` if path with query string target homepage - `/` (or `/index.php` - request script name)
	 * @param string $pathWithQueryString URL path part with possible query string.
	 * @return bool
	 */
	protected function urlIsHomePath ($pathWithQueryString) {
		$questionMarkPos = mb_strpos($pathWithQueryString, '?');
		$pathWithoutQueryString = $questionMarkPos !== FALSE 
			? mb_substr($pathWithQueryString, 0, $questionMarkPos)
			: $pathWithQueryString;
		return trim($pathWithoutQueryString, '/') === '' || $pathWithoutQueryString == $this->request->GetScriptName();
	}

	/**
	 * TODO: dopsat
	 * @param string $urlBaseSection URL section with domain part and possible base path part.
	 * @param string $urlPathWithQuerySection URL section with path and query string.
	 * @param array $systemParams System params to create URL prefixes from array values.
	 * @param bool $urlPathWithQueryIsHome `TRUE` if URL section with path and query string targets `/`  (or `/index.php` - request script name)
	 * @return string
	 */
	protected function urlByRoutePrefixSystemParams ($urlBaseSection, $urlPathWithQuerySection, array $systemParams = [], $urlPathWithQueryIsHome = NULL) {
		// complete prefixes section from system params
		$urlPrefixesSection = trim(implode('/', array_values($systemParams)), '/');
		$urlPrefixesSectionHasValue = $urlPrefixesSection !== '';

		if ($urlPrefixesSectionHasValue) {
			$urlPrefixesSection = '/' . $urlPrefixesSection;

			// finalizing possible trailing slash after prefix if any prefix
			if ($this->trailingSlashBehaviour === \MvcCore\IRouter::TRAILING_SLASH_REMOVE) {
				if ($urlPathWithQueryIsHome === NULL)
					$urlPathWithQueryIsHome = $this->urlIsHomePath($urlPathWithQuerySection);
				if ($urlPathWithQueryIsHome)
					$urlPathWithQuerySection = ltrim($urlPathWithQuerySection, '/');	
			}
		}
		//x([func_get_args(), $urlBaseSection, $urlPrefixesSection, $urlPathWithQuerySection]);
		return $urlBaseSection . $urlPrefixesSection . $urlPathWithQuerySection;
	}
}
