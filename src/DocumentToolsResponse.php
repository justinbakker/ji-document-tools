<?php

/**
 * Class DocumentToolsResponse
 */
class DocumentToolsResponse
{
    public int $statusCode;
    public string $body;

    /**
     * Constructor for creating a new instance of the class.
     *
     * @param int $statusCode The status code to be set for the instance.
     * @param string $body The body content to be assigned to the instance.
     * @return void
     */
    public function __construct(int $statusCode, string $body){
        $this->statusCode = $statusCode;
        $this->body = $body;
    }

    /**
     * Determines if the content of the property is a valid JSON format.
     *
     * @return bool Returns true if the content is valid JSON, otherwise false.
     */
    public function isJson(): bool
    {
        return (json_decode($this->body) !== false);
    }

    /**
     * Parses the body content to decode it from JSON format.
     *
     * @return mixed The decoded JSON data or null if the parsing fails.
     */
    public function parseBody(): mixed
    {
        return json_decode($this->body);
    }
}