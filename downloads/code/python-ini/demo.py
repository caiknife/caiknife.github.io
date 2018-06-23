#!/usr/bin/python
#coding: utf-8

import ConfigParser

class FakeSectionHead(object):
    """
    文件wrapper
    """
    def __init__(self, fp):
        """如果没有section的话，默认添加一个叫[default]的section"""
        self.fp = fp
        self.section = "[default]\n"

    def readline(self):
        """第一次输出[default], 以后就输出文件的内容"""
        if self.section:
            try:
                return self.section
            finally:
                self.section = None
        else:
            return self.fp.readline()

ini = './config/smus.ini'

def parse_ini_file(ini):
    p = ConfigParser.ConfigParser()
    try:
        """标准格式的ini文件，直接读取内容"""
        p.readfp(open(ini, 'rb'))
    except ConfigParser.MissingSectionHeaderError, error:
        """非标准格式的ini文件，用FakeSectionHead类包装一下"""
        print error
        """
        此处输出：
        File contains no section headers.
        file: ./config/smus.ini, line: 1
        'master.host                 = stats10.mezimedia.com\r\n'
        """
        p.readfp(FakeSectionHead(open(ini, 'rb')))

    return p

p = parse_ini_file(ini)
print p.items('default')
"""
[('master.host', 'stats10.mezimedia.com'), 
 ('master.username', 'scltrk'), 
 ('master.password', 'Mhok8homL7'), 
 ('master.dbname', 'tracking'), 
 ('slave.host', 'stats11.mezimedia.com'), 
 ('slave.port', '3308'), 
 ('slave.username', 'scltrk'), 
 ('slave.password', 'Mhok8homL7'), 
 ('slave.dbname', 'tracking'), 
 ('msgsite', 'smarter'), 
 ('offset', '20000')]
"""
print p.sections()
"""
['default']
"""
