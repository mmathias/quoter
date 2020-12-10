<?php


namespace App\MessageHandler;


use App\Message\QuoteRequestDTO;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use SymfonyBundles\RedisBundle\Redis\ClientInterface;

class QuoterRequestHandler implements MessageHandlerInterface
{
    const TIMEOUT_IN_SECONDS = 60;

    /**
     * @var ClientInterface
     */
    private $redisClient;

    /**
     * QuoterRequestHandler constructor.
     * @param ClientInterface $redisClient
     */
    public function __construct(ClientInterface $redisClient)
    {
        $this->redisClient = $redisClient;
    }

    public function __invoke(QuoteRequestDTO $quoteRequestDTO) {
        $authorName = $quoteRequestDTO->getAuthorName();

        $this->redisClient->rpush($authorName, $quoteRequestDTO->getQuotes());
        $this->redisClient->expire($authorName, self::TIMEOUT_IN_SECONDS);
    }

}
