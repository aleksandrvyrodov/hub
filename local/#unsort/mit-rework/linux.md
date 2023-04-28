# Linux Command

## Копирование файла с сервера на сервер

scp - [losst.pro](https://losst.pro/kopirovanie-fajlov-scp)

```bash
scp [pathfile] [user]@[host]:[path]
```

---

## Занятое место

### Устройства

```bash
df -h
```

---

### Директории

```bash
du [pathfolder] -hd 1
```

### MySQL dump

Создать:

```bash
mysqldump -u [user] -p [dbase] > mydatabase_dump.sql
```

Загрузить:

```bash
mysql -u [user] -p [dbase] < mydatabase_dump.sql
```

---

