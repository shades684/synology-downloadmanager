<?php

namespace Lib\Testing;

use Lib\Movie;
use Lib\XBMC;

class Test
{
    public function run()
    {
        $this->testMovieRenaming();
        $this->testUpdateXBMC();
    }

    public function testMovieRenaming()
    {
        echo "testMovieRenaming\n";

        $names = array(
            "Awful Nice 2013 WEBRip 480p X264 AAC-VYTO [P2PDL] [P2PDL]",
            "Craig.Ferguson.Im.Here.To.Help.2013.DVDRip.x264-GUFFAW",
            "Ender's Game 2013 BluRay 720p DTS x264-MgB [ETRG]",
            "Mr. And Mrs. Smith 2005 Dir Cut BluRay 720p DTS x264-3Li",
            "Mr.+And+Mrs.+Smith+2005+Dir+Cut+BluRay+720p+DTS+x264-3Li",
            "Thor The Dark World [2013] 1080p BluRay AAC x264-tomcat12[ETRG]"
        );

        foreach ($names as $name) {
            $movie = new Movie();
            $movie->setFileName($name);

            echo $movie->getDirectoryName() . "\n";
        }
    }

    public function testUpdateXBMC()
    {
        echo "testUpdateXBMC\n";

        try {
            $xbmc = new XBMC();
            $xbmc->setUpdateMovies(true);
            $xbmc->update();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}