<?php
/**
 * Json implementation of View.
 * @package Core
 * @subpackage View
 * @author kovacsricsi
 */
namespace Core\View;

class Json implements IView
{
	/**
	 * Output data.
	 * @access protcted
	 * @var array
	 */
	protected $_data;
	
	/**
	 * Constructor sets default variables.
	 * @access public
	 * @param mixed $out
	 * @return void
	 */
	public function __construct(array $data = null)
	{
		$this->_data = $data;
	}

    /**
     * Set Json data
     * @param string $name
     * @param mixed $value
     */
    public function __set($name,  $value) {
		$this->_data[$name] = $value;
    }

	/**
	 * Concat content with output.
	 * @access public
	 * @param string $out
	 * @return void
	 */
	public function addValue($name, $value)
	{
		$this->_data[$name][] = $value;
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
	 * Returns output.
	 * @access public
	 * @return string
	 */
	public function getOutput()
	{
		return json_encode($this->_data);
	}
}
?>