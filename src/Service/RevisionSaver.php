<?php

namespace WikiConnect\WikibaseApi\Service;

use WikiConnect\MediawikiApi\DataModel\EditInfo;
use WikiConnect\MediawikiApi\DataModel\Revision;
use WikiConnect\WikibaseApi\WikibaseApi;
use Deserializers\Deserializer;
use InvalidArgumentException;
use RuntimeException;
use Serializers\Serializer;
use Wikibase\DataModel\Entity\EntityDocument;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\Property;

/**
 * @access private
 */
class RevisionSaver {

	protected WikibaseApi $api;

	private Deserializer $entityDeserializer;

	private Serializer $entitySerializer;

	public function __construct( WikibaseApi $api, Deserializer $entityDeserializer, Serializer $entitySerializer ) {
		$this->api = $api;
		$this->entityDeserializer = $entityDeserializer;
		$this->entitySerializer = $entitySerializer;
	}

	/**
	 * @param EditInfo|null $editInfo
	 *
	 * @throws RuntimeException if Content is not an EntityDocument
	 * @throws InvalidArgumentException if $editInfo is null and the revision does not have an EditInfo
	 * @return Item|Property new version of the entity
	 */
	public function save( Revision $revision, EditInfo $editInfo = null ): object {
		$content = $revision->getContent();
		$data = $content->getData();
		if (!$data instanceof EntityDocument) {
			throw new RuntimeException('Can only save Content of EntityDocuments');
		}

		$entity = $data;
		$serialized = $this->entitySerializer->serialize($entity);

		$params = [
			'data' => json_encode($serialized)
		];

		$revId = $revision->getId();
		if ($revId !== null) {
			$params['baserevid'] = $revId;
		}

		$entityId = $entity->getId();
		if ($entityId !== null) {
			$params['id'] = $entityId->getSerialization();

			// If we are provided an empty entity, then set the clear flag
			if ($entity->isEmpty()) {
				$params['clear'] = true;
			}
		} else {
			$params['new'] = $entity->getType();
		}

		// If no editInfo is explicitly passed call back to the one in the revision?
		if ($editInfo === null && $revision->getEditInfo() === null) {
			throw new InvalidArgumentException('No EditInfo provided and the revision does not have one');
		}

		$editInfo = $editInfo ?? $revision->getEditInfo();
		$result = $this->api->postRequest('wbeditentity', $params, $editInfo);
		return $this->entityDeserializer->deserialize($result['entity']);
	}

}
