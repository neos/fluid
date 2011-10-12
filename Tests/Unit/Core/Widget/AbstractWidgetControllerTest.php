<?php
namespace TYPO3\Fluid\Tests\Unit\Core\Widget;

/*                                                                        *
 * This script belongs to the FLOW3 package "Fluid".                      *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 *  of the License, or (at your option) any later version.                *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/**
 * Testcase for AbstractWidgetController
 *
 */
class AbstractWidgetControllerTest extends \TYPO3\FLOW3\Tests\UnitTestCase {

	/**
	 * @test
	 * @expectedException TYPO3\Fluid\Core\Widget\Exception\WidgetContextNotFoundException
	 * @author Sebastian Kurfürst <sebastian@typo3.org>
	 */
	public function processRequestShouldThrowExceptionIfWidgetContextNotFound() {
		$request = $this->getMock('TYPO3\FLOW3\MVC\Web\SubRequest', array('dummy'), array(), '', FALSE);
		$response = $this->getMock('TYPO3\FLOW3\MVC\ResponseInterface');

		$abstractWidgetController = $this->getMock('TYPO3\Fluid\Core\Widget\AbstractWidgetController', array('dummy'), array(), '', FALSE);
		$abstractWidgetController->processRequest($request, $response);
	}

	/**
	 * @test
	 * @author Sebastian Kurfürst <sebastian@typo3.org>
	 */
	public function processRequestShouldSetWidgetConfiguration() {

		$widgetContext = $this->getMock('TYPO3\Fluid\Core\Widget\WidgetContext', array('getWidgetConfiguration'));
		$widgetContext->expects($this->once())->method('getWidgetConfiguration')->will($this->returnValue('myConfiguration'));

		$request = $this->getMock('TYPO3\FLOW3\MVC\Web\SubRequest', array('dummy'), array(), '', FALSE);
		$request->setArgument('__widgetContext', $widgetContext);

		$response = $this->getMock('TYPO3\FLOW3\MVC\ResponseInterface');

		$abstractWidgetController = $this->getAccessibleMock('TYPO3\Fluid\Core\Widget\AbstractWidgetController', array('resolveActionMethodName', 'initializeActionMethodArguments', 'initializeActionMethodValidators', 'mapRequestArgumentsToControllerArguments', 'detectFormat', 'resolveView', 'callActionMethod'));
		$abstractWidgetController->_set('argumentsMappingResults', new \TYPO3\FLOW3\Error\Result());
		$abstractWidgetController->_set('flashMessageContainer', new \TYPO3\FLOW3\MVC\FlashMessageContainer());

		$abstractWidgetController->processRequest($request, $response);

		$widgetConfiguration = $abstractWidgetController->_get('widgetConfiguration');
		$this->assertEquals('myConfiguration', $widgetConfiguration);
	}
}
?>