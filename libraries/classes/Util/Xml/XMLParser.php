<?php
/**
  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; version 3 of the License.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.
*/

/**
 * Parses an XML document into an object structure much like the SimpleXML extension.
 *
 * @package Util
 * @author Adam A. Flynn <adamaflynn@criticaldevelopment.net>
 * @copyright Copyright (c) 2005-2007, Adam A. Flynn
 *
 * @version 1.3.0
 */
class XMLParser
{
    /**
     * The XML parser
     *
     * @access protected
     * @var    resource
     */
    protected $_parser;

    /**
    * The XML document
    *
    * @access protected
    * @var    string
    */
    protected $_xml;

    /**
    * Document tag
    *
    * @access protected
    * @var    object
    */
    protected $_document;

    /**
    * Current object depth
    *
    * @access protected
    * @var    array
    */
    protected $_stack;

    /**
     * Whether or not to replace dashes and colons in tag
     * names with underscores.
     *
     * @access protected
     * @var    bool
     */
    protected $_cleanTagNames;

    /**
     * Factory method to initialize by file path.
     * @access public
     * @static
     * @param  string $filename The string of the XML file
     * @param  boolean $cleanTagNames
     * @return self
     *
     */
    public static function initByFilePath($filename, $cleanTagNames = true)
    {
    	return new self($filename, $cleanTagNames);
    }

    /**
     * Factory method for init parser by added content.
     * @access public
     * @static
     * @param  string $content
     * @param  boolean $cleanTagNames
     * @return self
     */
    public static function initByContent($content, $cleanTagNames = true)
    {
		return new self($content, $cleanTagNames, false);
    }

    /**
     * Constructor. Loads XML document.
     *
     * @access public
     * @param  string $xmlStructure The string of the XML file
     * @param  boolean $cleanTagNames
     * @param  boolean $readContent
     * @return void
     */
    public function __construct($XMLFile, $cleanTagNames = true, $readContent = true)
    {
    	if ($readContent == true) {
	        //Load XML document
	        $file = new FileHandler( $XMLFile );
	        $this->_xml = $file->getContents();
    	} else {
	    	$this->_xml = $XMLFile;
	    }

        //Set stack to an array
        $this->_stack = array();

        //Set whether or not to clean tag names
        $this->_cleanTagNames = $cleanTagNames;

        $this->_parse();
    }

    /**
     * Returns with document's element
     *
     * @access public
     * @param  string $name
     * @return object
     */
    public function __get($name)
    {
    	return $this->_document->$name;
    }

    /**
     * Returns with DOM document.
     *
     * @access public
     * @return XMLTag
     */
    public function getDocument()
    {
    	return $this->_document;
    }

    /**
     * Initiates and runs PHP's XML parser
     */
    protected function _parse()
    {
        //Create the parser resource
        $this->_parser = xml_parser_create();

        //Set the handlers
        xml_set_object($this->_parser, $this);
        xml_parser_set_option($this->_parser, XML_OPTION_CASE_FOLDING, 0);
        xml_set_element_handler($this->_parser, '_StartElement', '_EndElement');
        xml_set_character_data_handler($this->_parser, '_CharacterData');

        //Error handling
        if (!xml_parse($this->_parser, $this->_xml)){
            $this->_HandleError(xml_get_error_code($this->_parser), xml_get_current_line_number($this->_parser), xml_get_current_column_number($this->_parser));
		}

        //Free the parser
        xml_parser_free($this->_parser);
    }

    /**
     * Handles an XML parsing error
     *
     * @param int $code XML Error Code
     * @param int $line Line on which the error happened
     * @param int $col Column on which the error happened
     */
    protected function _HandleError($code, $line, $col)
    {
        BasicErrorHandler::trace('XML Parsing Error at '.$line.':'.$col.'. Error '.$code.': '.xml_error_string($code));
    }


    /**
     * Gets the XML output of the PHP structure within $this->_document
     *
     * @return string
     */
    public function GenerateXML()
    {
        return $this->_document->GetXML();
    }

    /**
     * Gets the reference to the current direct parent
     *
     * @return object
     */
    protected function _GetStackLocation()
    {
        //Returns the reference to the current direct parent
        return end($this->_stack);
    }

    /**
     * Handler function for the start of a tag
     *
     * @param resource $parser
     * @param string $name
     * @param array $attrs
     */
    protected function _StartElement($parser, $name, $attrs = array())
    {
        //Make the name of the tag lower case
        //$name = strtolower($name);

        //Check to see if tag is root-level
        if (count($this->_stack) == 0)
        {
            //If so, set the document as the current tag
            $this->_document = new XMLTag($name, $attrs);

            //And start out the stack with the document tag
            $this->_stack = array(&$this->_document);
        }
        //If it isn't root level, use the stack to find the parent
        else
        {
            //Get the reference to the current direct parent
            $parent = $this->_GetStackLocation();

            $parent->AddChild($name, $attrs, count($this->_stack), $this->_cleanTagNames);

            //If the cleanTagName feature is on, clean the tag names
            if($this->_cleanTagNames)
                $name = str_replace(array(':', '-'), '_', $name);

            //Update the stack
            $this->_stack[] = end($parent->$name);
        }
    }

    /**
     * Handler function for the end of a tag
     *
     * @param resource $parser
     * @param string $name
     */
    protected function _EndElement($parser, $name)
    {
        //Update stack by removing the end value from it as the parent
        array_pop($this->_stack);
    }

    /**
     * Handler function for the character data within a tag
     *
     * @param resource $parser
     * @param string $data
     */
    protected function _CharacterData($parser, $data)
    {
        //Get the reference to the current parent object
        $tag = $this->_GetStackLocation();

        //Assign data to it
        $tag->tagData .= trim($data);
    }
}