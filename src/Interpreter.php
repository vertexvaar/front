<?php

class Interpreter
{
    protected $tree = [];

    /**
     * @param array $tokens
     */
    public function interpret(array $tokens)
    {
        $tree = $this->buildTree($tokens);

        return $tree->execute();
    }

    /**
     * @param Token[] $tokens
     * @return Tree
     */
    protected function buildTree(array &$tokens)
    {
        $tree = new Tree();

        /** @var Token $token */
        while (list($key, $token) = each($tokens)) {
            if ($token->getType() === Token::OPENING_BRACE) {
                $tree->addToken($this->buildTree($tokens));
            } elseif ($token->getType() === Token::CLOSING_BRACE) {
                return $tree;
            } else {
                $tree->addToken($token);
            }
        }
        return $tree;
    }
}
