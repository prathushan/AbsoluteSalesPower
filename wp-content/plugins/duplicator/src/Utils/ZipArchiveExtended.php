<?php

/**
 * @package Duplicator
 */

namespace Duplicator\Utils;

use Duplicator\Libs\Snap\SnapIO;
use Duplicator\Libs\Snap\SnapLog;
use Duplicator\Libs\Snap\SnapUtil;
use Exception;
use ZipArchive;

class ZipArchiveExtended
{
    /** @var string */
    protected $archivePath = '';
    /** @var ZipArchive */
    protected $zipArchive = null;
    /** @var bool */
    protected $isOpened = false;
    /** @var bool */
    protected $compressed = false;
    /** @var bool */
    protected $encrypt = false;
    /** @var string */
    protected $password = '';

    /**
     * Class constructor
     *
     * @param string $path zip archive path
     */
    public function __construct($path)
    {
        if (!self::isPhpZipAvailable()) {
            throw new Exception('ZipArchive PHP module is not installed/enabled.');
        }
        if (file_exists($path) && (!is_file($path) || !is_writeable($path))) {
            throw new Exception('File ' . SnapLog::v2str($path) . 'exists but isn\'t valid');
        }

        $this->archivePath = $path;
        $this->zipArchive  = new ZipArchive();
        $this->setCompressed(true);
    }

    /**
     * Class destructor
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * Check if class ZipArchvie is available
     *
     * @return bool
     */
    public static function isPhpZipAvailable()
    {
        return SnapUtil::classExists(ZipArchive::class);
    }

    /**
     * Add full dir in archive
     *
     * @param string $dirPath     dir path
     * @param string $archivePath local archive path
     *
     * @return bool TRUE on success or FALSE on failure.
     */
    public function addDir($dirPath, $archivePath)
    {
        if (!is_dir($dirPath) || !is_readable($dirPath)) {
            return false;
        }

        $dirPath     = SnapIO::safePathTrailingslashit($dirPath);
        $archivePath = SnapIO::safePathTrailingslashit($archivePath);
        $thisObj     = $this;

        return SnapIO::regexGlobCallback(
            $dirPath,
            function ($path) use ($dirPath, $archivePath, $thisObj) {
                $newPath = $archivePath . SnapIO::getRelativePath($path, $dirPath);

                if (is_dir($path)) {
                    $thisObj->addEmptyDir($newPath);
                } else {
                    $thisObj->addFile($path, $newPath);
                }
            },
            array('recursive' => true)
        );
    }

    /**
     * Add empty dir on zip archive
     *
     * @param string $path archive dir to add
     *
     * @return bool TRUE on success or FALSE on failure.
     */
    public function addEmptyDir($path)
    {
        return $this->zipArchive->addEmptyDir($path);
    }

    /**
     * Add file on zip archive
     *
     * @param string $filepath    file path
     * @param string $archivePath archive path, if empty use file name
     * @param int    $maxSize     max size of file, if the size is grater than this value the file will be truncated, 0 for no limit
     *
     * @return bool TRUE on success or FALSE on failure.
     */
    public function addFile($filepath, $archivePath = '', $maxSize = 0)
    {
        if (!is_file($filepath) || !is_readable($filepath)) {
            return false;
        }
        if (strlen($archivePath) === 0) {
            $archivePath = basename($filepath);
        }
        if ($maxSize > 0 && filesize($filepath) > $maxSize) {
            if (($content = file_get_contents($filepath, false, null, 0, $maxSize)) === false) {
                return false;
            }
            $result = $this->zipArchive->addFromString($archivePath, $content);
        } else {
            $result = $this->zipArchive->addFile($filepath, $archivePath);
        }
        if ($result && $this->encrypt) {
            $this->zipArchive->setEncryptionName($archivePath, ZipArchive::EM_AES_256);
        }
        if ($result && !$this->compressed) {
            $this->zipArchive->setCompressionName($archivePath, ZipArchive::CM_STORE);
        }
        return $result;
    }

    /**
     * Creates a temporary file with the given content. The file will be deleted after the zip is created
     *
     * @param string $archivePath Filename in archive
     * @param string $content     Content of the file
     * @param int    $maxSize     max size of file, if the size is grater than this value the file will be truncated, 0 for no limit
     *
     * @return bool returns true if the file was created successfully
     */
    public function addFileFromString($archivePath, $content, $maxSize = 0)
    {
        if ($maxSize > 0 && strlen($content) > $maxSize) {
            $content = substr($content, 0, $maxSize);
        }
        $result = $this->zipArchive->addFromString($archivePath, $content);
        if ($result && $this->encrypt) {
            $this->zipArchive->setEncryptionName($archivePath, ZipArchive::EM_AES_256);
        }
        if ($result && !$this->compressed) {
            $this->zipArchive->setCompressionName($archivePath, ZipArchive::CM_STORE);
        }
        return $result;
    }

    /**
     * Open Zip archive, create it if don't exists
     *
     * @return bool|int Returns TRUE on success or the error code. See zip archive
     */
    public function open()
    {
        if ($this->isOpened) {
            return true;
        }

        if (($result = $this->zipArchive->open($this->archivePath, ZipArchive::CREATE)) === true) {
            $this->isOpened = true;
            if ($this->encrypt) {
                $this->zipArchive->setPassword($this->password);
            } else {
                $this->zipArchive->setPassword('');
            }
        }
        return $result;
    }

    /**
     * Close zip archive
     *
     * @return bool True on success or false on failure.
     */
    public function close()
    {
        if (!$this->isOpened) {
            return true;
        }

        $result = false;

        if (($result = $this->zipArchive->close()) === true) {
            $this->isOpened = false;
        }

        return $result;
    }

    /**
     * Get num files in zip archive
     *
     * @return int
     */
    public function getNumFiles()
    {
        $this->open();
        return $this->zipArchive->numFiles;
    }

    /**
     * Get the value of compressed\
     *
     * @return bool
     */
    public function isCompressed()
    {
        return $this->compressed;
    }

    /**
     * Se compression if is available
     *
     * @param bool $compressed if true compress zip archive
     *
     * @return bool return compressd value
     */
    public function setCompressed($compressed)
    {
        if (!method_exists($this->zipArchive, 'setCompressionName')) {
            // If don't exists setCompressionName the archive can't create uncrompressed
            $this->compressed = true;
        } else {
            $this->compressed = $compressed;
        }
        return $this->compressed;
    }

    /**
     * Get the value of encrypt
     *
     * @return bool
     */
    public function isEncrypted()
    {
        return $this->encrypt;
    }

    /**
     * Return true if ZipArchive encryption is available
     *
     * @return bool
     */
    public static function isEncryptionAvaliable()
    {
        static $isEncryptAvailable = null;
        if ($isEncryptAvailable === null) {
            if (!self::isPhpZipAvailable()) {
                $isEncryptAvailable = false;
                return false;
            }

            $zipArchive = new ZipArchive();
            if (!method_exists($zipArchive, 'setEncryptionName')) {
                $isEncryptAvailable = false;
                return false;
            }

            if (version_compare(self::getLibzipVersion(), '1.2.0', '<')) {
                $isEncryptAvailable = false;
                return false;
            }

            $isEncryptAvailable = true;
        }

        return $isEncryptAvailable;
    }

    /**
     * Get libzip version
     *
     * @return string
     */
    public static function getLibzipVersion()
    {
        static $zlibVersion =  null;

        if (is_null($zlibVersion)) {
            ob_start();
            SnapUtil::phpinfo(INFO_MODULES);
            $info = (string) ob_get_clean();

            if (preg_match('/<td\s.*?>\s*(libzip.*\sver.+?)\s*<\/td>\s*<td\s.*?>\s*(.+?)\s*<\/td>/i', $info, $matches) !== 1) {
                $zlibVersion = "0";
            } else {
                $zlibVersion = $matches[2];
            }
        }

        return $zlibVersion;
    }

     /**
      * Set encryption
      *
      * @param bool   $encrypt  true if archvie must be encrypted
      * @param string $password password

      * @return bool
      */
    public function setEncrypt($encrypt, $password = '')
    {
        $this->encrypt = (self::isEncryptionAvaliable() ? $encrypt : false);

        if ($this->encrypt) {
            $this->password = $password;
        } else {
            $this->password = '';
        }

        if ($this->isOpened) {
            if ($this->encrypt) {
                $this->zipArchive->setPassword($this->password);
            } else {
                $this->zipArchive->setPassword('');
            }
        }

        return $this->encrypt;
    }

    /**
     * Files regex search and return zip file stat
     *
     * @param string $path     Archive path
     * @param string $regex    Regex to search
     * @param string $password Password if archive is encrypted or empty string
     *
     * @return false|array{name:string,index:int,crc:int,size:int,mtime:int,comp_size:int,comp_method:int}
     */
    public static function searchRegex($path, $regex, $password = '')
    {
        if (!self::isPhpZipAvailable()) {
            throw new Exception(__('ZipArchive PHP module is not installed/enabled. The current Backup cannot be opened.', 'duplicator'));
        }

        $zip = new ZipArchive();
        if ($zip->open($path) !== true) {
            throw new Exception('Cannot open the ZipArchive file.  Please see the online FAQ\'s for additional help.' . $path);
        }

        if (strlen($password)) {
            $zip->setPassword($password);
        }

        $result = false;
        for ($i = 0; $i < $zip->numFiles; $i++) {
            /** @var array{name:string,index:int,crc:int,size:int,mtime:int,comp_size:int,comp_method:int} */
            $stat = $zip->statIndex($i);
            $name = basename($stat['name']);
            if (preg_match($regex, $name) === 1) {
                $result = $stat;
                break;
            }
        }

        $zip->close();
        return $result;
    }
}
