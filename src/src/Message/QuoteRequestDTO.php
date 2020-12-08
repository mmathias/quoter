<?php


namespace App\Message;


class QuoteRequestDTO
{
    /**
     * @var string
     */
    private $authorName;
    /**
     * @var array
     */
    private $quotes;

    /**
     * QuoteRequestDTO constructor.
     * @param string $authorName
     * @param array $quotes
     */
    public function __construct(string $authorName, array $quotes)
    {
        $this->authorName = $authorName;
        $this->quotes = $quotes;
    }

    /**
     * @return string
     */
    public function getAuthorName(): string
    {
        return $this->authorName;
    }

    /**
     * @return array
     */
    public function getQuotes(): array
    {
        return $this->quotes;
    }

}
