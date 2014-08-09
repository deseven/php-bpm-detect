## php-bpm-detect
a php class for bpm detection

## dependencies
php 5.2 or higher  
[soundtouch](http://www.surina.net/soundtouch/)  
sox  
mpg123  
ffmpeg (optional, can be used instead of sox and mpg123)  

## compatibility
tested on php 5.3.3, sox 14.2.0, mpg123 1.20.1, ffmpeg 0.6.5, soundtouch 1.8

## basic usage
important: **run the `test-dep.php` to test the dependencies**
```php
$bpm_detect = new bpm_detect("file.mp3");  
echo $bpm_detect->detectBPM();
```

## advanced usage
check the included test.php
