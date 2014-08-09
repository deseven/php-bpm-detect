## php-bpm-detect
a php class for bpm detection

## dependencies
php 5.2 or higher  
[soundtouch](http://www.surina.net/soundtouch/)  
sox  
mpg123  
ffmpeg (optional, can be used instead of sox and mpg123)

## dependencies installation
fedora-based: `yum install soundtouch sox mpg123 ffmpeg`  
debian-based: `apt-get install soundstretch sox mpg123 ffmpeg`  
windows: sorry, you'll have to figure it out yourself :)

**important:** run the `test-dep.php` to test the dependencies

## compatibility
tested on CentOS 6 with php 5.3.3, sox 14.2.0, mpg123 1.20.1, ffmpeg 0.6.5, soundtouch 1.8  
tested on Debian 7 with php 5.4.4, sox 14.4.0, mpg123 1.14.4, ffmpeg 0.8.13, soundtouch 1.6

## basic usage
```php
$bpm_detect = new bpm_detect("file.mp3");  
echo $bpm_detect->detectBPM();
```

## advanced usage
check the included test.php
