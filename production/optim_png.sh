#! /bin/bash

pngcrush $1 $1_crushed 1>/dev/null && mv $1_crushed $1 &&
optipng $1 > /dev/null &&
echo "  done: $1" || echo "  ERROR: $1"
