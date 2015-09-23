<?php
namespace TYPO3\Fluid\Tests\Unit\Core\Rendering;

/*                                                                        *
 * This script belongs to the Flow framework.                             *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the MIT license.                                          *
 *                                                                        */

/**
 * Testcase for ParsingState
 *
 */
class RenderingContextTest extends \TYPO3\Flow\Tests\UnitTestCase
{
    /**
     * Parsing state
     * @var \TYPO3\Fluid\Core\Rendering\RenderingContextInterface
     */
    protected $renderingContext;

    public function setUp()
    {
        $this->renderingContext = new \TYPO3\Fluid\Core\Rendering\RenderingContext();
    }

    /**
     * @test
     */
    public function templateVariableContainerCanBeReadCorrectly()
    {
        $templateVariableContainer = $this->getMock('TYPO3\Fluid\Core\ViewHelper\TemplateVariableContainer');
        $this->renderingContext->injectTemplateVariableContainer($templateVariableContainer);
        $this->assertSame($this->renderingContext->getTemplateVariableContainer(), $templateVariableContainer, 'Template Variable Container could not be read out again.');
    }

    /**
     * @test
     */
    public function controllerContextCanBeReadCorrectly()
    {
        $controllerContext = $this->getMock('TYPO3\Flow\Mvc\Controller\ControllerContext', array(), array(), '', false);
        $this->renderingContext->setControllerContext($controllerContext);
        $this->assertSame($this->renderingContext->getControllerContext(), $controllerContext);
    }

    /**
     * @test
     */
    public function viewHelperVariableContainerCanBeReadCorrectly()
    {
        $viewHelperVariableContainer = $this->getMock('TYPO3\Fluid\Core\ViewHelper\ViewHelperVariableContainer');
        $this->renderingContext->injectViewHelperVariableContainer($viewHelperVariableContainer);
        $this->assertSame($viewHelperVariableContainer, $this->renderingContext->getViewHelperVariableContainer());
    }
}
