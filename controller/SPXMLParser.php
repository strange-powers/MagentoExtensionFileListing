<?php

class SPXMLParser {
	private $xPath;

	/**
	 * @param string $xmlFile
	 */
	public function load($xmlFile) {
		$dom = new DOMDocument();
		$dom->load($xmlFile);
		$this->xPath = new DOMXPath($dom);
	}

	/**
	 * Searches in the entire xml file for the node name (ignores comments)
	 * @param $nodeName string
	 * @return DOMNode[]
	 */
	public function searchForNodeName($nodeName) {
		$foundNodes = array();
		$commentedXML = $this->xPath->query("//comment()");
		$usualXML = $this->xPath->query("//" . $nodeName);

		/* Searches in the ordinary xml */
		foreach($usualXML as $node) {
			array_push($foundNodes, $node);
		}

		/* Searches in the commented xml */
		$commentDom = new DOMDocument();
		foreach($commentedXML as $comment) {
			$commentXML = "<spwrap>" . $comment->nodeValue . "</spwrap>";
			$commentDom->loadXML($commentXML);
			foreach($commentDom->getElementsByTagName($nodeName) as $commentNode) {
				array_push($foundNodes, $commentNode);
			}
		}

		return $foundNodes;
	}
}