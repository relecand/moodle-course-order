<?php 

/**
 * Controller.php
 * Klasse zur Controller-Verarbeitung
 * @auhtor Robert Leonhardt
 */
class Controller
{
	/**
	 * Controller-Haupttyp (VIEW, DO)
	 * @var    string
	 * @access private
	 */
	private $controllerType;

	/**
	 * Konstruktormethode zum Initialisieren des Controllers
	 * @param  mixed $do
	 * @param  mixed $view
	 * // TODO @param string $alternative 
	 * @access public
	 */
	public function __construct( $do = false, $view = false )
	{
		$this -> controllerType = !$do ? 'view' : 'do';
	}

	/**
	 * __toString-Methode zur Rückgabe der Controllerdatei
	 * @return string
	 * @access public
	 */
	public function __toString()
	{
		## Hauptcontroller zurückliefern
		return CONTROLLER . $this -> controllerType . '.controller.php';
	}
}

?>