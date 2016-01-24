<?php

require "class.bpm.php";

echo "detecting bpm for the file test.mp3 with no predefined length using ffmpeg:\n";
$bpm_detect = new bpm_detect("test.mp3",DETECT_LENGTH,USE_FFMPEG);
echo "track length is ".$bpm_detect->getLength()." sec\n";
echo "average track bpm is ".$bpm_detect->detectBPM()."\n";
echo "track bpm by 16 seconds is ".json_encode($bpm_detect->detectBPM(SPLIT),JSON_NUMERIC_CHECK)."\n";
echo "track bpm by 4 seconds is ".json_encode($bpm_detect->detectBPM(SPLIT,4),JSON_NUMERIC_CHECK)."\n";
echo "total processing time is ".$bpm_detect->getProcessingTime()." sec \n";
//echo "used commands: ";
//var_dump($bpm_detect->getCmdLog());

echo "\n";

echo "detecting bpm for the file test.mp3 with no predefined length using sox and mpg123:\n";
$bpm_detect = new bpm_detect("test.mp3",DETECT_LENGTH,USE_SOX);
echo "track length is ".$bpm_detect->getLength()." sec\n";
echo "average track bpm is ".$bpm_detect->detectBPM()."\n";
echo "track bpm by 16 seconds is ".json_encode($bpm_detect->detectBPM(SPLIT),JSON_NUMERIC_CHECK)."\n";
echo "track bpm by 4 seconds is ".json_encode($bpm_detect->detectBPM(SPLIT,4),JSON_NUMERIC_CHECK)."\n";
echo "total processing time is ".$bpm_detect->getProcessingTime()." sec \n";
//echo "used commands: ";
//var_dump($bpm_detect->getCmdLog());

?>