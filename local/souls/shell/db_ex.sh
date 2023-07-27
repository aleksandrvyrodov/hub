#!/bin/bash

MIT_DB_USER=""
MIT_DB_NAME=""
MIT_DB_PASS=""

MIT_DB_DUMP=""

mysqldump -u $MIT_DB_USER -p$MIT_DB_PASS $MIT_DB_NAME > ${MIT_DB_DUMP}/.mit/.souls/dump.sql