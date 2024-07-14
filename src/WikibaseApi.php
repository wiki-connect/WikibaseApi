<?php

namespace WikiConnect\WikibaseApi;

use WikiConnect\MediawikiApi\Client\Action\ActionApi;
use WikiConnect\MediawikiApi\Client\Action\Request\ActionRequest;
use WikiConnect\MediawikiApi\DataModel\EditInfo;

class WikibaseApi {

	/**
	 * @var ActionApi
	 */
	private $api;

	/**
	 * @param ActionApi $api
	 */
	public function __construct( ActionApi $api ) {
		$this->api = $api;
	}

	/**
	 * Makes a POST request to the given action.
	 *
	 * @param string $action
	 * @param array $params
	 * @param EditInfo|null $editInfo
	 *
	 * @return mixed
	 */
	public function postRequest( string $action, array $params, EditInfo $editInfo = null ) {
		if ( $editInfo !== null ) {
			$params = array_merge( $params, $this->getEditInfoParams( $editInfo ) );
		}

		$params['token'] = $this->api->getToken();

		return $this->api->request( ActionRequest::simplePost( $action, $params ) );
	}

	/**
	 * Converts EditInfo to a set of parameters that can be passed to the API.
	 *
	 * @param EditInfo|null $editInfo
	 *
	 * @return array
	 */
	private function getEditInfoParams( ?EditInfo $editInfo ): array {
		$params = [];

		if ( $editInfo !== null ) {
			if ( $editInfo->getSummary() !== '' ) {
				$params['summary'] = $editInfo->getSummary();
			}

			if ( $editInfo->getMinor() ) {
				$params['minor'] = true;
			}

			if ( $editInfo->getBot() ) {
				$params['bot'] = true;
				$params['assert'] = 'bot';
			}

			if ( $editInfo->getMaxlag() ) {
				$params['maxlag'] = $editInfo->getMaxlag();
			}
		}

		return $params;
	}

}
