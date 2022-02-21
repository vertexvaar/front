<?php

declare(strict_types=1);

namespace VerteXVaaR\Front\Parsing;

use Closure;

use function array_filter;
use function array_map;
use function array_merge;
use function array_reverse;
use function array_shift;
use function array_unique;
use function count;
use function end;
use function explode;
use function in_array;
use function key;
use function natsort;
use function ord;
use function preg_match;
use function reset;
use function strlen;
use function strpos;
use function trim;

use const PHP_EOL;

class BnfParser
{
    public function parse(string $bnfGrammar): array
    {
        $rules = $this->getRules($bnfGrammar);
        $rules = $this->indexRules($rules);

        $resolved = [];

        while (($toBeat = count($rules)) > 0) {
            foreach (array_reverse($rules) as $name => $matchGroups) {
                unset($ruleRecursion);
                unset($ruleClosure);
                $ruleClosure = null;
                $ruleRecursion = null;
                $ruleRecursion = static function (array $input) use (&$ruleClosure, &$ruleRecursion): ?array {
                    $output = [
                        0 => [],
                        1 => $input,
                    ];
                    foreach ([$ruleClosure, $ruleRecursion] as $closure) {
                        $result = $closure($output[1]);
                        if (!empty($result[0])) {
                            $output[0] = array_merge($output[0], $result[0]);
                            $output[1] = $result[1];
                        } else {
                            return $output;
                        }
                    }
                    return $output;
                };

                $groupClosures = [];

                if (count($matchGroups) === 1 && strlen($matchGroups[0]) === 1) {
                    // match group has a single character, just match that char
                    $groupClosures[] = $this->getMatchCharExactFunction(ord($matchGroups[0]));
                } elseif ([1] === array_unique(array_map('strlen', $matchGroups))) {
                    // If every char is a single character we can try to match a sequence or fallback to a match list
                    // Convert all chars to an array of their ASCII numbers
                    $ordChars = array_map('ord', $matchGroups);
                    natsort($ordChars);
                    $isSequence = true;
                    $lower = $previous = $ordChars[0];
                    for ($i = 1; $i < count($ordChars); $i++) {
                        $current = $ordChars[$i];
                        if ($previous !== $current - 1) {
                            $isSequence = false;
                            break;
                        }
                        $previous = $current;
                    }
                    if ($isSequence) {
                        $closure = $this->getMatchCharRangeFunction($lower, end($ordChars));
                    } else {
                        $closure = $this->getMatchCharListFunction($ordChars);
                    }
                    $groupClosures[] = $closure;
                } else {
                    foreach ($matchGroups as $matchGroup) {
                        $groupParts = array_filter(array_map('trim', explode(' ', $matchGroup)));

                        $partClosures = [];
                        foreach ($groupParts as $part) {
                            if ($name === $part) {
                                $closure = $ruleRecursion;
                            } elseif (preg_match('/\<[\w\_]+\>/', $part)) {
                                if (!isset($resolved[$part])) {
                                    break 2;
                                }
                                $closure = $resolved[$part];
                            } elseif (strlen($part) === 1) {
                                $closure = $this->getMatchCharExactFunction(ord($part));
                            } else {
                                throw new \Exception('Invalid match group part "' . $part . '"');
                            }
                            $partClosures[$part] = $closure;
                        }
                        if (!empty($partClosures)) {
                            if (count($partClosures) === 1) {
                                $groupClosures[$matchGroup] = reset($partClosures);
                            } else {
                                $groupClosures[$matchGroup] = $this->getMatchSequenceFunction($partClosures);
                            }
                        }
                    }
                }

                if (!empty($groupClosures)) {
                    if (count($groupClosures) === 1) {
                        $ruleClosure = reset($groupClosures);
                    } else {
                        $ruleClosure = $this->getMatchAnyClosureFunction($groupClosures);
                    }
                    $resolved[$name] = $ruleClosure;
                    unset($rules[$name]);
                }
            }

            if (count($rules) >= $toBeat) {
                throw new \Exception('No rule was resolved this iteration');
            }
        }

        return $resolved;
    }

    protected function getMatchAnyClosureFunction(array $closures): Closure
    {
        return static function (array $input) use ($closures): ?array {
            foreach ($closures as $closure) {
                $result = $closure($input);
                if (!empty($result[0])) {
                    return $result;
                }
            }
            return null;
        };
    }

    protected function getMatchSequenceFunction(array $closures): Closure
    {
        return static function (array $input) use ($closures): ?array {
            $output = [
                0 => [],
                1 => $input,
            ];
            foreach ($closures as $closure) {
                $result = $closure($output[1]);
                if (!empty($result[0])) {
                    $output[0] = array_merge($output[0], $result[0]);
                    $output[1] = $result[1];
                } else {
                    return null;
                }
            }
            return $output;
        };
    }

    protected function getMatchCharRangeFunction(int $lower, int $upper): Closure
    {
        return static function (array $input) use ($lower, $upper): ?array {
            $char = array_shift($input);
            if ($char >= $lower && $char <= $upper) {
                return [[$char], $input];
            }
            return null;
        };
    }

    protected function getMatchCharExactFunction(int $exact): Closure
    {
        return static function (array $input) use ($exact): ?array {
            $char = array_shift($input);
            if ($char === $exact) {
                return [[$char], $input];
            }
            return null;
        };
    }

    protected function getMatchCharListFunction(array $list): Closure
    {
        return static function (array $input) use ($list): ?array {
            $char = array_shift($input);
            if (in_array($char, $list, true)) {
                return [[$char], $input];
            }
            return null;
        };
    }

    protected function getRules(string $bnfGrammar): array
    {
        $rules = [];

        $lines = array_filter(explode(PHP_EOL, $bnfGrammar));
        foreach ($lines as $index => $line) {
            // Subsequent lines not having '::=' belong to their previous lines
            if (false !== strpos($line, '::=')) {
                $rules[$index] = $line;
            } else {
                end($rules);
                $key = key($rules);
                $rules[$key] .= $line;
            }
        }
        return $rules;
    }

    protected function indexRules(array $rules): array
    {
        foreach ($rules as $index => $rule) {
            unset($rules[$index]);
            [$name, $definition] = explode('::=', $rule);
            $rules[trim($name)] = array_map('trim', explode('|', $definition));
        }
        return $rules;
    }
}
