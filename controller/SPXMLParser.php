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
	 *
	 * @param $nodeName string
	 *
	 * @return DOMElement[]
	 */
	public function searchForNodesByName($nodeName) {
		return $this->searchForElements("//" . $nodeName);
	}

	/**
	 * Searches in the entire xml file for the attribute name (ignores comments)
	 *
	 * @param $attributeName
	 *
	 * @return DOMElement[]
	 */
	public function searchForNodesByAttribute($attributeName) {
		return $this->searchForElements("//*[@" . $attributeName . "]");
	}

	/**
	 * Searches in the entire xml file for something queried name (ignores comments)
	 *
	 * @param $query
	 *
	 * @return DOMElement[]
	 */
	private function searchForElements($query) {
		$foundNodes = array();
		$usualXML = $this->xPath->query($query);
		foreach($usualXML as $node) {
			array_push($foundNodes, $node);
		}

		$commentDom = new DOMDocument();
		$commentDom->loadXML($this->getCommentedXML());
		$commentedXPath = new DOMXPath($commentDom);

		foreach($commentedXPath->query($query) as $commentNode) {
			array_push($foundNodes, $commentNode);
		}

		return $foundNodes;
	}

	/**
	 * Returns a string that contains all commented XML in condition that it is valid
	 *
	 * @return string
	 */
	public function getCommentedXML() {
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

			if(SPXMLParser::validXML($commentedXML)) {
				$fullCommentedXML .= $commentedXML;
			}
		}

		return $fullCommentedXML;
	}

	/**
	 * Determines if XML is valid or not
	 *
	 * @param $xml
	 *
	 * @return bool
	 */
	public static function validXML($xml) {
		if(simplexml_load_string($xml)) {
			return true;
		}

		return false;
	}
}