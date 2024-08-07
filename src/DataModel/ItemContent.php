<?php

namespace WikiConnect\WikibaseApi\DataModel;

use WikiConnect\MediawikiApi\DataModel\Content;
use Wikibase\DataModel\Entity\Item;

class ItemContent extends Content {

	/**
	 * @var string
	 */
	public const MODEL = 'wikibase-item';

	public function __construct( Item $item ) {
		parent::__construct( $item, self::MODEL );
	}

	/**
	 * @required
	 * @see Content::getData
	 */
	public function getData(): Item {
		return parent::getData();
	}
}
