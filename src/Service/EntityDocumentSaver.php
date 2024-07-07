<?php

namespace WikiConnect\WikibaseApi\Service;

use WikiConnect\MediawikiApi\DataModel\Content;
use WikiConnect\MediawikiApi\DataModel\EditInfo;
use WikiConnect\MediawikiApi\DataModel\Revision;
use Wikibase\DataModel\Entity\EntityDocument;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\Property;

/**
 * @access private
 */
class EntityDocumentSaver {

	private RevisionSaver $revisionSaver;

	public function __construct( RevisionSaver $revisionSaver ) {
		$this->revisionSaver = $revisionSaver;
	}

	/**
	 *
	 * @return Item|Property
	 */
	public function save( EntityDocument $entityDocument, EditInfo $editInfo ) {
		return $this->revisionSaver->save( new Revision( new Content( $entityDocument ) ), $editInfo );
	}

}
