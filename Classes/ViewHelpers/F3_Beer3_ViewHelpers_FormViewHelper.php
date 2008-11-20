<?php
declare(ENCODING = 'utf-8');
namespace F3::Beer3::ViewHelpers;

/*                                                                        *
 * This script is part of the TYPO3 project - inspiring people to share!  *
 *                                                                        *
 * TYPO3 is free software; you can redistribute it and/or modify it under *
 * the terms of the GNU General Public License version 2 as published by  *
 * the Free Software Foundation.                                          *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General      *
 * Public License for more details.                                       *
 *                                                                        */

/**
 * @package Beer3
 * @subpackage ViewHelpers
 * @version $Id:$
 */
/**
 * Form view helper. Generates a <form> Tag.
 *
 * @package Beer3
 * @subpackage ViewHelpers
 * @version $Id:$
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 * @scope prototype
 */
class FormViewHelper extends F3::Beer3::Core::TagBasedViewHelper {
	
	/**
	 * Initialize arguments.
	 * 
	 * @return void
	 * @author Sebastian Kurfürst <sebastian@typo3.org>
	 */
	public function initializeArguments() {
		$this->registerArgument('controller', 'string', 'name of controller to call the current action on');
		$this->registerArgument('action', 'string', 'name of action to call');
		$this->registerArgument('package', 'string', 'name of package to call');
		
		$this->registerTagAttribute('enctype', 'string', 'MIME type with which the form is submitted');
		$this->registerTagAttribute('method', 'string', 'Transfer type (GET or POST)');
		$this->registerTagAttribute('name', 'string', 'Name of form');
		$this->registerTagAttribute('onreset', 'string', 'JavaScript: On reset of the form');
		$this->registerTagAttribute('onsubmit', 'string', 'JavaScript: On submit of the form');
		
		$this->registerUniversalTagAttributes();
	}

	/**
	 * Render the form.
	 *
	 * @return string FORM-Tag.
	 * @author Sebastian Kurfürst <sebastian@typo3.org>
	 */
	public function render() {
		$uriHelper = $this->variableContainer->get('view')->getViewHelper('F3::FLOW3::MVC::View::Helper::URIHelper');
		
		$method = ( $this->arguments['method'] ? $this->arguments['method'] : 'GET' );
		
		$formActionUrl = $uriHelper->URIFor($this->arguments['action'], array(), $this->arguments['controller'], $this->arguments['package']);
		
		$out = '<form action="' . $formActionUrl . '" ' . $this->renderTagAttributes() . '>';
		$out .= $this->renderChildren();
		$out .= '</form>';
		
		return $out;
	}
}

?>