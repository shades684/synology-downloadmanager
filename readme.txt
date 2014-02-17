Download Manager

This program triggers when a download is finished in Synology Download Station
It gets the completed downloads and places the media file(s) in a destination directory (per media type) renamed to a logical name.
Non necessary aren't copied.
Optionally
    The download is removed from Synology Download Station
    The download directory is removed
    XBMC is notified that new movies and/or music has been added.

INSTALL:
    Change config.json to your wishes
    Add directories to open_basedir in the Webservices - PHP settings of the configuration screen (/volume1/video, /volume1/music and /volume1/downloads for instance)
    run setup.sh

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
