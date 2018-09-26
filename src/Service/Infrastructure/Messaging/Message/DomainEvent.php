<?php
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 9/9/18
 * Time: 9:29 PM
 */

namespace Service\Infrastructure\Messaging\Message;

use Service\Infrastructure\Messaging\Message\Base\Message;
use Assert\Assertion;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class DomainEvent extends Message
{
    /**
     * DomainEvent constructor.
     *
     * @param string $aggregateName
     * @param string $aggregateId
     * @param array $payload
     * @param array $metadata
     *
     * @throws \Assert\AssertionFailedException
     */
    protected function __construct(
        string $aggregateName,
        string $aggregateId,
        array $payload,
        array $metadata = []
    ) {
        $this->metadata = $metadata;
        $this->setAggregateId($aggregateId);
        $this->setAggregateName($aggregateName);
        $this->setVersion($metadata['_aggregate_version'] ?? 1);
        $this->setPayload($payload);
        $this->init();
    }

    /**
     * @param string $aggregateId
     * @throws \Assert\AssertionFailedException
     */
    protected function setAggregateId(string $aggregateId): void
    {
        Assertion::notEmpty($aggregateId);
        $this->metadata['_aggregate_id'] = $aggregateId;
    }

    /**
     * @param string $aggregateName
     * @throws \Assert\AssertionFailedException
     */
    protected function setAggregateName(string $aggregateName): void
    {
        Assertion::notEmpty($aggregateName);
        $this->metadata['_aggregate_name'] = $aggregateName;
    }

    /**
     * @param int $version
     */
    protected function setVersion(int $version): void
    {
        $this->metadata['_aggregate_version'] = $version;
    }

    /**
     * @inheritdoc
     */
    public function messageType(): string
    {
        return self::TYPE_EVENT;
    }

    /**
     * @param array $metaData
     * @return DomainEvent
     */
    public function withMetaData(array $metaData): DomainEvent
    {
        $this->metadata = array_merge($this->metadata, $metaData);
        return $this;
    }

    public function aggregateId(): string
    {
        return $this->metadata['_aggregate_id'];
    }

    public function aggregateName(): string
    {
        return $this->metadata['_aggregate_name'];
    }
}
