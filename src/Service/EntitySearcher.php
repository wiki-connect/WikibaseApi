<?php

namespace WikiConnect\WikibaseApi\Service;

use WikiConnect\MediawikiApi\Client\Action\ActionApi;
use WikiConnect\MediawikiApi\Client\Action\Request\ActionRequest;

/**
 * @access private
 */
class EntitySearcher {

	private ActionApi $api;

	public function __construct( ActionApi $api ) {
		$this->api = $api;
	}

	/**
	 * @return string[] EntityIds
	 */
	public function search( string $entityType, string $string, string $language ): array {
		$params = [
			'search' => $string,
			'language' => $language,
			'type' => $entityType,
		];

		$data = $this->api->request( ActionRequest::simpleGet( 'wbsearchentities', $params ) );

		$ids = [];
		foreach ( $data['search'] as $searchResult ) {
			$ids[] = $searchResult['id'];
		}

		return $ids;
	}

}
