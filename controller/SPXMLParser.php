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
	 * @param $nodePath string
	 * @return DOMElement[]
	 */
	public function searchForNodesByName($nodePath) {
		$foundNodes = array();
		$usualXML = $this->xPath->query("//" . $nodePath);

		/* Searches in the ordinary xml */
		foreach($usualXML as $node) {
			array_push($foundNodes, $node);
		}

		/* Searches in the commented xml */
		$commentedPlainXML = $this->getCommentedXML();
		$commentDom = new DOMDocument();
		$commentDom->loadXML($commentedPlainXML);
		$commentedXPath = new DOMXPath($commentDom);
		$commentedXML = $commentedXPath->query("//" . $nodePath);

		foreach($commentedXML as $commentNode) {
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
		$fullCommentedXML = "";

		foreach($commentedNodes as $commented) {
			$c = $commented->parentNode; // I didn't find a better variable name...
			$commentedXML = $commented->textContent;

			while(!is_null($c->tagName)) {
				$parentNodeStr = $c->tagName;
				$commentedXML = "<" . $parentNodeStr . ">" . $commentedXML . "</" . $parentNodeStr . ">";
				$c = $c->parentNode;
			}

			$fullCommentedXML .= $commentedXML;
		}

		return $fullCommentedXML;
	}
}