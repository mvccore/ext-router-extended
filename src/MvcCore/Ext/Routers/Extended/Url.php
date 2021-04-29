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
trait Url {

	/**
	 * Complete semi-finished result URL as two section strings and system 
	 * params array. First section as base section with scheme, domain and base 
	 * path, second section as application requested path and query string and 
	 * third section as system params like `localization` or `media_version`.
	 * Those params could be inserted between first two sections as system 
	 * params in result URL by router behaviour and default values. Or it could 
	 * be inserted into domain part in more extended routers.
	 * Example:
	 *	Input (`\MvcCore\Route::$reverse`):
	 *	`[
	 *		"en"	=> "/products-list/<name>/<color>"`,
	 *		"de"	=> "/produkt-liste/<name>/<color>"`,
	 *	]`
	 *	Input ($params):
	 *		`array(
	 *			"name"			=> "cool-product-name",
	 *			"color"			=> "red",
	 *			"variant"		=> ["L", "XL"],
	 *			"localization"	=> "en-US",
	 *			"media_version"	=> "mobile",
	 *		);`
	 *	Output:
	 *		`[
	 *			"/application/base/bath", 
	 *			"/products-list/cool-product-name/blue?variant[]=L&amp;variant[]=XL", 
	 *			["media_version" => "m", "localization" => "en-US"]
	 *		]`
	 * @param \MvcCore\Route $route
	 * @param array $params
	 * @param string $urlParamRouteName
	 * @return array `string $urlBaseSection, string $urlPathWithQuerySection, array $systemParams`
	 */
	protected abstract function urlByRouteSections (\MvcCore\IRoute $route, array & $params = [], $urlParamRouteName = NULL);

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
	 * Complete final URL, simply concatenate strings from three given sources:
	 * - `$urlBaseSection`
	 *   - Begin URL part containing http, domain and base path like:
	 *     `https://domain.com/path/to/app`
	 * - `$urlPathWithQuerySection`
	 *   - Subject url part with application path and possible query string:
	 *     `/some/path?with=query`
	 * - `$systemParams`
	 *   - Array to implode it's values into string with system params 
	 *     like media site version or localization:
	 *     `['media_version' => 'm', 'localization' => 'en-US']`
	 * Example output:
	 * - `https://domain.com/path/to/app/m/en-US/some/path?with=query`
	 * @param string	$urlBaseSection				URL section with domain part and possible base path part.
	 * @param string	$urlPathWithQuerySection	URL section with path and query string.
	 * @param array		$systemParams				System params to create URL prefixes from array values.
	 * @param bool		$urlPathWithQueryIsHome		`TRUE` if URL section with path and query string targets `/` 
	 *												(or `/index.php` - request script name)
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

		return $urlBaseSection . $urlPrefixesSection . $urlPathWithQuerySection;
	}
}
