<?php
declare(strict_types=1);

function matchChar(string $input): array
{
    if (isset($input[0])) {
        $ord = ord($input[0]);
        if (($ord >= 65 && $ord <= 90) || ($ord >= 97 && $ord <= 122)) {
            return [$input[0], substr($input, 1)];
        }
    }
    return ['', $input];
}

function matchDigit(string $input): array
{
    if (isset($input[0])) {
        $ord = ord($input[0]);
        if (($ord >= 48 && $ord <= 57)) {
            return [$input[0], substr($input, 1)];
        }
    }
    return ['', $input];
}

function consecutive(string $input, Closure $function): array
{
    $output = [
        0 => '',
        1 => '',
    ];
    while (strlen($input) > 0) {
        $result = $function($input);
        if ('' !== $result[0]) {
            $output[0] .= $result[0];
            $input = $result[1];
        } else {
            $output[1] = $input;
            break;
        }
    }
    return $output;
}

function matchFirstMatching(string $input, array $closures): array
{
    foreach ($closures as $closure) {
        $result = $closure($input);
        if ('' !== $result[0]) {
            return $result;
        }
    }
    return ['', $input];
}

function matchAnyConsecutive(string $input, array $closures): array
{
    return consecutive(
        $input,
        function (string $input) use ($closures): array {
            return matchFirstMatching($input, $closures);
        }
    );
}

function matchSequence(string $input, array $sequence): array
{
    $matched = '';
    $carry = $input;
    foreach ($sequence as $matcher) {
        $result = $matcher($carry);
        if ('' === $result[0]) {
            return ['', $input];
        }
        $matched .= $result[0];
        $carry = $result[1];
    }
    return [$matched, $carry];
}

function matchNumber(string $input): array
{
    return consecutive($input, Closure::fromCallable('matchDigit'));
}

function matchString(string $input): array
{
    return consecutive($input, Closure::fromCallable('matchChar'));
}

function matchWord(string $input): array
{
    return matchAnyConsecutive($input, [Closure::fromCallable('matchChar'), Closure::fromCallable('matchDigit')]);
}

function matchAddition(string $input): array
{
    if (isset($input[0]) && 43 === ord($input[0])) {
        return [$input[0], substr($input, 1)];
    }
    return ['', $input];
}

function matchSubtraction(string $input): array
{
    if (isset($input[0]) && 45 === ord($input[0])) {
        return [$input[0], substr($input, 1)];
    }
    return ['', $input];
}

function matchMultiplication(string $input): array
{
    if (isset($input[0]) && 42 === ord($input[0])) {
        return [$input[0], substr($input, 1)];
    }
    return ['', $input];
}

function matchDivision(string $input): array
{
    if (isset($input[0]) && 47 === ord($input[0])) {
        return [$input[0], substr($input, 1)];
    }
    return ['', $input];
}

function matchWhitespace(string $input): array
{
    if (isset($input[0]) && 32 === ord($input[0])) {
        return [$input[0], substr($input, 1)];
    }
    return ['', $input];
}

function matchOpeningParenthesis(string $input): array
{
    if (isset($input[0]) && 40 === ord($input[0])) {
        return [$input[0], substr($input, 1)];
    }
    return ['', $input];
}

function matchClosingParenthesis(string $input): array
{
    if (isset($input[0]) && 41 === ord($input[0])) {
        return [$input[0], substr($input, 1)];
    }
    return ['', $input];
}

function matchMathOperator(string $input): array
{
    return matchFirstMatching(
        $input,
        [
            Closure::fromCallable('matchAddition'),
            Closure::fromCallable('matchSubtraction'),
            Closure::fromCallable('matchMultiplication'),
            Closure::fromCallable('matchDivision'),
        ]
    );
}

function matchMathExpression(string $input): array
{
    return matchSequence(
        $input,
        [
            Closure::fromCallable('matchNumber'),
            Closure::fromCallable('matchMathOperator'),
            Closure::fromCallable('matchNumber'),
        ]
    );
}

function matchParenthesisExpression(string $input): array
{
    return matchSequence(
        $input,
        [
            Closure::fromCallable('matchOpeningParenthesis'),
            Closure::fromCallable('matchMathExpression'),
            Closure::fromCallable('matchClosingParenthesis'),
        ]
    );
}
