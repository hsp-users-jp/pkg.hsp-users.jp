#!/bin/bash

find ./ -name ._\* -exec rm -f {} \;

# oil t --group=App --coverage-html=coverage
oil t --group=App
