#!/usr/bin/python3

import os
import subprocess

def check_repository_status(path):
    os.chdir(path)

    result = subprocess.run(['git', 'status', '--porcelain'], stdout=subprocess.PIPE, stderr=subprocess.DEVNULL, encoding='utf-8')

    if result.returncode != 0:
        return 2

    output = result.stdout.strip()

    if not output:
        return 1
    else:
        return commit_changes()


def commit_changes():
    result = subprocess.run(['git', 'add', '-A'], stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL)
    if result.returncode != 0:
        return 3
    result = subprocess.run(['git', 'commit', '-m', '<[US-CR|EvD]'], stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL)
    if result.returncode != 0:
        return 4
    return 1


if not os.path.exists('/home/bitrix/ext_www/carolinashop.ru/dev/!mit'):
  result = check_repository_status('/home/bitrix/ext_www/carolinashop.ru')
  print(result)
else:
  print(0)