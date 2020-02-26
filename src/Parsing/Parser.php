<?php
declare(strict_types=1);
namespace VerteXVaaR\Front\Parsing;

use Closure;
use function matchFirstMatching;
use function var_export;

class Parser
{
    public function parse(string $string)
    {
        $result = matchFirstMatching(
            $string,
            [
                Closure::fromCallable('matchParenthesisExpression'),
                Closure::fromCallable('matchMathExpression'),
            ]
        );
        return var_export($result, true);
    }
}
