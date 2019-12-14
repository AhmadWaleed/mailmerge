<?php

namespace MailMerge;

abstract class BaseMailLog implements MailLog
{
    protected array $normalizedResponse;

    protected array $originalResponse = [];

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return (array) [
            'normalized_response' => $this->normalizedResponse,
            'original_response' => (array) $this->originalResponse,
        ];
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode([
            'normalized_response' => $this->normalizedResponse,
            'original_response' => $this->originalResponse,
        ], $options);
    }
}