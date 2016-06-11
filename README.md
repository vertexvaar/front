# about front

"front" is a interpreter based on PHP

# usage

clone this repo and run "./front" on your command line

# what is this for

in short: nothing

this is a experimental interpreter. it takes your input, tokenizes it and returns the result.

# what is speacial about it

you can use arithmetic operations on string.
yep, wtf!

# attention

DO NOT USE THIS
I'm serious. This whole thing is experimental. It doesn't even respect mathematic rules.

# syntax

each parameter is delimited by whitespace.

examples:

* 1 + 1
* 53628749 * 7453
* 7 / 5
* foo + bar
* abc * fff
* 1 + ( 7 * 2 )
* 2 * ( 4 + 1 )
* 2 * 4 + 1
* 2 + 4 * 3 (ATTENTION: This should return 16 but returns 18 because it is calculated left to right, not point before line)
