<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2026
 */

namespace Aimeos\Base\View\Engine;


class TwigTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $mock;


	protected function setUp() : void
	{
		if( !class_exists( '\Twig\Environment' ) ) {
			$this->markTestSkipped( 'Twig\Environment is not available' );
		}

		$this->mock = $this->getMockBuilder( '\Twig\Environment' )
			->onlyMethods( array( 'getExtensions', 'getLoader', 'loadTemplate', 'useYield' ) )
			->disableOriginalConstructor()
			->getMock();

		$this->mock->method( 'useYield' )->willReturn( false );

		$this->object = new \Aimeos\Base\View\Engine\Twig( $this->mock );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->mock );
	}


	public function testRender()
	{
		$v = new \Aimeos\Base\View\Standard( [] );

		$this->mock->expects( $this->any() )->method( 'getExtensions' )
			->willReturn( array( [] ) );


		$view = new TwigTestTemplate( $this->mock );
		$view->blockNames = ['testblock'];
		$view->blockContent = 'block content';
		$view->renderContent = 'test';


		$loader = $this->createStub( \Twig\Loader\LoaderInterface::class );

		$this->mock->expects( $this->exactly( 2 ) )->method( 'getLoader' )
			->willReturn( $loader );

		$this->mock->expects( $this->once() )->method( 'loadTemplate' )
			->willReturn( $view );


		$result = $this->object->render( $v, __FILE__, array( 'key' => 'value' ) );
		$this->assertEquals( 'test', $result );
	}
}


class TwigTestTemplate extends \Twig\Template
{
	public array $blockNames = [];
	public string $blockContent = '';
	public string $renderContent = '';


	public function getTemplateName() : string
	{
		return 'test';
	}


	public function getDebugInfo() : array
	{
		return [];
	}


	public function getSourceContext() : \Twig\Source
	{
		return new \Twig\Source( '', 'test' );
	}


	public function getBlockNames( array $context, array $blocks = [] ) : array
	{
		return $this->blockNames;
	}


	public function renderBlock( $name, array $context, array $blocks = [], $useBlocks = true ) : string
	{
		return $this->blockContent;
	}


	public function render( array $context ) : string
	{
		return $this->renderContent;
	}


	protected function doDisplay( array $context, array $blocks = [] ) : iterable
	{
		return [];
	}
}
