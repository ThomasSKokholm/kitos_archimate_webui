#!/usr/bin/env python3
import json
import pathlib

from kitos_helper.kitos_helper import KitosHelper
import kitos_helper.kitos_logger as kl

cfg_file = pathlib.Path.cwd() / 'settings' / 'settings.json'
if not cfg_file.is_file():
    raise Exception('No setting file')

SETTINGS = json.loads(cfg_file.read_text(encoding='utf-8'))

kh = KitosHelper(SETTINGS['KITOS_USER'], SETTINGS['KITOS_PASSWORD'], SETTINGS['KITOS_URL'], False, False)

def verify_kitos_login():
    """
    Verifies KITOS user login information
    """
    print(kh.token)

if __name__ == '__main__':
    verify_kitos_login()