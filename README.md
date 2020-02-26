# About `front`

`front` is an experimental interpreter built with PHP.
This interpreter will take your input, tokenize it, built an AST and execute the AST.
It is a package built to experiment with the concept of parser and interpreted languages. 

## ATTENTION / DISCLAIMER

DO NOT USE THIS PACKAGE IN PRODUCTION!

USE ONLY FOR EDUCATIONAL PURPOSES!

I'm serious. This whole thing is experimental. It doesn't even respect basic mathematical rules.

## Usage

1. `git clone git@github.com:vertexvaar/front.git`
1. `cd front`
1. `./front`
1. Type in some calculations.

## What is special about this kind of interpreter?

The interpreter will try to make sense out of every operation given to it.
Even mathematical operations on strings.

## Syntax

Each parameter is delimited by whitespace.

Examples:

* `1 + 1`
* `53628749 * 7453`
* `7 / 5`
* `foo + bar`
* `abc * fff`
* `1 + ( 7 * 2 )`
* `2 * ( 4 + 1 )`
* `2 * 4 + 1`
* `2 + 4 * 3` (ATTENTION: This should return 16 but returns 18 because it is calculated left to right, not point before line)

## Alternative branches

The branch `functional` contains a different approach to tokenizing/parsing based on the youtube video "function parsing" by computerphile: https://www.youtube.com/watch?v=dDtZLm7HIJs
