<?php

/**
 * XML Tag Object (php5.3)
 *
 * This object stores all of the direct children of itself in the $children array. They are also stored by
 * type as arrays. So, if, for example, this tag had 2 <font> tags as children, there would be a class member
 * called $font created as an array. $font[0] would be the first font tag, and $font[1] would be the second.
 *
 * To loop through all of the direct children of this object, the $children member should be used.
 *
 * To loop through all of the direct children of a specific tag for this object, it is probably easier
 * to use the arrays of the specific tag names, as explained above.
 *
 * @package Util
 * @subpackage Xml
 * @author Adam A. Flynn <adamaflynn@criticaldevelopment.net>
 * @copyright Copyright (c) 2005-2007, Adam A. Flynn
 */

namespace lisa_util\Xml;

class Tag {

	/**
	 * Array with the attributes of this XML tag.
	 *
	 * @access public
	 * @var    array
	 */
	public $tagAttrs;
	/**
	 * The name of the tag
	 *
	 * @access public
	 * @var    string
	 */
	public $tagName;
	/**
	 * The data the tag contains
	 * So, if the tag doesn't contain child tags, and just contains a string, it would go here
	 *
	 * @access public
	 * @var    stat
	 */
	public $tagData;
	/**
	 * Array of references to the objects of all direct children of this XML object
	 *
	 * @access public
	 * @var    array
	 */
	public $tagChildren;
	/**
	 * The number of parents this XML object has (number of levels from this tag to the root tag)
	 * Used presently only to set the number of tabs when outputting XML
	 *
	 * @access public
	 * @var    int
	 */
	public $tagParents;

	/**
	 * Constructor, sets up all the default values.
	 *
	 * @access public
	 * @param  string $name
	 * @param  array $attrs
	 * @param  int $parents
	 * @return Tag
	 */
	public function __construct($name, $attrs = array(), $parents = 0) {
		//Make the keys of the attr array lower case, and store the value
		//$this->tagAttrs = array_change_key_case($attrs, CASE_LOWER);
		$this->tagAttrs = $attrs;

		//Make the name lower case and store the value
		//$this->tagName = strtolower($name);
		$this->tagName = $name;

		//Set the number of parents
		$this->tagParents = $parents;

		//Set the types for children and data
		$this->tagChildren = array();
		$this->tagData = '';
	}

	/**
	 * Adds a direct child to this object.
	 *
	 * @access public
	 * @param  string $name
	 * @param  array $attrs
	 * @param  int $parents
	 * @param  bool $cleanTagName
	 * @return void
	 */
	public function AddChild($name, $attrs, $parents, $cleanTagName = true) {
		//If the tag is a reserved name, output an error
		if (in_array($name, array('tagChildren', 'tagAttrs', 'tagParents', 'tagData', 'tagName'))) {
			$msg = 'You have used a reserved name as the name of an XML tag. Please consult the documentation (http://www.criticaldevelopment.net/xml/) and rename the tag named "' . $name . '" to something other than a reserved name.';

			$eh = \Context::getService('ErrorHandler');
			if (!is_null($eh)) {
				$eh->trace($msg, E_USER_ERROR);
			} else {
				trigger_error($msg);
			}
			return;
		}

		//Create the child object itself
		$class = get_called_class();
		$child = new $class($name, $attrs, $parents);

		//If the cleanTagName feature is on, replace colons and dashes with underscores
		if ($cleanTagName)
			$name = str_replace(array(':', '-'), '_', $name);

		//Toss up a notice if someone's trying to to use a colon or dash in a tag name
		elseif (strstr($name, ':') || strstr($name, '-')) {
			$msg = 'Your tag named "' . $name . '" contains either a dash or a colon. Neither of these characters are friendly with PHP variable names, and, as such, you may have difficulty accessing them. You might want to think about enabling the cleanTagName feature (pass true as the second argument of the Parser constructor). For more details, see http://www.criticaldevelopment.net/xml/';
			
			$eh = \Context::getService('ErrorHandler');
			if (!is_null($eh)) {
				$eh->trace($msg, E_USER_NOTICE);
			} else {
				trigger_error($msg);
			}
		}

		//If there is no array already set for the tag name being added,
		//create an empty array for it
		if (!isset($this->$name))
			$this->$name = array();

		//Add the reference of it to the end of an array member named for the tag's name
		$this->{$name}[] = &$child;

		//Add the reference to the children array member
		$this->tagChildren[] = &$child;

		//Return a reference to this object for the stack
		return $this;
	}

	/**
	 * Returns the string of the XML document which would be generated from this object
	 *
	 * This function works recursively, so it gets the XML of itself and all of its children, which
	 * in turn gets the XML of all their children, which in turn gets the XML of all thier children,
	 * and so on. So, if you call GetXML from the document root object, it will return a string for
	 * the XML of the entire document.
	 *
	 * This function does not, however, return a DTD or an XML version/encoding tag. That should be
	 * handled by Parser::GetXML()
	 *
	 * @access public
	 * @return string
	 */
	public function GetXML() {
		//Start a new line, indent by the number indicated in $this->parents, add a <, and add the name of the tag
		$out = "\n" . str_repeat("\t", $this->tagParents) . '<' . $this->tagName;

		//For each attribute, add attr="value"
		foreach ($this->tagAttrs as $attr => $value)
			$out .= ' ' . $attr . '="' . $value . '"';

		//If there are no children and it contains no data, end it off with a />
		if (empty($this->tagChildren) && empty($this->tagData))
			$out .= " />";

		//Otherwise...
		else {
			//If there are children
			if (!empty($this->tagChildren)) {
				//Close off the start tag
				$out .= '>';

				//For each child, call the GetXML function (this will ensure that all children are added recursively)
				foreach ($this->tagChildren as $child)
					$out .= $child->GetXML();

				//Add the newline and indentation to go along with the close tag
				$out .= "\n" . str_repeat("\t", $this->tagParents);
			}

			//If there is data, close off the start tag and add the data
			elseif (!empty($this->tagData))
				$out .= '>' . $this->tagData;

			//Add the end tag
			$out .= '</' . $this->tagName . '>';
		}

		//Return the final output
		return $out;
	}

	/**
	 * Deletes this tag's child with a name of $childName and an index.
	 * of $childIndex
	 *
	 * @access public
	 * @param  string $childName
	 * @param  int $childIndex
	 * @return void
	 */
	public function Delete($childName, $childIndex = 0) {
		//Delete all of the children of that child
		$this->{$childName}[$childIndex]->DeleteChildren();

		//Destroy the child's value
		$this->{$childName}[$childIndex] = null;

		//Remove the child's name from the named array
		unset($this->{$childName}[$childIndex]);

		//Loop through the tagChildren array and remove any null
		//values left behind from the above operation
		for ($x = 0; $x < count($this->tagChildren); $x++) {
			if (is_null($this->tagChildren[$x]))
				unset($this->tagChildren[$x]);
		}
	}

	/**
	 * Removes all of the children of this tag in both name and value.
	 *
	 * @access public
	 * @return void
	 */
	private function DeleteChildren() {
		//Loop through all child tags
		for ($x = 0; $x < count($this->tagChildren); $x++) {
			//Do this recursively
			$this->tagChildren[$x]->DeleteChildren();

			//Delete the name and value
			$this->tagChildren[$x] = null;
			unset($this->tagChildren[$x]);
		}
	}

}

?>