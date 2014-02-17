#!/bin/ash

rm -rf /usr/syno/synoman/phpsrc/DownloadManager/

mkdir /usr/syno/synoman/phpsrc/DownloadManager/
chmod guo+rwx /usr/syno/synoman/phpsrc/DownloadManager/

cp -r * /usr/syno/synoman/phpsrc/DownloadManager/
chmod guo+x /usr/syno/synoman/phpsrc/DownloadManager/main.php

/volume1/@appstore/DownloadStation/scripts/S25download.sh stop

sed -i 's|"script-torrent-done-enabled": false|"script-torrent-done-enabled": true|
s|"script-torrent-done-filename": "",|"script-torrent-done-filename": "/usr/syno/synoman/phpsrc/DownloadManager/main.php",|
' /usr/syno/etc/packages/DownloadStation/download/settings.json

/volume1/@appstore/DownloadStation/scripts/S25download.sh start
