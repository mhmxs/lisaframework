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
 * Generate xls output.
 * @package Util
 * @author kovacsricsi
 */
namespace Util;

class XLSOutput
{
	protected $headers;
	protected $data;
	protected $author;
	protected $html;
	protected $charSet;

	protected $validCharSets = array(
		"UTF-8", "LATIN1", "LATIN2"
	);

	protected $numeralTypes = array(
		"integer", "double", "float"
	);

    /**
     * Constructor create XLS structure
     * @access public
     * @param array $headers
     * @param array $data
     * @param string $name of the sheet
     * @param boolean $html if html create <table></table> else xml structure
     * @param string $charSet
     * @return void
     */
	public function __construct(array $headers, array $data, $name = "Munkalap 1", $html = false, $charSet = "UTF-8")
	{
		$this->headers = $headers;
		$this->data    = $data;
		$this->name    = $name;
		$this->author  = $author;
		$this->html    = $html;

		if (!in_array($charSet, $this->validCharSets)) {
			throw new \Exception("Invalid charset!");
		}
		$this->charSet = $charSet;
	}

	/**
	 * Generate content.
	 * @access public
	 * @return string
	 */
	public function getContent()
	{
		$content = $this->html ? $this->generateHTMLOutput() : $this->generateOutput();

		if ($this->html && ($this->charSet != "LATIN2")) {
			$content = mb_convert_encoding($content, "LATIN2", $this->charSet);
		} elseif (!$this->html && ($this->charSet != "UTF-8")) {
			$content = mb_convert_encoding($content, "UTF-8", $this->charSet);
		}

		return $content;
	}

    /**
     * Send output to $filename file.
     * @access public
     * @param string $filename
     * @return void
     */
	public function send($filename)
	{
		$out = $this->getContent();

		header("Content-type: application/vnd.ms-excel");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		header("Content-Length: " . strlen($out));
		header("Pragma: public");
		header("Expires: 0");

		echo $out;
		die;
	}

    /**
     * Create HTML XLS structure
     * @access public
     * @return string
     */
	protected function generateHTMLOutput()
	{
		$out = "<table>";

		$out .= "<thead><tr>";
		foreach($this->headers as $header) {
			$out .= '<th>' . $header . '</th>';
		}
		$out .= "</tr></thead>";

		$out .= "<tbody>";
		foreach($this->data as $row) {
			$out .= '<tr>';

			foreach($row as $data) {
				$out .= '<td>' . $data . '</td>';
			}
			$out .= '</tr>';
		}
		$out .= "</tbody>";

		$out .= "</table>";

		return $out;
	}

     /**
     * Create XML XLS structure
     * @access public
     * @return string
     */
	protected function generateOutput()
	{
		$out = '<?xml version="1.0" encoding="UTF-8"?>
					<?mso-application progid="Excel.Sheet"?>
					<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns:x2="http://schemas.microsoft.com/office/excel/2003/xml" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:html="http://www.w3.org/TR/REC-html40" xmlns:c="urn:schemas-microsoft-com:office:component:spreadsheet">
						<OfficeDocumentSettings xmlns="urn:schemas-microsoft-com:office:office">
							<Colors>
								<Color>
									<Index>3</Index>
									<RGB>#c0c0c0</RGB>
								</Color>
								<Color>
									<Index>4</Index>
									<RGB>#ff0000</RGB>
								</Color>
							</Colors>
						</OfficeDocumentSettings>
						<ExcelWorkbook xmlns="urn:schemas-microsoft-com:office:excel">
							<WindowHeight>9000</WindowHeight>
							<WindowWidth>13860</WindowWidth>
							<WindowTopX>240</WindowTopX>
							<WindowTopY>75</WindowTopY>
							<ProtectStructure>False</ProtectStructure>
							<ProtectWindows>False</ProtectWindows>
						</ExcelWorkbook>
						<Styles>
							<Style ss:ID="Default" ss:Name="Default"/>
							<Style ss:ID="Head">
								<Font x:CharSet="238" x:Family="Swiss" ss:Bold="1"/>
							</Style>
							<Style ss:ID="Result" ss:Name="Result">
								<Font ss:Bold="1" ss:Italic="1" ss:Underline="Single"/>
							</Style>
							<Style ss:ID="Result2" ss:Name="Result2">
								<Font ss:Bold="1" ss:Italic="1" ss:Underline="Single"/>
								<NumberFormat ss:Format="Currency"/>
							</Style>
							<Style ss:ID="Heading" ss:Name="Heading">
								<Alignment ss:Horizontal="Center"/>
								<Font ss:Bold="1" ss:Italic="1" ss:Size="16"/>
							</Style>
							<Style ss:ID="Heading1" ss:Name="Heading1">
								<Alignment ss:Horizontal="Center" ss:Rotate="90"/>
								<Font ss:Bold="1" ss:Italic="1" ss:Size="16"/>
							</Style>
							<Style ss:ID="co1"/>
							<Style ss:ID="ta1"/>
						</Styles>
						<ss:Worksheet ss:Name="'. $this->name . '">
							<Table ss:StyleID="ta1">
								<Column ss:Span="2" ss:Width="64.2614"/>
								<Row ss:Height="13.2945">';
		foreach($this->headers as $header) {
			$out .= 			'<Cell  ss:StyleID="Head"><Data ss:Type="String">' . $header . '</Data></Cell>';
		}
		$out .= 			'</Row>';

		foreach($this->data as $row) {
			$out .= 		'<Row ss:Height="20">';

			foreach($row as $data) {
				$type = in_array(gettype($data), $this->numeralTypes) ? "Number" : "String";

				$out .= 		'<Cell><Data ss:Type="' . $type . '">' . $data . '</Data></Cell>';
			}

			$out .= 		'</Row>';
		}

		$out .= 		'</Table>
							<x:WorksheetOptions/>
						</ss:Worksheet>
					</Workbook>';

		return $out;
	}

}

?>
