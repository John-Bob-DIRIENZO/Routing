<?php

namespace App\Core\Entity;

use Cocur\Slugify\Slugify;

class UploadedFile
{
    private string $fileName;
    private string $MimeType;
    private string $tempPath;
    private int $size;
    private ?string $safeName = null;

    public function __construct(string $fileName, string $MimeType, string $tempPath, int $size)
    {
        $this->fileName = $fileName;
        $this->MimeType = $MimeType;
        $this->tempPath = $tempPath;
        $this->size = $size;
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * @return string
     */
    public function getMimeType(): string
    {
        return $this->MimeType;
    }

    /**
     * @return string
     */
    public function getTempPath(): string
    {
        return $this->tempPath;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @return bool
     */
    public function isImage(): bool
    {
        return getimagesize($this->tempPath) !== false;
    }

    /**
     * @return string
     */
    public function getSafeName(): string
    {
        if ($this->safeName !== null) {
            return $this->safeName;
        }
        $slugify = new Slugify();
        $this->safeName = uniqid() . $slugify->slugify($this->getFileName()) . $this->getExtension();
        return $this->safeName;
    }

    /**
     * @return string
     */
    public function getRealMimeType(): string
    {
        return mime_content_type($this->getTempPath());
    }

    /**
     * @return string
     */
    public function getExtension(): string
    {
        if (empty($this->getRealMimeType())) return '';
        switch ($this->getRealMimeType()) {
            case 'image/bmp':
                return '.bmp';
            case 'image/cis-cod':
                return '.cod';
            case 'image/gif':
                return '.gif';
            case 'image/ief':
                return '.ief';
            case 'image/jpeg':
                return '.jpg';
            case 'image/pipeg':
                return '.jfif';
            case 'image/tiff':
                return '.tif';
            case 'image/x-cmu-raster':
                return '.ras';
            case 'image/x-cmx':
                return '.cmx';
            case 'image/x-icon':
                return '.ico';
            case 'image/x-portable-anymap':
                return '.pnm';
            case 'image/x-portable-bitmap':
                return '.pbm';
            case 'image/x-portable-graymap':
                return '.pgm';
            case 'image/x-portable-pixmap':
                return '.ppm';
            case 'image/x-rgb':
                return '.rgb';
            case 'image/x-xbitmap':
                return '.xbm';
            case 'image/x-xpixmap':
                return '.xpm';
            case 'image/x-xwindowdump':
                return '.xwd';
            case 'image/png':
                return '.png';
            case 'image/x-jps':
                return '.jps';
            case 'image/x-freehand':
                return '.fh';
            default:
                return '';
        }
    }
}