#!/bin/sh

is_installed () {
    local binary="$1"
    for file in $@;
    do
        if ! which "$file" 2> /dev/null;
        then
            echo "$file is not installed. You need to install using your distro's package manager."
            return 1
        fi
    done
    return 0
}

BASEPATH=$(dirname `readlink -f $0`)
