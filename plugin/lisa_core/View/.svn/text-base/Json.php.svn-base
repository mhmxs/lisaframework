<?php
/**
 * Json implementation of View.
 * @package Core
 * @subpackage View
 * @author kovacsricsi
 */
namespace lisa_core\View;

class Json implements \lisa_core_api\IView
{
	/**
	 * Output data.
	 * @access protcted
	 * @var array
	 */
	protected $_data;

	/**
	 * Inicialize View class.
	 * @access public
	 * @static
	 * @return \lisa_core_api\IView
	 */
    public static function getInstance() {
		return new self();
	}

	/**
	 * Constructor sets default variables.
	 * @access private
	 * @return void
	 */
	private function __construct() {}

    /**
     * Set Json data
     * @param string $name
     * @param mixed $value
     */
    public function __set($name,  $value) {
		$this->_data[$name] = $value;
    }

	/**
	 * Set new content to output.
	 * @access public
	 * @param array $out
	 * @return void
	 */
	public function setContent(array $value){
		$this->_data = $value;
	}
	
	/**
	 * Concat  content with output.
	 * @access public
	 * @param string $out
	 * @return void
	 */
	public function addContent(array $data)
	{
		$this->_data = array_merge($this->_data, $data);
	}
	
	/**
	 * Clear content.
	 * @access public
	 * @return void
	 */
	public function clearContent()
	{
		$this->_data = array();
	}

    /**
	  * Get template of view.
	  * @access public
	  * @return bool
	  */
	public function getTemplate()
	{
		return null;
	}

	/**
	 * Returns current content.
	 * @access public
	 * @return string
	 */
	public function getContent()
	{
		return json_encode($this->_data);
	}
}
?>