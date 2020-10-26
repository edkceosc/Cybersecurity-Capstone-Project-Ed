#!/bin/sh

set -e

find . -type f -name "*.php" | grep -v vendor | while read ln; do
  php -l "$ln"
done

