<?php

namespace WikiConnect\WikibaseApi\Service;

use WikiConnect\MediawikiApi\DataModel\EditInfo;
use WikiConnect\WikibaseApi\WikibaseApi;
use Wikibase\DataModel\SiteLink;

/**
 * @access private
 */
class SiteLinkLinker {

	private WikibaseApi $api;

	public function __construct( WikibaseApi $api ) {
		$this->api = $api;
	}

	/**
	 * @param EditInfo|null $editInfo
	 */
	public function link( SiteLink $toSiteLink, SiteLink $fromSiteLink, EditInfo $editInfo = null ): bool {
		$params = [
			'tosite' => $toSiteLink->getSiteId(),
			'totitle' => $toSiteLink->getPageName(),
			'fromsite' => $fromSiteLink->getSiteId(),
			'fromtitle' => $fromSiteLink->getPageName(),
		];

		$this->api->postRequest( 'wblinktitles', $params, $editInfo );
		return true;
	}

}
