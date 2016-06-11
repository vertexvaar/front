<?php

class Interactive
{
    public function run()
    {
        $cli = fopen('php://stdin', 'r');

        $line = '';

        $tokenizer = new Tokenizer();
        $interpreter = new Interpreter();

        do {
            $tokens = $tokenizer->tokenize($line);
            $result = $interpreter->interpret($tokens);
            if (null !== $result) {
                printLine($result->getValue());
            }
            echo '> ';
        } while (($line = trim(fgets($cli))));
    }
}
