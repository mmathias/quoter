<?php


namespace App\Resources;

use App\Message\QuoteRequestDTO;
use App\Repository\AuthorRepository;
use Symfony\Component\Messenger\MessageBusInterface;
use SymfonyBundles\RedisBundle\Redis\ClientInterface;

class QuoteFinder
{
    private const MAX_LIMIT = 10;
    private const MIN_LIMIT = 1;

    /**
     * @var AuthorRepository
     */
    private $authorRepository;
    /**
     * @var ClientInterface
     */
    private $client;
    /**
     * @var MessageBusInterface
     */
    private $bus;

    /**
     * QuoteFinder constructor.
     * @param AuthorRepository $authorRepository
     * @param ClientInterface $client
     */
    public function __construct(AuthorRepository $authorRepository, ClientInterface $client, MessageBusInterface $bus) {
        $this->authorRepository = $authorRepository;
        $this->client = $client;
        $this->bus = $bus;
    }

    public function findShoutedQuotesByActorWithLimit(string $author, int $limit): array
    {
        $this->validateLimit($limit);
        $quotes = $this->getQuotes($author);

        if (empty($quotes)) {
            return [];
        }

        return $this->getShoutedQuotes(array_slice($quotes, 0, $limit));
    }

    /**
     * @param string $authorName
     * @return array
     */
    private function getQuotes(string $authorName): array
    {
        $quotes = $this->client->lRange($authorName, 0, -1);
        if (empty($quotes)) {
            var_dump('Did not find in REDIS');
            $author = $this->authorRepository->findOneBy(["name" => $authorName]);
            $quotes = $author->getQuotes()->getValues();
            $this->storeInRedis($authorName, $quotes);
        }

        if (!$quotes) {
            throw new AuthorNotFoundException('Author not found.');
        }

        return $quotes;
    }

    /**
     * @param array $quotes
     * @return array
     */
    private function getShoutedQuotes(array $quotes): array
    {
        $shoutedQuotes = [];
        foreach ($quotes as $quote) {
            $shoutedQuotes[] = strtoupper($quote) . '!';
        }

        return $shoutedQuotes;
    }

    private function validateLimit(int $limit)
    {
        if ($limit > self::MAX_LIMIT || $limit < self::MIN_LIMIT ) {
            throw new LimitInvalidException('Filter value should be equal or lower than 10 and higher than 0!');
        }
    }

    private function storeInRedis(string $authorName, array $quotes)
    {
        var_dump('oi');
        $this->bus->dispatch(new QuoteRequestDTO($authorName, $quotes));
    }
}
