<?php

namespace WikiConnect\WikibaseApi\Service;

use WikiConnect\MediawikiApi\Client\Action\ActionApi;
use WikiConnect\MediawikiApi\Client\Action\Request\ActionRequest;
use Wikibase\DataModel\Entity\ItemId;

/**
 * @access private
 */
class BadgeIdsGetter {

	private ActionApi $api;

	public function __construct( ActionApi $api ) {
		$this->api = $api;
	}

	/**
	 * @return ItemId[]
	 */
	public function get(): array {
		$result = $this->api->request( ActionRequest::simpleGet( 'wbavailablebadges' ) );
		$ids = [];
		foreach ( $result['badges'] as $badgeIdString ) {
			$ids[] = new ItemId( $badgeIdString );
		}

		return $ids;
	}

}
