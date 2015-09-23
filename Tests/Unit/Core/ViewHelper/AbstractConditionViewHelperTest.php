<?php
namespace TYPO3\Fluid\Tests\Unit\Core\ViewHelper;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Fluid".           *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Fluid\Core\ViewHelper\AbstractConditionViewHelper;
use TYPO3\Fluid\ViewHelpers\ViewHelperBaseTestcase;

require_once(__DIR__ . '/../../ViewHelpers/ViewHelperBaseTestcase.php');

/**
 * Testcase for Condition ViewHelper
 */
class AbstractConditionViewHelperTest extends ViewHelperBaseTestcase
{
    /**
     * @var AbstractConditionViewHelper|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $viewHelper;

    public function setUp()
    {
        parent::setUp();
        $this->viewHelper = $this->getAccessibleMock(\TYPO3\Fluid\Core\ViewHelper\AbstractConditionViewHelper::class, array('getRenderingContext', 'renderChildren', 'hasArgument'));
        $this->viewHelper->expects($this->any())->method('getRenderingContext')->will($this->returnValue($this->renderingContext));
        $this->injectDependenciesIntoViewHelper($this->viewHelper);
    }

    /**
     * @test
     */
    public function renderThenChildReturnsAllChildrenIfNoThenViewHelperChildExists()
    {
        $this->viewHelper->expects($this->any())->method('renderChildren')->will($this->returnValue('foo'));

        $actualResult = $this->viewHelper->_call('renderThenChild');
        $this->assertEquals('foo', $actualResult);
    }

    /**
     * @test
     */
    public function renderThenChildReturnsThenViewHelperChildIfConditionIsTrueAndThenViewHelperChildExists()
    {
        $mockThenViewHelperNode = $this->getMock(\TYPO3\Fluid\Core\Parser\SyntaxTree\ViewHelperNode::class, array('getViewHelperClassName', 'evaluate'), array(), '', false);
        $mockThenViewHelperNode->expects($this->at(0))->method('getViewHelperClassName')->will($this->returnValue(\TYPO3\Fluid\ViewHelpers\ThenViewHelper::class));
        $mockThenViewHelperNode->expects($this->at(1))->method('evaluate')->with($this->renderingContext)->will($this->returnValue('ThenViewHelperResults'));

        $this->viewHelper->setChildNodes(array($mockThenViewHelperNode));
        $actualResult = $this->viewHelper->_call('renderThenChild');
        $this->assertEquals('ThenViewHelperResults', $actualResult);
    }

    /**
     * @test
     */
    public function renderThenChildReturnsValueOfThenArgumentIfItIsSpecified()
    {
        $this->viewHelper->expects($this->atLeastOnce())->method('hasArgument')->with('then')->will($this->returnValue(true));
        $this->arguments['then'] = 'ThenArgument';
        $this->injectDependenciesIntoViewHelper($this->viewHelper);

        $actualResult = $this->viewHelper->_call('renderThenChild');
        $this->assertEquals('ThenArgument', $actualResult);
    }

    /**
     * @test
     */
    public function renderThenChildReturnsEmptyStringIfChildNodesOnlyContainElseViewHelper()
    {
        $mockElseViewHelperNode = $this->getMock(\TYPO3\Fluid\Core\Parser\SyntaxTree\ViewHelperNode::class, array('getViewHelperClassName', 'evaluate'), array(), '', false);
        $mockElseViewHelperNode->expects($this->any())->method('getViewHelperClassName')->will($this->returnValue(\TYPO3\Fluid\ViewHelpers\ElseViewHelper::class));
        $this->viewHelper->setChildNodes(array($mockElseViewHelperNode));
        $this->viewHelper->expects($this->never())->method('renderChildren')->will($this->returnValue('Child nodes'));

        $actualResult = $this->viewHelper->_call('renderThenChild');
        $this->assertEquals('', $actualResult);
    }

    /**
     * @test
     */
    public function renderElseChildReturnsEmptyStringIfConditionIsFalseAndNoElseViewHelperChildExists()
    {
        $actualResult = $this->viewHelper->_call('renderElseChild');
        $this->assertEquals('', $actualResult);
    }

    /**
     * @test
     */
    public function renderElseChildRendersElseViewHelperChildIfConditionIsFalseAndNoThenViewHelperChildExists()
    {
        $mockElseViewHelperNode = $this->getMock(\TYPO3\Fluid\Core\Parser\SyntaxTree\ViewHelperNode::class, array('getViewHelperClassName', 'evaluate', 'setRenderingContext'), array(), '', false);
        $mockElseViewHelperNode->expects($this->at(0))->method('getViewHelperClassName')->will($this->returnValue(\TYPO3\Fluid\ViewHelpers\ElseViewHelper::class));
        $mockElseViewHelperNode->expects($this->at(1))->method('evaluate')->with($this->renderingContext)->will($this->returnValue('ElseViewHelperResults'));

        $this->viewHelper->setChildNodes(array($mockElseViewHelperNode));
        $actualResult = $this->viewHelper->_call('renderElseChild');
        $this->assertEquals('ElseViewHelperResults', $actualResult);
    }

    /**
     * @test
     */
    public function thenArgumentHasPriorityOverChildNodesIfConditionIsTrue()
    {
        $mockThenViewHelperNode = $this->getMock(\TYPO3\Fluid\Core\Parser\SyntaxTree\ViewHelperNode::class, array('getViewHelperClassName', 'evaluate', 'setRenderingContext'), array(), '', false);
        $mockThenViewHelperNode->expects($this->never())->method('evaluate');

        $this->viewHelper->setChildNodes(array($mockThenViewHelperNode));

        $this->viewHelper->expects($this->atLeastOnce())->method('hasArgument')->with('then')->will($this->returnValue(true));
        $this->arguments['then'] = 'ThenArgument';

        $this->injectDependenciesIntoViewHelper($this->viewHelper);

        $actualResult = $this->viewHelper->_call('renderThenChild');
        $this->assertEquals('ThenArgument', $actualResult);
    }

    /**
     * @test
     */
    public function renderReturnsValueOfElseArgumentIfConditionIsFalse()
    {
        $this->viewHelper->expects($this->atLeastOnce())->method('hasArgument')->with('else')->will($this->returnValue(true));
        $this->arguments['else'] = 'ElseArgument';
        $this->injectDependenciesIntoViewHelper($this->viewHelper);

        $actualResult = $this->viewHelper->_call('renderElseChild');
        $this->assertEquals('ElseArgument', $actualResult);
    }

    /**
     * @test
     */
    public function elseArgumentHasPriorityOverChildNodesIfConditionIsFalse()
    {
        $mockElseViewHelperNode = $this->getMock(\TYPO3\Fluid\Core\Parser\SyntaxTree\ViewHelperNode::class, array('getViewHelperClassName', 'evaluate', 'setRenderingContext'), array(), '', false);
        $mockElseViewHelperNode->expects($this->any())->method('getViewHelperClassName')->will($this->returnValue(\TYPO3\Fluid\ViewHelpers\ElseViewHelper::class));
        $mockElseViewHelperNode->expects($this->never())->method('evaluate');

        $this->viewHelper->setChildNodes(array($mockElseViewHelperNode));

        $this->viewHelper->expects($this->atLeastOnce())->method('hasArgument')->with('else')->will($this->returnValue(true));
        $this->arguments['else'] = 'ElseArgument';
        $this->injectDependenciesIntoViewHelper($this->viewHelper);

        $actualResult = $this->viewHelper->_call('renderElseChild');
        $this->assertEquals('ElseArgument', $actualResult);
    }
}
