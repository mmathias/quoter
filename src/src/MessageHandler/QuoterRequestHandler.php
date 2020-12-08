<?php


namespace App\MessageHandler;


use App\Message\QuoteRequestDTO;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use SymfonyBundles\RedisBundle\Redis\ClientInterface;

class QuoterRequestHandler implements MessageHandlerInterface
{
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
        $this->redisClient->lpush($quoteRequestDTO->getAuthorName(), $quoteRequestDTO->getQuotes());
    }

}
