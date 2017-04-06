<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017
 */

namespace Aimeos\MW\View\Engine;


class TwigTest extends \PHPUnit_Framework_TestCase
{
	private $object;
	private $mock;


	protected function setUp()
	{
		if( !class_exists( '\Twig_Environment' ) ) {
			$this->markTestSkipped( 'Twig_Environment is not available' );
		}

		$this->mock = $this->getMockBuilder( '\Twig_Environment' )
			->setMethods( array( 'getLoader', 'loadTemplate' ) )
			->disableOriginalConstructor()
			->getMock();

		$this->object = new \Aimeos\MW\View\Engine\Twig( $this->mock );
	}


	protected function tearDown()
	{
		unset( $this->object, $this->mock );
	}


	public function testRender()
	{
		$v = new \Aimeos\MW\View\Standard( [] );

		$view = $this->getMockBuilder( '\Twig_Template' )
			->setConstructorArgs( array ( $this->mock ) )
			->setMethods( array( 'getBlockNames', 'render', 'renderBlock' ) )
			->getMockForAbstractClass();

		$view->expects( $this->once() )->method( 'getBlockNames' )
			->will( $this->returnValue( array( 'testblock' ) ) );

		$view->expects( $this->once() )->method( 'renderBlock' )
			->will( $this->returnValue( 'block content' ) );

		$view->expects( $this->once() )->method( 'render' )
			->will( $this->returnValue( 'test' ) );


		$loader = $this->getMockBuilder( '\Twig_LoaderInterface' )
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$this->mock->expects( $this->once() )->method( 'getLoader' )
			->will( $this->returnValue( $loader) );

		$this->mock->expects( $this->once() )->method( 'loadTemplate' )
			->will( $this->returnValue( $view) );


		$result = $this->object->render( $v, __FILE__, array( 'key' => 'value' ) );
		$this->assertEquals( 'test', $result );
	}
}
