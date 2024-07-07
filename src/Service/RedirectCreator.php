<?php

namespace WikiConnect\WikibaseApi\Service;

use WikiConnect\MediawikiApi\DataModel\EditInfo;
use WikiConnect\WikibaseApi\WikibaseApi;
use Wikibase\DataModel\Entity\EntityId;

/**
 * @access private
 */
class RedirectCreator {

	private WikibaseApi $api;

	public function __construct( WikibaseApi $api ) {
		$this->api = $api;
	}

	/**
	 * @param EditInfo|null $editInfo
	 */
	public function create( EntityId $from, EntityId $to, EditInfo $editInfo = null ): bool {
		$params = [
			'from' => $from->__toString(),
			'to' => $to->__toString(),
		];

		$this->api->postRequest( 'wbcreateredirect', $params, $editInfo );
		return true;
	}

}
