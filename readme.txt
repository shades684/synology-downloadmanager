Download Manager

This program triggers when a download is finished in Synology Download Station
It gets the completed downloads and places the media file(s) in a destination directory (per media type) renamed to a logical name.
Non necessary aren't copied.
Optionally
    The download is removed from Synology Download Station
    The download directory is removed
    XBMC is notified that new movies and/or music has been added.

INSTALL:
     PHP scripts can only access the internal database if they're placed in /usr/syno/synoman/phpsrc a good place would be /usr/syno/synoman/phpsrc/DownloadManager/
     don't forget to make the main.php executable chmod guo+x main.php
     change config.json to your wishes

     Add target directories to open_basedir in the Webservices - PHP settings of the configuration screen

     stop download station
        /volume1/@appstore/DownloadStation/scripts/S25download.sh stop
     edit /usr/syno/etc/packages/DownloadStation/download/settings.json
        set "script-torrent-done-enabled" to true
        set "script-torrent-done-filename" to /usr/syno/synoman/phpsrc/DownloadManager/main.php
     start download station
        /volume1/@appstore/DownloadStation/scripts/S25download.sh start

FAQ:
    Does the download in /volume1/@download/task_id get removed? : YES (though not immediately)

TODO:
    Implement Archive
    Implement Music
    Implement TVShows
    Movie
        What to do if we already downloaded a specific movie (now if the directory exists we do nothing)?
    Might be nice to wrap this up in an spk (so we can see logging in the GUI, and do the install automatically)

THANKS:
    Niels Keurentjes (Configuration, WebClient and URL Classes, which are part of the Omines-CMF, http://www.omines.com)
    Honza Kuchař (BigFileTools, https://github.com/jkuchar/BigFileTools/blob/master/class/BigFileTools.php)
    Maurice from Kruchten (Who gave me the original idea and pointed out the option for triggering in Synology Download Station,
                            https://www.gitorious.org/synology/synology-script)
