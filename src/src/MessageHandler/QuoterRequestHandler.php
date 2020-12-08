<?php


namespace App\MessageHandler;


use App\Message\QuoteRequestDTO;
use App\Repository\QuoteRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use SymfonyBundles\RedisBundle\Redis\ClientInterface;

class QuoterRequestHandler implements MessageHandlerInterface
{
    /**
     * @var QuoteRepository
     */
    private $quoteRepository;
    /**
     * @var ClientInterface
     */
    private $redisClient;

    /**
     * QuoterRequestHandler constructor.
     * @param QuoteRepository $quoteRepository
     * @param ClientInterface $redisClient
     */
    public function __construct(QuoteRepository $quoteRepository, ClientInterface $redisClient)
    {
        $this->quoteRepository = $quoteRepository;
        $this->redisClient = $redisClient;
    }

    public function __invoke(QuoteRequestDTO $quoteRequestDTO) {
        $this->redisClient->lpush($quoteRequestDTO->getAuthorName(), $quoteRequestDTO->getQuotes());
    }

}
