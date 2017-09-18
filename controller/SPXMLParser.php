<?php
/**
 * Copyright (c) 2017. Julian Vöst <jv@strange-powers.com>
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * See <http://www.gnu.org/licenses/>.
 */

class SPXMLParser {
	private $xPath;

	/**
	 * Loads the XML File and makes it ready for use
	 *
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
	 * @param $attributeName string
	 *
	 * @return DOMElement[]
	 */
	public function searchForNodesByAttribute($attributeName) {
		return $this->searchForElements("//*[@" . $attributeName . "]");
	}

	/**
	 * Searches in the entire xml file (ignores comments)
	 *
	 * @param $query string
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
	 * Returns a string that contains alL commented XML
	 * in condition that it is valid
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

			if(self::validXML($commentedXML)) {
				$fullCommentedXML .= $commentedXML;
			}
		}

		return $fullCommentedXML;
	}

	/**
	 * Determines if XML is valid or not
	 *
	 * @param $xml string
	 *
	 * @return bool
	 */
	public static function validXML($xml) {
		if(simplexml_load_string($xml)) {
			return true;
		}

		return false;
	}

	/**
	 * @param $nodes DOMElement[]
	 * @param $attributeName string
	 *
	 * @return string[]
	 */
	public static function getAttributeContentFromNodes($nodes, $attributeName) {
		$output = self::getContentFromNodes($nodes, function($node) use ($attributeName) {
			return $node->getAttribute($attributeName);
		});

		return $output;
	}

	/**
	 * Returns the text contents of node elements if they
	 * are not empty or null
	 *
	 * @param $nodes DOMElement[]
	 *
	 * @return string[]
	 */
	public static function getTextContentFromNodes($nodes) {
		$output = self::getContentFromNodes($nodes, function($node) {
			return $node->textContent;
		});

		return $output;
	}

	/**
	 * Returns content of lamdba value
	 *
	 * @param $nodes DOMElement[]
	 * @param $lambda Closure
	 *
	 * @return string[]
	 */
	private static function getContentFromNodes($nodes, $lambda) {
		$content = array();

		foreach($nodes as $node) {
			$contentFromNode = $lambda($node);
			if(!is_null($contentFromNode) && strlen($contentFromNode) > 0) {
				array_push($content, $contentFromNode);
			}
		}

		return $content;
	}
}