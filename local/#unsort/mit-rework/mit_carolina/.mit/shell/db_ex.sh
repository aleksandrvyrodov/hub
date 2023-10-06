#!/bin/bash

MIT_DB_USER="carolinashop_user_4cT8"
MIT_DB_PASS="rL8)vy=hDoCC"
MIT_DB_NAME="carolinashop_db"

MIT_DB_DUMP="/home/bitrix/ext_www/carolinashop.ru/.mit/.term"

mysqldump -u $MIT_DB_USER -p$MIT_DB_PASS $MIT_DB_NAME > ${MIT_DB_DUMP}/dump.sql