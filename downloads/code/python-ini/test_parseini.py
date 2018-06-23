#coding: utf-8

import unittest
import os
from ..pyparseini import *

PATH = os.path.dirname(__file__)

class ParseIniTestCase(unittest.TestCase):
    def setUp(self):
        """
        master.host                 = stats10.mezimedia.com
        master.username             = scltrk
        master.password             = Mhok8homL7
        master.dbname               = tracking

        slave.host                  = stats11.mezimedia.com
        slave.port                  = 3308
        slave.username              = scltrk
        slave.password              = Mhok8homL7
        slave.dbname                = tracking

        msgsite                     = smarter
        offset                      = 20000
        """
        self.file = os.path.join(PATH, 'files/smus.ini')
        self.ini = parse_ini(self.file)

    def test_master(self):
        self.assertTrue('master' in self.ini)
        self.assertEqual(self.ini.master.host, 'stats10.mezimedia.com')
        self.assertEqual(self.ini.master.username, 'scltrk')
        self.assertEqual(self.ini.master.password, 'Mhok8homL7')
        self.assertEqual(self.ini.master.dbname, 'tracking')

    def test_slave(self):
        self.assertTrue('slave' in self.ini)
        self.assertEqual(self.ini.slave.host, 'stats11.mezimedia.com')
        self.assertEqual(self.ini.slave.port, 3308)
        self.assertEqual(self.ini.slave.username, 'scltrk')
        self.assertEqual(self.ini.slave.password, 'Mhok8homL7')
        self.assertEqual(self.ini.slave.dbname, 'tracking')

    def test_msgsite(self):
        self.assertEqual(self.ini.msgsite, 'smarter')

    def test_offset(self):
        self.assertEqual(self.ini.offset, 20000)

