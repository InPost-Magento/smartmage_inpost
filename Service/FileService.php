<?php
namespace Smartmage\Inpost\Service;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Smartmage\Inpost\Model\Config\Source\LabelFormat;

class FileService
{
    /**
     * @var FileFactory
     */
    protected $fileFactory;
    private $directoryList;
    private $dateTime;

    public function __construct(
        FileFactory   $fileFactory,
        DirectoryList $directoryList,
        DateTime $dateTime
    ) {
        $this->fileFactory = $fileFactory;
        $this->directoryList = $directoryList;
        $this->dateTime = $dateTime;
    }

    public function generateFile($fileContent, $fileFormat, $fileContentType)
    {
        return $this->fileFactory->create(
            $this->createFilename($fileFormat),
            $this->getFileContentArray($fileContent, $fileContentType),
            DirectoryList::VAR_DIR,
            LabelFormat::LABEL_CONTENT_TYPES[$fileFormat]
        );
    }

    public function createZip(array $files, $filesFormat): string
    {
        $zip = new \ZipArchive();
        $zipFileName = $this->createFilename('zip');
        $zipFilePath = $this->directoryList->getPath(DirectoryList::VAR_DIR) . '/' . $zipFileName;

        if ($zip->open($zipFilePath, \ZipArchive::CREATE) === TRUE) {
            foreach ($files as $index => $fileContent) {
                $zip->addFromString(sprintf('label-%d.' . $filesFormat, $index + 1), $fileContent);
            }
            $zip->close();
        } else {
            throw new \Exception('Cannot create a ZIP file.');
        }

        return $zipFilePath;
    }

    private function getFileContentArray($fileContent, $fileContentType)
    {
        return ['type' => $fileContentType, 'value' => $fileContent, 'rm' => false];
    }

    private function createFilename($format)
    {
        return sprintf('labels-%s.' . $format, $this->dateTime->date('Y-m-d_H-i-s'));
    }
}
