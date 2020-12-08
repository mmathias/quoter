<?php


namespace App\Resources;

use App\Message\QuoteRequestDTO;
use App\Repository\AuthorRepository;
use Doctrine\Common\Collections\Collection;
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

        return $this->getQuotes($author, $limit);
    }

    /**
     * @param string $authorName
     * @param int $limit
     * @return array
     */
    private function getQuotes(string $authorName, int $limit): array
    {
        $shoutedQuotes = $this->client->lRange($authorName, 0, -1);
        if (empty($shoutedQuotes)) {
            $author = $this->authorRepository->findOneBy(["name" => $authorName]);
            if (!$author) {
                throw new AuthorNotFoundException('Author not found.');
            }

            $shoutedQuotes = $this->getShoutedQuotes($author->getQuotes());
            $this->storeInRedis($authorName, $shoutedQuotes);
        }

        return array_slice($shoutedQuotes, 0, $limit);
    }

    /**
     * @param Collection $quotes
     * @return array
     */
    private function getShoutedQuotes(Collection $quotes): array
    {
        return $quotes->map(
            function($quote) {
                return strtoupper($quote) . '!';
            }
        )->getValues();
    }

    private function validateLimit(int $limit)
    {
        if ($limit > self::MAX_LIMIT || $limit < self::MIN_LIMIT ) {
            throw new LimitInvalidException('Filter value should be equal or lower than 10 and higher than 0!');
        }
    }

    private function storeInRedis(string $authorName, array $quotes)
    {
        $this->bus->dispatch(new QuoteRequestDTO($authorName, $quotes));
    }
}
