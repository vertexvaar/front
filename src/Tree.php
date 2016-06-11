<?php

class Tree extends Token
{
    /**
     * @var Token[]
     */
    protected $tokens = [];

    /**
     * @var array
     */
    protected $stack = [];

    /**
     * @param Token $token
     */
    public function addToken(Token $token)
    {
        $this->tokens[] = $token;
    }

    public function execute()
    {
        foreach ($this->tokens as $index => $token) {
            if ($token instanceof Tree) {
                $this->tokens[$index] = $token->resolveTokens();
            }
        }
        $this->stack = array_reverse($this->tokens);

        return $this->reduce();
    }

    protected function resolveTokens()
    {
        return $this->execute();
    }

    /**
     * @return Token|null
     * @throws Exception
     */
    protected function reduce()
    {
        while (($count = count($this->stack)) > 1) {
            if ($count < 2) {
                throw new \Exception('Impossible operation');
            }
            $left = array_pop($this->stack);
            $operation = array_pop($this->stack);
            $right = array_pop($this->stack);
            $this->stack[] = $this->executeOperation($left, $operation, $right);
        }
        if ($count === 0) {
            return null;
        } else {
            return reset($this->stack);
        }
    }

    protected function executeOperation(Token $left, Token $operation, Token $right)
    {
        if ($operation->getType() !== Token::OPERATION) {
            throw new \Exception(
                'Missing operation between "' . $left->getValue() . '" and "' . $right->getValue() . '"'
            );
        }
        switch ($operation->getValue()) {
            case '+':
                return $this->add($left, $right);
            case '-':
                return $this->sub($left, $right);
            case '*':
                return $this->mul($left, $right);
            case '/':
                return $this->div($left, $right);
            default:
                throw new \Exception('Unknow operation ' . $operation->getValue());
        }
    }

    protected function add(Token $left, Token $right)
    {
        if ($left->getType() !== $right->getType()) {
            throw new \Exception('can not add type ' . $left->getType() . ' to type ' . $right->getType());
        }

        $token = new Token();
        $token->setType($left->getType());

        switch ($left->getType()) {
            case Token::STRING:
                $token->setValue($left->getValue() . $right->getValue());
                break;
            case Token::INTEGER:
                $token->setValue($left->getValue() + $right->getValue());
                break;
        }

        return $token;
    }

    protected function sub(Token $left, Token $right)
    {
        if ($left->getType() !== $right->getType()) {
            throw new \Exception('can not sub type ' . $left->getType() . ' to type ' . $right->getType());
        }

        $token = new Token();
        $token->setType($left->getType());

        switch ($left->getType()) {
            case Token::STRING:
                $string1 = $left->getValue();
                $string2 = $right->getValue();
                if (strrpos($string1, $string2) === strlen($string1) - ($str2len = strlen($string2))) {
                    $token->setValue(substr($string1, 0, -$str2len));
                } else {
                    $token->setValue($string1);
                }
                break;
            case Token::INTEGER:
                $token->setValue($left->getValue() - $right->getValue());
                break;
        }

        return $token;
    }

    protected function mul(Token $left, Token $right)
    {
        if ($left->getType() !== $right->getType()) {
            throw new \Exception('can not multiply type ' . $left->getType() . ' to type ' . $right->getType());
        }

        $token = new Token();
        $token->setType($left->getType());

        switch ($left->getType()) {
            case Token::STRING:
                $value = '';
                $rString = str_split($right->getValue());

                foreach ($rString as $rChar) {
                    $value .= $left->getValue();
                    $value .= $rChar;
                }

                $token->setValue($value);
                break;
            case Token::INTEGER:
                $token->setValue($left->getValue() * $right->getValue());
                break;
        }

        return $token;
    }

    protected function div(Token $left, Token $right)
    {
        if ($left->getType() !== $right->getType()) {
            throw new \Exception('can not divide type ' . $left->getType() . ' to type ' . $right->getType());
        }

        $token = new Token();
        $token->setType($left->getType());

        switch ($left->getType()) {
            case Token::STRING:
                $token->setValue(str_replace($right->getValue(), '', $left->getValue()));
                break;
            case Token::INTEGER:
                $token->setValue($left->getValue() / $right->getValue());
                break;
        }

        return $token;
    }
}
