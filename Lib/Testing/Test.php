<?php

namespace Lib\Testing;

use Lib\Download;
use Lib\Movie;
use Lib\UpdateContext;

class Test
{
    public function run()
    {
        $this->testDb();
        $this->testMovieRenaming();
    }

    public function testDb()
    {
        foreach(Download::getCompleted() as $download)
        {
            echo $download->getFileName(). "\n";
        }
    }

    public function testMovieRenaming()
    {
        echo "testMovieRenaming\n";

        $names = array(
            "A Test 2001 WEBRip 480p X264 AAB-CCD [ARG1] [ARG2]",
            "A.Test.With.Dots.2002.DVDRip.x264-AAB",
            "Aren't There Test with quotes 2003 BluRay 720p DTS x264-AAB [AAC]",
            "Dot. And Dot. Test 2004 Dir Cut BluRay 720p DTS x264-4YOU",
            "Dot.+And+Dot.+Test+2005+Dir+Cut+BluRay+720p+DTS+x264-4YOU",
            "A Test With A Different Year Block [2006] 1080p BluRay AAC x264-aab [ARG1]"
        );

        foreach ($names as $name) {
            $movie = new Movie();
            $movie->setFileName($name);

            echo $movie->getTargetDirectory() . "\n";
        }
    }
}