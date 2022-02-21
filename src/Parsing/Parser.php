<?php

declare(strict_types=1);

namespace VerteXVaaR\Front\Parsing;

use function array_filter;
use function array_map;
use function file;
use function file_get_contents;
use function printf;
use function unpack;

class Parser
{
    public function parse(string $string)
    {
        $bnfParser = new BnfParser();
        $closures = $bnfParser->parse(file_get_contents(__DIR__ . '/../../grammar.bnf.txt'));
        $search = array_filter(array_map('trim', file(__DIR__ . '/../../searchtypes.txt')), function (string $line) {
            return !empty($line) && '#' !== $line[0];
        });

        $ordArray = unpack('C*', $string);
        foreach ($search as $name) {
            $result = $closures[$name]($ordArray);
            if (null !== $result && empty($result[1])) {
                printf("Type: $name matched\n");
            }
        }
    }
}
