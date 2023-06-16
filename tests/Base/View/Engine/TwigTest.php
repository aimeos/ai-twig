<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2023
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
			->onlyMethods( array( 'getExtensions', 'getLoader', 'loadTemplate' ) )
			->disableOriginalConstructor()
			->getMock();

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
			->will( $this->returnValue( array( [] ) ) );


		$view = $this->getMockBuilder( '\Twig\Template' )
			->setConstructorArgs( array( $this->mock ) )
			->onlyMethods( array( 'getBlockNames', 'render', 'renderBlock' ) )
			->getMockForAbstractClass();

		$view->expects( $this->once() )->method( 'getBlockNames' )
			->will( $this->returnValue( array( 'testblock' ) ) );

		$view->expects( $this->once() )->method( 'renderBlock' )
			->will( $this->returnValue( 'block content' ) );

		$view->expects( $this->once() )->method( 'render' )
			->will( $this->returnValue( 'test' ) );


		$loader = $this->getMockBuilder( '\Twig\Loader\LoaderInterface' )
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$this->mock->expects( $this->exactly( 2 ) )->method( 'getLoader' )
			->will( $this->returnValue( $loader ) );

		$this->mock->expects( $this->once() )->method( 'loadTemplate' )
			->will( $this->returnValue( $view ) );


		$result = $this->object->render( $v, __FILE__, array( 'key' => 'value' ) );
		$this->assertEquals( 'test', $result );
	}
}
