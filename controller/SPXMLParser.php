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
	public function searchForNodesByName($nodeName) {
		$foundNodes = array();
		$usualXML = $this->xPath->query("//" . $nodeName);

		/* Searches in the ordinary xml */
		foreach($usualXML as $node) {
			array_push($foundNodes, $node);
		}

		/* Searches in the commented xml */
		$commentedXML = $this->getCommentedXML();
		$commentDom = new DOMDocument();
		$commentDom->loadXML($commentedXML);

		foreach($commentDom->getElementsByTagName($nodeName) as $commentNode) {
			array_push($foundNodes, $commentNode);
		}

		return $foundNodes;
	}

	public function searchForNodesByAttribute($attributeName) {
		$attributeQuery = "//*[@" . $attributeName . "]";
		$foundNodes = array();
		$usualXML = $this->xPath->query($attributeQuery);

		foreach($usualXML as $node) {
			array_push($foundNodes, $node);
		}

		$commentedXML = $this->getCommentedXML();
		$commentDom = new DOMDocument();
		$commentDom->loadXML($commentedXML);
		$attributeXPath = new DOMXPath($commentDom);

		foreach($attributeXPath->query($attributeQuery) as $commentNode) {
			array_push($foundNodes, $commentNode);
		}

		return $foundNodes;
	}

	private function getCommentedXML() {
		$commentedNodes = $this->xPath->query("//comment()");
		$commentedXML = "";

		foreach($commentedNodes as $commented) {
			$commentedXML .= $commented->nodeValue;
		}

		$commentedXML = "<spwrap>" . $commentedXML . "</spwrap>";

		return $commentedXML;
	}
}