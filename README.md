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

-> Continue at the tag specific instructions (under alternative attempts)

## Alternative attempts

### Tokenizer

The tag `tokenizer` tags a commit where the input on the CLI is split into single tokens
which will then be ordered in a tree structure (AST) which then will be executed.

#### Speciality

The interpreter will try to make sense out of every operation given to it.
Even mathematical operations on strings.

#### Usage 

1. `./front`
1. Type in some calculations.

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
* `2 + 4 * 3` (ATTENTION: This should return 16 but returns 18 because it is calculated left to right,
not point before line)

### Functional

The tag `functional` marks a state of this repository with a different approach to tokenizing/parsing than
on the youtube video "function parsing" by computerphile: https://www.youtube.com/watch?v=dDtZLm7HIJs
The functional branch contains an approach that has hit its limits and will be discontinued.

#### Speciality

This approach does not actually calculate any output. It will just return the parsed input,
which could be the foundation for calculations and other operations.

#### Usage 

1. `./front`
1. Type in anything you like.

### BNF-Parser

The Backus-Naur-Form is a special text that defines formal language.
The BNF-Definition is written in the `grammar.bnf.txt` file.
The `searchtypes.txt` file containes all tags the input should be scanned for.

The aim of this code is to write and understand a parser or rather an interpreter for the BNF.

#### Speciality

The interpreter will not do any calculations but it will classify your input based on the rules and tags given in
the input files `grammar.bnf.txt` and `searchtypes.txt`

Fixed strings and whitespace are not yet supported

#### Usage 

1. Run `bash ctrl.sh install` once
1. Run `bash ctrl.sh run` to start the interpreter
1. Type in anything you want to classify

You should try to invent your own language ;)
Open `grammar.bnf.txt` and type in your own rules. E.g. you can define that every word must begin with an "A".

You can add the `-x` flag to the run command to initiate a xdebug session.
