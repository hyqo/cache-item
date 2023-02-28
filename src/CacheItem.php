<?php

namespace Hyqo\Cache;

use Hyqo\Cache\Contract\ItemInterface;

class CacheItem implements ItemInterface
{
    protected mixed $value = null;

    protected array $tags = [];

    protected ?int $expiresAt = null;
    protected ?int $expiresAfter = null;

    public function __construct(
        protected string $key,
        protected bool $hit,
    ) {
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function isHit(): bool
    {
        return $this->hit;
    }

    public function get(): mixed
    {
        return $this->value;
    }

    public function set(mixed $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getExpiresAt(): ?int
    {
        return $this->expiresAt;
    }

    public function getExpiresAfter(): ?int
    {
        return $this->expiresAfter;
    }

    protected function setEternal(): static
    {
        $this->expiresAt = null;
        $this->expiresAfter = null;

        return $this;
    }

    public function expiresAt(?int $timestamp): static
    {
        if (null === $timestamp) {
            return $this->setEternal();
        }

        $this->expiresAt = $timestamp;
        $this->expiresAfter = $timestamp ? $timestamp - time() : 0;

        return $this;
    }

    public function expiresAfter(?int $seconds): static
    {
        if (null === $seconds) {
            return $this->setEternal();
        }

        $this->expiresAt = $seconds ? time() + $seconds : 0;
        $this->expiresAfter = $seconds;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * @param string|string[] $tag
     * @return $this
     */
    public function tag(string|array $tag): static
    {
        if (is_string($tag)) {
            $tags = [$tag];
        } else {
            $tags = array_values($tag);
        }

        $this->tags = array_values(array_unique([...$this->tags, ...$tags]));

        return $this;
    }
}
