#!/usr/bin/env python3
# -*- coding: utf-8 -*-
import shutil
import sys

path = '/opt/kitos_tools/settings/settings.json'

if(sys.argv[1] == "web"):
    shutil.chown(path, user='www-data', group='www-data')
elif sys.argv[1] == "kitos":
    shutil.chown(path, user='root', group='root')
