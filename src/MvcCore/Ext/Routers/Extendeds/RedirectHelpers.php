<?php

/**
 * MvcCore
 *
 * This source file is subject to the BSD 3 License
 * For the full copyright and license information, please view
 * the LICENSE.md file that are distributed with this source code.
 *
 * @copyright	Copyright (c) 2016 Tom Flidr (https://github.com/mvccore)
 * @license		https://mvccore.github.io/docs/mvccore/5.0.0/LICENCE.md
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
trait RedirectHelpers {

	/**
	 * If local request object global collection `$_GET` contains any items
	 * and if controller and action in collection have the same values as 
	 * default controller and action values, unset them from request global 
	 * `$_GET` collection.
	 * @return void
	 */
	protected function removeDefaultCtrlActionFromGlobalGet () {
		/** @var $this \MvcCore\Ext\Routers\Extended */
		if ($this->requestGlobalGet) {
			$toolClass = $this->application->GetToolClass();
			list($dfltCtrlPc, $dftlActionPc) = $this->application->GetDefaultControllerAndActionNames();
			$dfltCtrlDc = $toolClass::GetDashedFromPascalCase($dfltCtrlPc);
			$dftlActionDc = $toolClass::GetDashedFromPascalCase($dftlActionPc);
			if (isset($this->requestGlobalGet[static::URL_PARAM_CONTROLLER]) && isset($this->requestGlobalGet[static::URL_PARAM_ACTION]))
				if (
					$this->requestGlobalGet[static::URL_PARAM_CONTROLLER] == $dfltCtrlDc && 
					$this->requestGlobalGet[static::URL_PARAM_ACTION] == $dftlActionDc
				)
					unset($this->requestGlobalGet[static::URL_PARAM_CONTROLLER], $this->requestGlobalGet[static::URL_PARAM_ACTION]);
		}
	}

	/**
	 * Add all remaining params in `$this->requestGlobalGet` into given reference URL string `$targetUrl`.
	 * @param string $targetUrl 
	 */
	protected function redirectAddAllRemainingInGlobalGet (& $targetUrl) {
		/** @var $this \MvcCore\Ext\Routers\Extended */
		if ($this->requestGlobalGet) {
			$amp = $this->getQueryStringParamsSepatator();
			//foreach ($this->requestGlobalGet as $paramName => $paramValue)
			//	$paramValue = rawurldecode($paramValue);
			$questionMarkDelimiter = mb_strpos($targetUrl, '?') === FALSE ? '?' : $amp;
			$targetUrl .= $questionMarkDelimiter . str_replace(
				'%2F', '/', 
				http_build_query($this->requestGlobalGet, '', $amp, PHP_QUERY_RFC3986)
			);
		}
	}
}
