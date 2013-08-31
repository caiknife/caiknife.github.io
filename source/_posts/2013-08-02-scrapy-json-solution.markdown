---
layout: post
title: "Scrapy保存json格式的一点小问题"
date: 2013-08-02 00:01
comments: true
categories: scrapy python
---
折腾了两天，才把Scrapy的稍微弄懂了一点点，还得多加努力。

昨天用Scrapy练手，打算先爬豆瓣小组的帖子。没想到由于中文的问题，Scrapy会用unicode来处理中文，所以保存到文件中就成了unicode字符串，昨天弄了一晚上都没弄好，今晚稍微理清了一点头绪。

<!-- more -->

``` python
def list_first_item(item):
    return item[0].strip() if item and isinstance(item, list) else None


class DoubangroupSpider(CrawlSpider):
    name = 'doubangroup'
    
    allowed_domains = ['douban.com']
    
    start_urls = ['http://www.douban.com/group/topic/37643549/']
    
    rules = (
        Rule(SgmlLinkExtractor(allow=r'/group/topic/\d+/', deny='/group/topic/37643549/'), callback='parse_item', follow=False),
    )

    def parse_item(self, response):
        hxs = HtmlXPathSelector(response)
        i = TutorialItem()
        i['title'] = list_first_item(hxs.select('//div[@id="wrapper"]/div[@id="content"]/h1/text()').extract())
        i['url'] = response.url
        return i


class TopicPipeline(object):
    def __init__(self):
        self.file = codecs.open("out.json", "wb", encoding="utf-8")

    def process_item(self, item, spider):
        line = json.dumps(dict(item), ensure_ascii=False) + "\n"
        self.file.write(line)
        return item

    def spider_closed(self, spider):
        self.file.close()
```

上面的代码大概是从一个spider爬取数据并输送到pipeline的过程。关键在于```self.file = codecs.open("out.json", "wb", encoding="utf-8")```和```line = json.dumps(dict(item), ensure_ascii=False) + "\n"```这两行代码，如此一来就能够将unicode的数据以utf8的格式保存了。

以上问题的解决方案来自Stack Overflow - [scrapy text encoding](http://stackoverflow.com/questions/9181214/scrapy-text-encoding)