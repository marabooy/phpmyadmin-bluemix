<?php


function copy_directory($src, $dst)
{
    $dir = opendir($src);
    @mkdir($dst);
    while (false !== ($file = readdir($dir))) {
        if (($file != '.') && ($file != '..')) {
            if (is_dir($src . '/' . $file)) {
                copy_directory($src . '/' . $file, $dst . '/' . $file);
            } else {
                copy($src . '/' . $file, $dst . '/' . $file);
            }
        }
    }
    closedir($dir);
}

function remove_dirs($dir)
{
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );

    foreach ($files as $fileinfo) {
        $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
        $todo($fileinfo->getRealPath());
    }

    rmdir($dir);
}

$options = json_decode(file_get_contents(__DIR__ . '/options.json'), true);


$myadminFile = __DIR__ . '/myadmin.zip';

$fp = fopen($myadminFile, 'w+');//This is the file where we save the    information
$ch = curl_init(str_replace(" ", "%20", $options['url']));//Here is the file we are downloading, replace spaces with %20
curl_setopt($ch, CURLOPT_TIMEOUT, 50);
curl_setopt($ch, CURLOPT_FILE, $fp); // write curl response to file
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_exec($ch); // get curl response
curl_close($ch);

fclose($fp);


$md5hash = md5_file($myadminFile);

if ($md5hash === $options['hash']) {

    echo "extracting to the required path..." . PHP_EOL;

    $zipArchive = new ZipArchive();
    $zipArchive->open($myadminFile);
    $zipArchive->extractTo(__DIR__ . '/tmp');
    $dirs = array_diff(scandir(__DIR__ . '/tmp'), array('.', '..'));
    $dirs = array_values($dirs);
    $parentFolder = $dirs[0];

    echo "copying to deploy directory..." . PHP_EOL;
    copy_directory(__DIR__ . '/tmp/' . $parentFolder, __DIR__ . '/htdocs');

    echo "cleaning up after myself" . PHP_EOL;
    remove_dirs(__DIR__ . '/tmp/');
    echo "copying config" . PHP_EOL;

    copy_directory(__DIR__ . '/config', __DIR__ . '/htdocs');
    echo "done..." . PHP_EOL;
} else {
    echo "failed to deploy. the hash does not match the expected md5 hash .. someone is trying to dupe you! ..";
    exit(-1);
}


