<?php

namespace WikiConnect\WikibaseApi\Service;

use WikiConnect\MediawikiApi\DataModel\EditInfo;
use WikiConnect\WikibaseApi\WikibaseApi;
use UnexpectedValueException;
use Wikibase\DataModel\Entity\EntityId;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\Property;
use Wikibase\DataModel\SiteLink;

/**
 * @access private
 */
class SiteLinkSetter {

	private WikibaseApi $api;

	public function __construct( WikibaseApi $api ) {
		$this->api = $api;
	}

	/**
	 * @param EntityId|Item|Property|SiteLink $target
	 * @param EditInfo|null $editInfo
	 */
	public function set( SiteLink $siteLink, $target, EditInfo $editInfo = null ): bool {
		$this->throwExceptionsOnBadTarget( $target );

		$params = $this->getTargetParamsFromTarget(
			$this->getEntityIdentifierFromTarget( $target )
		);

		$params['linksite'] = $siteLink->getSiteId();
		$params['linktitle'] = $siteLink->getPageName();
		$params['badges'] = implode( '|', $siteLink->getBadges() );

		$this->api->postRequest( 'wbsetsitelink', $params, $editInfo );
		return true;
	}

	/**
	 * @param mixed $target
	 *
	 * @throws UnexpectedValueException
	 *
	 * @todo Fix duplicated code
	 */
	private function throwExceptionsOnBadTarget( $target ): void {
		if ( !$target instanceof EntityId && !$target instanceof Item && !$target instanceof Property && !$target instanceof SiteLink ) {
			throw new UnexpectedValueException( '$target needs to be an EntityId, Item, Property or SiteLink' );
		}

		if ( ( $target instanceof Item || $target instanceof Property ) && $target->getId() === null ) {
			throw new UnexpectedValueException( '$target Entity object needs to have an Id set' );
		}
	}

	/**
	 * @param EntityId|Item|Property $target
	 *
	 * @throws UnexpectedValueException
	 * @return EntityId|SiteLink
	 *
	 * @todo Fix duplicated code
	 */
	private function getEntityIdentifierFromTarget( $target ) {
		if ( $target instanceof Item || $target instanceof Property ) {
			return $target->getId();
		} else {
			return $target;
		}
	}

	/**
	 * @param EntityId|SiteLink $target
	 *
	 * @throws UnexpectedValueException
	 * @return array
	 *
	 * @todo Fix duplicated code
	 */
	private function getTargetParamsFromTarget( $target ) {
		if ( $target instanceof EntityId ) {
			return [ 'id' => $target->getSerialization() ];
		} elseif ( $target instanceof SiteLink ) {
			return [
				'site' => $target->getSiteId(),
				'title' => $target->getPageName(),
			];
		} else {
			throw new UnexpectedValueException( '$target needs to be an EntityId or SiteLink' );
		}
	}
}
