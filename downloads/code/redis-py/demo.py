#coding: utf-8

import redis
import random
import string


def gen_random_string(length=10, chars=string.ascii_letters+string.digits):
    """
    生成指定长度的随机字符串
    """
    return "".join([random.choice(chars) for x in range(length)])


r = redis.Redis(db=1)
r.flushdb()

data = dict(
    caiknife=1,
    cai=2,
    zhi=3,
    jiang=4,
    knife=5,
    test=6
)

r.mset(data)

print r.mget(['cai', 'caiknife'])
"""
获得key=cai和key=caiknife的值
['2', '1']
"""

print r.keys('*cai*') 
"""
匹配包含cai的key
['cai', 'caiknife']
"""

print r.keys('c??')
"""
匹配以c开头，长度为3的key
['cai']
"""

print r.keys('k[n]*')
"""
匹配k开头，包含0个或者多个n的key
['knife']
"""

print r.keys('*')
"""
匹配所有key
['zhi', 'test', 'jiang', 'knife', 'cai', 'caiknife']
"""

r.flushdb()

"""
生成10个随机数据 id    name    score
"""
data = [dict(id=x, score=random.randint(0, 100), name=gen_random_string()) for x in range(1, 11)]

for d in data:
    r.lpush('id', d['id'])
    r.set('name:%d' % d['id'], d['name'])
    r.set('score:%d' % d['id'], d['score'])


for d in sorted(data, key=lambda x: x['score']):
    print d
"""
按照score进行升序排序
{'score': 7, 'name': 'Uir8Pfo27c', 'id': 2}
{'score': 17, 'name': 'oFhLHSu42X', 'id': 7}
{'score': 19, 'name': 'C3DWCos4wq', 'id': 10}
{'score': 56, 'name': 'I7JRymyPJ5', 'id': 9}
{'score': 58, 'name': 'gJPkpD1TGn', 'id': 6}
{'score': 63, 'name': 'Vzt7gy2349', 'id': 1}
{'score': 75, 'name': 'eRHfRbeWrW', 'id': 4}
{'score': 77, 'name': 'Kja64ofoP1', 'id': 3}
{'score': 85, 'name': 'VRRPhp5Vmz', 'id': 8}
{'score': 94, 'name': 'm5mKg1s7Ji', 'id': 5}
"""

print r.sort('id', by='score:*')
"""
根据score获得排序后的id
['2', '7', '10', '9', '6', '1', '4', '3', '8', '5']
"""

print r.sort('id', by='score:*', get=['#', 'name:*', 'score:*'])
"""
根据score获得排序后的id, name, score, 返回的是一个N*M的数组，N是id的数量，M是GET中所取属性的数量
['2', '7', '10', '9', '6', '1', '4', '3', '8', '5']
['2', 'Uir8Pfo27c', '7', 
 '7', 'oFhLHSu42X', '17', 
 '10', 'C3DWCos4wq', '19', 
 '9', 'I7JRymyPJ5', '56', 
 '6', 'gJPkpD1TGn', '58', 
 '1', 'Vzt7gy2349', '63', 
 '4', 'eRHfRbeWrW', '75', 
 '3', 'Kja64ofoP1', '77', 
 '8', 'VRRPhp5Vmz', '85', 
 '5', 'm5mKg1s7Ji', '94']
"""