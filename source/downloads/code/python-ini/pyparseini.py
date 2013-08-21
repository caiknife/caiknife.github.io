#coding: utf-8

from collections import OrderedDict
from copy import deepcopy
from ast import literal_eval
import re

__all__ = ['parse_ini']

default = '__default__'

# [ production ]
REGEX_SEC = re.compile('\[\s?([\w]+)\s?\]', re.IGNORECASE)
# [ dev : production ]
REGEX_I_SEC = re.compile('\[\s?([\w]+)\s?:\s?([\w]+)\s?\]', re.IGNORECASE)

def parse_ini(ini_path, env=None):
    ini = _Obj({default: _Obj()})
    current_section = default

    with open(ini_path) as f:
        for line in f:
            line = line.strip()
            # empty line or a comment line
            if not line or line.isspace() or line[0]==';' or line[0]=='#':
                continue

            # section
            if line[0]=='[':
                # a common section 
                result = REGEX_SEC.search(line)
                if result is not None:
                    section = result.group(1)
                    ini[section] = deepcopy(ini[default])
                else:
                    # a inherited section 
                    result = REGEX_I_SEC.search(line)
                    if result in None:
                        raise SyntaxError("Invalid section declaration")
                    section = result.group(1)
                    parent = result.group(2)

                    # wrong parent section
                    if parent not in ini:
                        raise MissingSectionError("'%s' inherits from '%s' which hasn't been declared." % (section, parent))
                    ini[section] = deepcopy(ini[parent])

                current_section = section

            # option
            else:
                pieces = line.split("=")
                # master.foo.bar
                vals = pieces[0].strip().split('.')
                # ['bar', 'foo', 'master']
                vals.reverse()
                data = _cast(pieces[1].strip())
                working_obj = ini[current_section]

                while vals:
                    if len(vals)==1:
                        working_obj[vals.pop()] = data
                    else:
                        val = vals.pop()
                        if val not in working_obj:
                            working_obj[val] = _Obj()
                        working_obj = working_obj[val]

        if env is not None:
            if env not in ini:
                raise MissingSectionError("The section being loaded does not exist.")
            return ini[env]

        return ini

def _cast(val):
    try:
        val = literal_eval(val)
    except:
        pass
    return val


class _Obj(OrderedDict):
    """
    A dict that allows for object-like property access syntax.
    """
    def __copy__(self):
        data = self.__dict__.copy()
        return _Obj(data)

    def __getattr__(self, name):
        try:
            return self[name]
        except KeyError:
            try:
                return self[default][name]
            except KeyError:
                raise AttributeError(name)

    def __contains__(self, name):
        try:
            self.__getattr__(name)
        except AttributeError:
            return False
        return True     


class MissingSectionError(Exception):
    """
    Thrown when a section header inherits from a section that has yet been undeclared.
    """
    pass