<?php

namespace WikiConnect\WikibaseApi;

use WikiConnect\MediawikiApi\Client\Action\ActionApi;
use WikiConnect\WikibaseApi\Lookup\EntityApiLookup;
use WikiConnect\WikibaseApi\Lookup\ItemApiLookup;
use WikiConnect\WikibaseApi\Lookup\PropertyApiLookup;
use WikiConnect\WikibaseApi\Service\AliasGroupSetter;
use WikiConnect\WikibaseApi\Service\BadgeIdsGetter;
use WikiConnect\WikibaseApi\Service\DescriptionSetter;
use WikiConnect\WikibaseApi\Service\EntityDocumentSaver;
use WikiConnect\WikibaseApi\Service\EntitySearcher;
use WikiConnect\WikibaseApi\Service\ItemMerger;
use WikiConnect\WikibaseApi\Service\LabelSetter;
use WikiConnect\WikibaseApi\Service\RedirectCreator;
use WikiConnect\WikibaseApi\Service\ReferenceRemover;
use WikiConnect\WikibaseApi\Service\ReferenceSetter;
use WikiConnect\WikibaseApi\Service\RevisionGetter;
use WikiConnect\WikibaseApi\Service\RevisionSaver;
use WikiConnect\WikibaseApi\Service\RevisionsGetter;
use WikiConnect\WikibaseApi\Service\SiteLinkLinker;
use WikiConnect\WikibaseApi\Service\SiteLinkSetter;
use WikiConnect\WikibaseApi\Service\StatementCreator;
use WikiConnect\WikibaseApi\Service\StatementGetter;
use WikiConnect\WikibaseApi\Service\StatementRemover;
use WikiConnect\WikibaseApi\Service\StatementSetter;
use WikiConnect\WikibaseApi\Service\ValueFormatter;
use WikiConnect\WikibaseApi\Service\ValueParser;
use WikiConnect\WikibaseApi\DataModel\DataModelFactory;
use Wikibase\DataModel\Services\Lookup\EntityRetrievingTermLookup;

/**
 * @access public
 */
class WikibaseFactory {

	private ActionApi $api;

	private DataModelFactory $datamodelFactory;

	public function __construct( ActionApi $api, $datamodelFactory ) {
		$this->api = $api;

		if ( $datamodelFactory instanceof DataModelFactory ) {
			$this->datamodelFactory = $datamodelFactory;
		} else {
			// Back compact from older constructor signature
			// ( ActionApi $api, Deserializer $dvDeserializer, Serializer $dvSerializer )
			$arg_list = func_get_args();
			$this->datamodelFactory = new DataModelFactory(
				$arg_list[1],
				$arg_list[2]
			);
		}
	}

	public function newRevisionSaver(): RevisionSaver {
		return new RevisionSaver(
			$this->newWikibaseApi(),
			$this->datamodelFactory->newEntityDeserializer(),
			$this->datamodelFactory->newEntitySerializer()
		);
	}

	public function newRevisionGetter(): RevisionGetter {
		return new RevisionGetter(
			$this->api,
			$this->datamodelFactory->newEntityDeserializer()
		);
	}

	public function newRevisionsGetter(): RevisionsGetter {
		return new RevisionsGetter(
			$this->api,
			$this->datamodelFactory->newEntityDeserializer()
		);
	}

	public function newValueParser(): ValueParser {
		return new ValueParser(
			$this->api,
			$this->datamodelFactory->getDataValueDeserializer()
		);
	}

	public function newValueFormatter(): ValueFormatter {
		return new ValueFormatter(
			$this->api,
			$this->datamodelFactory->getDataValueSerializer()
		);
	}

	public function newItemMerger(): ItemMerger {
		return new ItemMerger( $this->newWikibaseApi() );
	}

	public function newAliasGroupSetter(): AliasGroupSetter {
		return new AliasGroupSetter( $this->newWikibaseApi() );
	}

	public function newDescriptionSetter(): DescriptionSetter {
		return new DescriptionSetter( $this->newWikibaseApi() );
	}

	public function newLabelSetter(): LabelSetter {
		return new LabelSetter( $this->newWikibaseApi() );
	}

	public function newReferenceRemover(): ReferenceRemover {
		return new ReferenceRemover( $this->newWikibaseApi() );
	}

	public function newReferenceSetter(): ReferenceSetter {
		return new ReferenceSetter(
			$this->newWikibaseApi(),
			$this->datamodelFactory->newReferenceSerializer()
		);
	}

	public function newSiteLinkLinker(): SiteLinkLinker {
		return new SiteLinkLinker( $this->newWikibaseApi() );
	}

	public function newSiteLinkSetter(): SiteLinkSetter {
		return new SiteLinkSetter( $this->newWikibaseApi() );
	}

	public function newBadgeIdsGetter(): BadgeIdsGetter {
		return new BadgeIdsGetter( $this->api );
	}

	public function newRedirectCreator(): RedirectCreator {
		return new RedirectCreator( $this->newWikibaseApi() );
	}

	public function newStatementGetter(): StatementGetter {
		return new StatementGetter(
			$this->api,
			$this->datamodelFactory->newStatementDeserializer()
		);
	}

	public function newStatementSetter(): StatementSetter {
		return new StatementSetter(
			$this->newWikibaseApi(),
			$this->datamodelFactory->newStatementSerializer()
		);
	}

	public function newStatementCreator(): StatementCreator {
		return new StatementCreator(
			$this->newWikibaseApi(),
			$this->datamodelFactory->getDataValueSerializer()
		);
	}

	public function newStatementRemover(): StatementRemover {
		return new StatementRemover( $this->newWikibaseApi() );
	}

	private function newWikibaseApi(): WikibaseApi {
		return new WikibaseApi( $this->api );
	}

	public function newEntityLookup(): EntityApiLookup {
		return new EntityApiLookup( $this->newRevisionGetter() );
	}

	public function newItemLookup(): ItemApiLookup {
		return new ItemApiLookup( $this->newEntityLookup() );
	}

	public function newPropertyLookup(): PropertyApiLookup {
		return new PropertyApiLookup( $this->newEntityLookup() );
	}

	public function newTermLookup(): EntityRetrievingTermLookup {
		return new EntityRetrievingTermLookup( $this->newEntityLookup() );
	}

	public function newEntityDocumentSaver(): EntityDocumentSaver {
		return new EntityDocumentSaver( $this->newRevisionSaver() );
	}

	public function newEntitySearcher(): EntitySearcher {
		return new EntitySearcher( $this->api );
	}

}
