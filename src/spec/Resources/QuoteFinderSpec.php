<?php

namespace spec\App\Resources;

use App\Entity\Author;
use App\Entity\Quote;
use App\Repository\AuthorRepository;
use App\Resources\AuthorNotFoundException;
use App\Resources\LimitInvalidException;
use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;

class QuoteFinderSpec extends ObjectBehavior
{
    function let(AuthorRepository $repository) {
        $this->beConstructedWith($repository);
    }

    function it_should_return_one_quote_if_author_exists_and_its_limited_to_one(AuthorRepository $repository) {
        $authorName = "steve";
        $limit = 1;
        $this->mockAuthor($repository, $authorName, true);

        $quotes = $this->findShoutedQuotesByActorWithLimit($authorName, $limit);

        $quotes->shouldBeArray();
        $quotes->shouldHaveCount(1);
    }

    function it_should_return_an_exception_when_author_does_not_exist(AuthorRepository $repository) {
        $authorName = "unknown";
        $limit = 1;
        $repository->findOneBy(array("name"=> $authorName))->willReturn(null);

        $this->shouldThrow(AuthorNotFoundException::class)
            ->during('findShoutedQuotesByActorWithLimit', [$authorName, $limit]);
    }

    function it_should_return_nothing_if_there_are_no_quotes(AuthorRepository $repository) {
        $authorName = "steve";
        $limit = 1;
        $this->mockAuthor($repository, $authorName, false);

        $quotes = $this->findShoutedQuotesByActorWithLimit($authorName, $limit);

        $quotes->shouldBeArray();
        $quotes->shouldHaveCount(0);
    }

    function it_should_return_an_exception_if_limit_is_higher_than_10(AuthorRepository $repository) {
        $authorName = "steve";
        $limit = 12;
        $repository->findOneBy(["name"=> $authorName])->shouldNotBeCalled();

        $this->shouldThrow(LimitInvalidException::class)
            ->during('findShoutedQuotesByActorWithLimit', [$authorName, $limit]);
    }

    function it_should_return_an_exception_if_limit_is_lower_than_1(AuthorRepository $repository) {
        $authorName = "steve";
        $limit = 0;
        $repository->findOneBy(["name"=> $authorName])->shouldNotBeCalled();

        $this->shouldThrow(LimitInvalidException::class)
            ->during('findShoutedQuotesByActorWithLimit', [$authorName, $limit]);
    }

    /**
     * @param AuthorRepository $repository
     * @param string $authorName
     * @param bool $withQuotes
     * @return Author
     */
    private function mockAuthor(AuthorRepository $repository, string $authorName, bool $withQuotes = true): Author
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

        $repository->findOneBy(array("name"=> $authorName))->willReturn($author);

        return $author;
    }
}
