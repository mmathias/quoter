<?php


namespace App\MessageHandler;


use App\Message\QuoteRequestDTO;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class QuoterRequestHandler implements MessageHandlerInterface
{

    public function __invoke(QuoteRequestDTO $quoteRequestDTO) {
        var_dump('HERE!');
    }

}
