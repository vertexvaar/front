<?php

class Tokenizer
{
    /**
     * @param $line
     * @return array
     * @throws Exception
     */
    public function tokenize($line)
    {
        $parts = explode(' ', $line);
        $tokens = [];

        foreach ($parts as $part) {
            $token = new Token();
            $type = null;
            $prevType = null;
            $lenght = strlen($part);
            for ($i = 0; $i < $lenght; $i++) {
                $char = $part[$i];
                $type = $this->getType($char);
                if (null === $prevType) {
                    $prevType = $type;
                    $token->setType($type);
                } elseif ($prevType !== $type) {
                    throw new \Exception('Part "' . $part . ' of line ' . $line . ' is inconsitent!');
                }
                $token->appendValue($char);
            }
            $tokens[] = $token;
        }
        return $tokens;
    }

    /**
     * @param string $char
     * @return string
     */
    protected function getType($char)
    {
        $ascii = ord($char);
        if ($this->in(48, $ascii, 57)) {
            return Token::INTEGER;
        } elseif ($this->in(65, $ascii, 122)) {
            return Token::STRING;
        } elseif (40 === $ascii) {
            return Token::OPENING_BRACE;
        } elseif (41 === $ascii) {
            return Token::CLOSING_BRACE;
        } elseif (in_array($ascii, [42, 43, 45, 47])) {
            return Token::OPERATION;
        } else {
            printLine('Character "' . $char . '" [' . $ascii . '] is not supported');
            exit(1);
        }
    }

    /**
     * @param string $low
     * @param string $value
     * @param string $high
     * @return bool
     */
    protected function in($low, $value, $high)
    {
        return $low === $value
               || $high === $value
               || ($high > $value && $low < $value);
    }
}
