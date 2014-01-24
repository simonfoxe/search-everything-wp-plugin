#! /bin/bash

jpegoptim $1 > /dev/null &&
echo "  done: $1" || echo "  ERROR: $1"
