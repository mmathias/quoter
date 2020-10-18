<?php


namespace App\Resources;

use App\Repository\AuthorRepository;
use Doctrine\Common\Collections\Collection;

class QuoteFinder
{
    private const MAX_LIMIT = 10;
    private const MIN_LIMIT = 1;

    /**
     * @var AuthorRepository
     */
    private $authorRepository;

    public function __construct(AuthorRepository $authorRepository) {
        $this->authorRepository = $authorRepository;
    }

    public function findShoutedQuotesByActorWithLimit(string $author, int $limit): array
    {
        $this->validateLimit($limit);
        $quotes = $this->getQuotes($author);

        if ($quotes->isEmpty()) {
            return [];
        }

        return $this->getShoutedQuotes($quotes->slice(0, $limit));
    }

    /**
     * @param string $name
     * @return Collection
     */
    private function getQuotes(string $name): Collection
    {
        $author = $this->authorRepository->findOneBy(["name" => $name]);

        if (!$author) {
            throw new AuthorNotFoundException('Author not found.');
        }

        return $author->getQuotes();
    }

    /**
     * @param array $quotes
     * @return array
     */
    private function getShoutedQuotes(array $quotes): array
    {
        $shoutedQuotes = [];
        foreach ($quotes as $quote) {
            $shoutedQuotes[] = strtoupper($quote->getQuote()) . '!';
        }

        return $shoutedQuotes;
    }

    private function validateLimit(int $limit)
    {
        if ($limit > self::MAX_LIMIT || $limit < self::MIN_LIMIT ) {
            throw new LimitInvalidException('Filter value should be equal or lower than 10 and higher than 0!');
        }
    }
}
