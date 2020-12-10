<?php

namespace spec\App\Resources;

use App\Entity\Author;
use App\Entity\Quote;
use App\Repository\AuthorRepository;
use App\Resources\AuthorNotFoundException;
use App\Resources\LimitInvalidException;
use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use SymfonyBundles\RedisBundle\Redis\ClientInterface;

class QuoteFinderSpec extends ObjectBehavior
{
    function let(AuthorRepository $authorRepository, ClientInterface $redisClient, MessageBusInterface $queueClient) {
        $this->beConstructedWith($authorRepository, $redisClient, $queueClient);
    }

    function it_should_return_one_quote_if_author_exists_and_its_limited_to_one(AuthorRepository $authorRepository, MessageBusInterface $queueClient) {
        $authorName = "steve";
        $limit = 1;
        $this->mockAuthor($authorRepository, $authorName, true);
        $queueClient->dispatch(Argument::any())->willReturn(new Envelope((object)[]));

        $quotes = $this->findShoutedQuotesByActorWithLimit($authorName, $limit);

        $quotes->shouldBeArray();
        $quotes->shouldHaveCount(1);
        $quotes[0]->shouldBe('BE OR NOT BE!');
    }

    function it_should_return_an_exception_when_author_does_not_exist(AuthorRepository $authorRepository) {
        $authorName = "unknown";
        $limit = 1;
        $authorRepository->findOneBy(array("name"=> $authorName))->willReturn(null);

        $this->shouldThrow(AuthorNotFoundException::class)
            ->during('findShoutedQuotesByActorWithLimit', [$authorName, $limit]);
    }

    function it_should_return_nothing_if_there_are_no_quotes(AuthorRepository $authorRepository, MessageBusInterface $queueClient) {
        $authorName = "steve";
        $limit = 1;
        $this->mockAuthor($authorRepository, $authorName, false);
        $queueClient->dispatch(Argument::any())->willReturn(new Envelope((object)[]));

        $quotes = $this->findShoutedQuotesByActorWithLimit($authorName, $limit);

        $quotes->shouldBeArray();
        $quotes->shouldHaveCount(0);
    }

    function it_should_return_an_exception_if_limit_is_higher_than_10(AuthorRepository $authorRepository) {
        $authorName = "steve";
        $limit = 12;
        $authorRepository->findOneBy(["name"=> $authorName])->shouldNotBeCalled();

        $this->shouldThrow(LimitInvalidException::class)
            ->during('findShoutedQuotesByActorWithLimit', [$authorName, $limit]);
    }

    function it_should_return_an_exception_if_limit_is_lower_than_1(AuthorRepository $authorRepository) {
        $authorName = "steve";
        $limit = 0;
        $authorRepository->findOneBy(["name"=> $authorName])->shouldNotBeCalled();

        $this->shouldThrow(LimitInvalidException::class)
            ->during('findShoutedQuotesByActorWithLimit', [$authorName, $limit]);
    }

    function it_should_return_from_cache_if_author_exists(AuthorRepository $authorRepository, ClientInterface $redisClient, MessageBusInterface $queueClient) {
        $authorName = "steve";
        $limit = 1;
        $author = $this->createAuthorMock($authorName, true);
        $authorRepository->findBy((array)Argument::any())->shouldNotBeCalled();
        $redisClient->lRange($authorName, 0, $limit)->willReturn($author->getQuotes()->getValues());
        $queueClient->dispatch(Argument::any())->willReturn(new Envelope((object)[]));

        $quotes = $this->findShoutedQuotesByActorWithLimit($authorName, $limit);

        $quotes->shouldBeArray();
        $quotes->shouldHaveCount(1);
    }

    /**
     * @param AuthorRepository $authorRepository
     * @param string $authorName
     * @param bool $withQuotes
     * @return Author
     */
    private function mockAuthor(AuthorRepository $authorRepository, string $authorName, bool $withQuotes = true): Author
    {
        $author = $this->createAuthorMock($authorName, $withQuotes);

        $authorRepository->findOneBy(["name"=> $authorName])->willReturn($author);

        return $author;
    }

    /**
     * @param string $authorName
     * @param bool $withQuotes
     * @return Author
     */
    private function createAuthorMock(string $authorName, bool $withQuotes): Author
    {
        $author = new Author();
        $author->setName($authorName);
        $quote1 = new Quote();
        $quote1->setQuote("Be or not be");
        $quote2 = new Quote();
        $quote2->setQuote("Brilliant");

        $author->setQuotes(new ArrayCollection([]));
        if ($withQuotes) {
            $author->setQuotes(new ArrayCollection([$quote1, $quote2]));
        }
        return $author;
    }
}
