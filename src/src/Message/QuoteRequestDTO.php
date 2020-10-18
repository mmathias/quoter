<?php


namespace App\Message;


class QuoteRequestDTO
{
    /**
     * @var string
     */
    private $author;
    /**
     * @var int
     */
    private $limit;

    /**
     * QuoteRequestDTO constructor.
     * @param string $author
     * @param int $limit
     */
    public function __construct(string $author, int $limit)
    {
        $this->author = $author;
        $this->limit = $limit;
    }

    /**
     * @return string
     */
    public function getAuthor(): string
    {
        return $this->author;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }
}
