#!/usr/bin/env python
# coding:utf8

import os
import sys
import requests
import re
from pyquery import PyQuery as pq

entity = {}
images = []


def main():
    try:
        get_url()
        load_page()
        save_imgs()
    except Exception, e:
        print e


def get_url():
    args = sys.argv
    # 如果不是dl.py zhihu_url这种格式的话，抛出异常
    if len(args) != 2:
        raise Exception(u"Wrong number for args, please use Zhihu question url!")

    zhihu_url = args[1]
    # zhihu_url不符合问题页面url格式的话，抛出异常
    re_exp = re.compile(ur"^https://www\.zhihu\.com/question/(\d+)")
    match = re_exp.match(zhihu_url)
    if not match:
        raise Exception(u"Zhihu url is invalid!")

    entity['url'] = zhihu_url
    entity['question'] = match.groups()[0]

    print entity


def load_page():
    header = {
        ur'User-Agent': ur'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.116 Safari/537.36',
        ur'Host': ur'www.zhihu.com',
        ur'Accept': ur'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
        ur'Accept-Language': ur'zh-CN,zh;q=0.8,en;q=0.6',
        ur'Accept-Encoding': ur'gzip, deflate, sdch',
        ur'Connection': ur'keep-alive',
        ur'Cache-Control': ur'max-age=0'
    }

    resp = requests.get(entity['url'], headers=header)
    if resp.status_code != 200:
        raise Exception(u"Http error!")

    d = pq(resp.content)
    title = d('title').text()
    entity['title'] = title.split(u" ")[0]
    imgs = d("img.origin_image.zh-lightbox-thumb.lazy")
    for ele in imgs:
        images.append(pq(ele).attr("data-original"))


def save_imgs():
    dest_dir = os.path.dirname(os.path.abspath(__file__)) + "/images/" + entity['question'] + \
               entity['title']
    print dest_dir
    if not os.path.exists(dest_dir):
        os.makedirs(dest_dir)

    for img in images:
        res = requests.get(img)
        filename = os.path.basename(img)
        fp = open(dest_dir + "/" + filename, "wb")
        fp.write(res.content)
        fp.close()
        print img + " done."


if __name__ == "__main__":
    main()
