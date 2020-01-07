<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2018
 */

namespace Aimeos\MW\View\Engine;


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
			->setMethods( array( 'getExtensions', 'getLoader', 'load', 'mergeGlobals' ) )
			->disableOriginalConstructor()
			->getMock();

		$this->object = new \Aimeos\MW\View\Engine\Twig( $this->mock );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->mock );
	}


	public function testRender()
	{
		$v = new \Aimeos\MW\View\Standard( [] );

		$this->mock->expects( $this->any() )->method( 'getExtensions' )
			->will( $this->returnValue( array( [] ) ) );

		$this->mock->expects( $this->any() )->method( 'mergeGlobals' )
			->will( $this->returnArgument( 0 ) );


		$view = $this->getMockBuilder( '\Twig\Template' )
			->setConstructorArgs( array ( $this->mock ) )
			->setMethods( array( 'displayBlock', 'getBlockNames', 'render' ) )
			->getMockForAbstractClass();

		$view->expects( $this->once() )->method( 'displayBlock' );

		$view->expects( $this->once() )->method( 'getBlockNames' )
			->will( $this->returnValue( array( 'testblock' ) ) );

		$view->expects( $this->once() )->method( 'render' )
			->will( $this->returnValue( 'test' ) );


		$loader = $this->getMockBuilder( '\Twig\Loader\LoaderInterface' )
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$this->mock->expects( $this->once() )->method( 'getLoader' )
			->will( $this->returnValue( $loader) );

		$this->mock->expects( $this->once() )->method( 'load' )
			->will( $this->returnValue( new \Twig\TemplateWrapper( $this->mock, $view ) ) );


		$result = $this->object->render( $v, __FILE__, array( 'key' => 'value' ) );
		$this->assertEquals( 'test', $result );
	}
}
