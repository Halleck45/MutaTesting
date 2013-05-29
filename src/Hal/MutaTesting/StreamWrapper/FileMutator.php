<?php

namespace Hal\MutaTesting\StreamWrapper;

class FileMutator
{

    private static $FILES_TO_MUTATE = array();
    private $hwnd;

    public static function initialize()
    {
        if (in_array("file", stream_get_wrappers())) {
            stream_wrapper_unregister("file");
        }
        stream_wrapper_register("file", __CLASS__);
    }

    public static function addMutatedFile($originalFile, $mockedFile)
    {
        self::$FILES_TO_MUTATE[$originalFile] = $mockedFile;
    }

    public function stream_open($path, $mode, $options, &$opened_path)
    {
        // avoid to load files listed in "self::$FILES_TO_MUTATE"
        if (in_array($path, self::$FILES_TO_MUTATE)) {
            $path = self::$FILES_TO_MUTATE[$path];
        }

        stream_wrapper_restore('file');
        $this->hwnd = fopen($path, $mode, $options);
        stream_wrapper_unregister("file");
        stream_wrapper_register("file", get_class($this));
        return !empty($this->hwnd);
    }

    public function stream_read($count)
    {
        return fread($this->hwnd, $count);
    }

    public function stream_write($content)
    {
        return fwrite($this->hwnd, $content);
    }

    public function stream_eof()
    {
        return feof($this->hwnd);
    }

    public function stream_stat()
    {
        return fstat($this->hwnd);
    }

    public function url_stat()
    {
//        return fstat($this->hwnd);
    }

    public function dir_opendir($path, $options)
    {
        $this->hwnd = opendir($path);
        return true;
    }

    public function dir_readdir()
    {
        return readdir($this->hwnd);
    }

    public function mkdir($path, $mode = 0777, $recursive = false, $context = null)
    {
        stream_wrapper_restore('file');
        if (!file_exists($path)) {
            mkdir($path, $mode, $recursive);
        }
        stream_wrapper_unregister("file");
        stream_wrapper_register("file", get_class($this));
        return true;
    }

}

