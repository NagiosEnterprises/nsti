#!/bin/sh

PREREQS='mysql httpd gcc wget make tar'

echo "Checking installer prereqs..."
echo "-----------------------------"
if ! is_installed "$PREREQS";
then
    echo "Baseline prereqs are not installed. You must have these installed:"
    for prereq in "$PREREQS";
    do
        echo " - $prereq"
    done
    echo "Cannot continue install until all of these prereqs are met."
    exit 1
fi
echo "Installer prereqs met."
echo ""