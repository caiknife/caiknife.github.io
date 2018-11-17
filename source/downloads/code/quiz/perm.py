#!/usr/bin/env python
# coding:utf8

from itertools import permutations


def main():
    result = permutations('abc')

    for v in result:
        print v


if __name__ == "__main__":
    main()
