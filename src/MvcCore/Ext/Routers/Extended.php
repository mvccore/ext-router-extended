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
trait Extended {

	use \MvcCore\Ext\Routers\Extendeds\PropsGettersSetters;
	use \MvcCore\Ext\Routers\Extendeds\Preparing;
	use \MvcCore\Ext\Routers\Extendeds\RedirectHelpers;
	use \MvcCore\Ext\Routers\Extendeds\Redirect;
	use \MvcCore\Ext\Routers\Extendeds\Url;
}
