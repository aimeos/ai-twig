<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017
 * @package MW
 * @subpackage View
 */


namespace Aimeos\MW\View\Engine;


/**
 * Twig view engine implementation
 *
 * @package MW
 * @subpackage View
 */
class Twig implements Iface
{
	private $env;


	/**
	 * Initializes the view object
	 *
	 * @param \Twig_Environment $env Twig environment object
	 */
	public function __construct( \Twig_Environment $env )
	{
		$this->env = $env;
	}


	/**
	 * Renders the output based on the given template file name and the key/value pairs
	 *
	 * @param \Aimeos\MW\View\Iface $view View object
	 * @param string $filename File name of the view template
	 * @param array $values Associative list of key/value pairs
	 * @return string Output generated by the template
	 * @throws \Aimeos\MW\View\Exception If the template isn't found
	 */
	public function render( \Aimeos\MW\View\Iface $view, $filename, array $values )
	{
		$loader = $this->env->getLoader();
		$name = sprintf('__string_template__%s', hash('sha256', $filename, false ) );
		$chain = new \Twig_Loader_Chain( array( new \Twig_Loader_Array( array( $name => $filename ) ), $loader ) );

		$this->env->setLoader( $chain );

		try
		{
			$template = $this->env->loadTemplate( $name );
			$content = $template->render( $values );

			foreach( $template->getBlocks() as $name => $block ) {
				$view->block()->set( $name, $block );
			}

			return $content;
		}
		finally
		{
			$this->env->setLoader( $loader );
		}
	}
}
