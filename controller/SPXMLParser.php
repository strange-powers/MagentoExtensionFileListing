<?php

class SPXMLParser {
	private $dom;
	private $xPath;
	private $xmlFile;

	public function __construct($file) {
		$this->dom = new DOMDocument();
		$this->xPath = new DOMXPath();
		$this->setXmlFile($file);
	}

	/**
	 * @param string $xmlFile
	 */
	public function setXmlFile( $xmlFile ) {
		$this->xmlFile = $xmlFile;
		$this->dom->load($this->xmlFile);
		$this->xPath->document = $this->dom;
	}

	/**
	 * Searches in the entire xml file for the node name (ignores comments)
	 * @param $nodeName string
	 * @return string[]
	 */
	public function searchForNodeName($nodeName) {
		$foundNodes = array();
		$commentedXML = $this->xPath->query("//comment()");

		/* Searches in the ordinary xml */
		foreach($this->dom->getElementsByTagName($nodeName) as $node) {
			array_push($foundNodes, $node);
		}

		/* Searches in the commented xml */
		foreach($commentedXML as $comment) {
			$commentXML = "<spwrap>" . $comment->nodeValue . "</spwrap>";
			$this->loadXML($commentXML);
			foreach($this->getElementsByTagName($nodeName) as $commentNode) {
				array_push($foundNodes, $commentNode);
			}
		}

		return $foundNodes;
	}
}