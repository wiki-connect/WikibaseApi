<?php

namespace WikiConnect\WikibaseApi\Service;

use WikiConnect\MediawikiApi\Client\Action\ActionApi;
use WikiConnect\MediawikiApi\Client\Action\Request\ActionRequest;
use WikiConnect\MediawikiApi\DataModel\Content;
use WikiConnect\MediawikiApi\DataModel\PageIdentifier;
use WikiConnect\MediawikiApi\DataModel\Revision;
use WikiConnect\WikibaseApi\DataModel\ItemContent;
use WikiConnect\WikibaseApi\DataModel\PropertyContent;
use Deserializers\Deserializer;
use RuntimeException;
use Wikibase\DataModel\Entity\EntityId;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\Property;
use Wikibase\DataModel\SiteLink;

/**
 * @access private
 */
/**
 * A service for getting revisions of entities in Wikibase.
 *
 * @access private
 */
class RevisionGetter {

	/**
	 * @var ActionApi
	 */
	protected $api;

	/**
	 * @var Deserializer
	 */
	protected $entityDeserializer;

	/**
	 * @param ActionApi $api
	 * @param Deserializer $entityDeserializer
	 */
	public function __construct( ActionApi $api, Deserializer $entityDeserializer ) {
		$this->api = $api;
		$this->entityDeserializer = $entityDeserializer;
	}

	/**
	 * @param string|EntityId $id
	 *
	 * @return Revision|null
	 */
	public function getFromId( $id ): ?Revision {
		return $this->getFromWikibaseEntityId( $id );
	}

	/**
	 * @param SiteLink $siteLink
	 *
	 * @return Revision|null
	 */
	public function getFromSiteLink( SiteLink $siteLink ): ?Revision {
		return $this->getFromWikibaseEntityId( $siteLink );
	}

	/**
	 * @param string $siteId
	 * @param string $title
	 *
	 * @return Revision|null
	 */
	public function getFromSiteAndTitle( string $siteId, string $title ): ?Revision {
		return $this->getFromWikibaseEntityId( $siteId . ':' . $title );
	}

	/**
	 * @param string|EntityId|SiteLink $id
	 *
	 * @return Revision|null
	 */
	private function getFromWikibaseEntityId( $id ): ?Revision {
		$result = $this->api->request( ActionRequest::simpleGet(
			'wbgetentities',
			[ 'ids' => $this->getSerializedId( $id ) ]
		) );

		return $this->newRevisionFromResult( array_shift( $result['entities'] ) );
	}

	/**
	 * @param string|EntityId|SiteLink $id
	 *
	 * @return string
	 */
	private function getSerializedId( $id ): string {
		if ( $id instanceof EntityId ) {
			return $id->getSerialization();
		}

		if ( $id instanceof SiteLink ) {
			return $id->getSiteId() . ':' . $id->getPageName();
		}

		return $id;
	}

	/**
	 * @param array $entityResult
	 *
	 * @return Revision|null
	 */
	private function newRevisionFromResult( array $entityResult ): ?Revision {
		if ( array_key_exists( 'missing', $entityResult ) ) {
			return null;
		}

		return new Revision(
			$this->getContentFromEntity( $this->entityDeserializer->deserialize( $entityResult ) ),
			new PageIdentifier( null, (int)$entityResult['pageid'] ),
			(int)$entityResult['lastrevid'],
			null,
			null,
			$entityResult['modified']
		);
	}

	/**
	 * @param Item|Property $entity
	 *
	 * @throws RuntimeException
	 * @return ItemContent|PropertyContent|Content
	 */
	private function getContentFromEntity( $entity ) {
		switch ( $entity->getType() ) {
			case Item::ENTITY_TYPE:
				return new ItemContent( $entity );
			case Property::ENTITY_TYPE:
				return new PropertyContent( $entity );
			default:
				return new Content( $entity, 'unknown-wikibase-entity-content-' . $entity->getType() );
		}
	}

}
