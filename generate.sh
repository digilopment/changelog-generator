#!/bin/bash

if [ $# -ge 1 ]; then
    arg1="$1"
fi

if [ $# -ge 2 ]; then
    arg2="$2"
fi

if [ -z "$arg1" ] && [ ! -z "$arg2" ]; then
    echo "arg1 is required when arg2 is present"
    exit 1
fi

command="php generate.php"

if [ ! -z "$arg1" ]; then
    command="$command \"$arg1\""
fi

if [ ! -z "$arg2" ]; then
    command="$command \"$arg2\""
fi

eval $command
