---
layout: post
title: "完美洗牌"
date: 2013-08-26 11:20
comments: true
categories: python
---
在知乎上看到这么一个问题<http://www.zhihu.com/question/21503093>，里面提到了完美洗牌，就想想一副牌到底要经过多少次完美洗牌才能复原呢？写了个小程序测试一下。

``` python
#coding: utf-8
"""
http://www.zhihu.com/question/21503093

a set of cards including 52 cards tagged from 1 to 52, no joker cards
"""

import copy

def perfect_shuffle(cards):
    size = len(cards)
    left, right = cards[0:size/2], cards[size/2:]
    new_cards = []
    
    for i in range(size/2):
        new_cards.append(left[i])
        new_cards.append(right[i])

    return new_cards


def main():
    cards = range(1, 53)
    shuffle_times = 0
    old_cards = copy.deepcopy(cards)
    new_cards = []

    while True:
        new_cards = perfect_shuffle(old_cards)
        shuffle_times += 1

        if new_cards == cards:
            break

        old_cards = copy.deepcopy(new_cards)

    print shuffle_times


if __name__ == '__main__':
    main()
```

<!-- more -->

最终确定是8次。

但是我这个完美洗牌算法不是最优解。正确的完美洗牌要求是：

> 输入a1, a2, ..., an, b1, b2, ..., bn，这是一个2n大小的数组。要求用O(n)的时间，用O(1)的空间，将这个序列顺序改为a1, b1, ..., an, bn。

