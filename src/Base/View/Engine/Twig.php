<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2025
 * @package Base
 * @subpackage View
 */


namespace Aimeos\Base\View\Engine;


/**
 * Twig view engine implementation
 *
 * @package Base
 * @subpackage View
 */
class Twig implements Iface
{
	private $env;


	/**
	 * Initializes the view object
	 *
	 * @param \Twig\Environment $env Twig environment object
	 */
	public function __construct( \Twig\Environment $env )
	{
		$this->env = $env;
	}


	/**
	 * Renders the output based on the given template file name and the key/value pairs
	 *
	 * @param \Aimeos\Base\View\Iface $view View object
	 * @param string $filename File name of the view template
	 * @param array $values Associative list of key/value pairs
	 * @return string Output generated by the template
	 * @throws \Aimeos\Base\View\Exception If the template isn't found
	 */
	public function render( \Aimeos\Base\View\Iface $view, string $filename, array $values ) : string
	{
		$loader = $this->env->getLoader();

		if( ( $content = @file_get_contents( $filename ) ) === false ) {
			throw new \Aimeos\Base\View\Exception( sprintf( 'Template "%1$s" not found', $filename ) );
		}

		$custom = new \Twig\Loader\ArrayLoader( array( $filename => $content ) );
		$this->env->setLoader( new \Twig\Loader\ChainLoader( array( $custom, $loader ) ) );

		try
		{
			$template = $this->env->loadTemplate( $this->env->getTemplateClass( $filename ), $filename );
			$content = $template->render( $values );

			foreach( $template->getBlockNames( $values ) as $key ) {
				$view->block()->set( str_replace( '_', '/', $key ), $template->renderBlock( $key, $values ) );
			}

			$this->env->setLoader( $loader );

			return $content;
		}
		catch( \Exception $e )
		{
			$this->env->setLoader( $loader );
			throw $e;
		}
	}
}
