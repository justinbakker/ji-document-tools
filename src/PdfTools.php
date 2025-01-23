<?php
namespace DocumentTools;
class PdfTools
{
    private DocumentToolsClient $client;

    public function __construct(DocumentToolsClient $client)
    {
        $this->client = $client;
    }

    /**
     * Get an array of images from a PDF file.
     *
     * @param string $path The file path of the PDF.
     * @param int $resolution The resolution of the images (default 300).
     * @param string $output The output image format (default 'png').
     *
     * @return false|DocumentToolsResponse False if file not found or not a PDF, DocumentToolsResponse on success.
     */
    public function getImageArrayFromPath(string $path, int $resolution = 300, string $output = 'png'): false|DocumentToolsResponse
    {
        if(!file_exists($path)) {
            throw new Exception('File not found in path: ' . $path);
        }

        if (!$this->isPDF($path)) {
           throw new Exception('File is not PDF: ' . $path);
        }

        $content = file_get_contents($path);

        return $this->getImageArrayFromString($content, $resolution, $output);
    }

    /**
     * Get an array of images from a PDF data string.
     *
     * @param string $data The PDF data string to extract images from.
     * @param int $resolution The resolution of the images (default 300).
     * @param string $output The output image format (default 'png').
     *
     * @return false|DocumentToolsResponse False if data is empty or invalid, DocumentToolsResponse on success.
     */
    public function getImageArrayFromString(string $data, int $resolution = 300, string $output = 'png'): false|DocumentToolsResponse
    {
        $postObj = new stdClass();
        $postObj->file = new stdClass();
        $postObj->file->data = base64_encode($data);
        $postObj->file->mime = "@file/pdf";
        $postObj->resolution = $resolution;
        $postObj->output = $output;

        return $this->client->request('/pdf/images', 'POST', [], json_encode($postObj));
    }

    /**
     * Retrieves a thumbnail image from the provided file path.
     *
     * @param string $path The path to the file for which the thumbnail is needed
     * @param int $resolution The resolution of the thumbnail image (default: 300)
     * @param string $output The output format of the thumbnail image (default: 'png')
     *
     * @return false|DocumentToolsResponse Returns false if the file is not found or is not a PDF, otherwise returns the thumbnail image as DocumentToolsResponse
     */
    public function getThumbnailFromPath(string $path, int $resolution = 300, string $output = 'png'): false|DocumentToolsResponse
    {
        if(!file_exists($path)) {
            throw new Exception('File not found in path: ' . $path);
        }

        if (!$this->isPDF($path)) {
            throw new Exception('File is not PDF: ' . $path);
        }

        $content = file_get_contents($path);

        return $this->getThumbnailFromString($content, $resolution, $output);
    }

    /**
     * Retrieves a thumbnail image from a given string data with optional parameters.
     *
     * @param string $data The input string data to generate the thumbnail from.
     * @param int $resolution The resolution of the generated thumbnail (default is 25).
     * @param string $output The output format of the thumbnail (default is 'png').
     *
     * @return false|DocumentToolsResponse Returns false if an error occurs during the thumbnail generation process,
     *         otherwise returns a DocumentToolsResponse object containing the generated thumbnail image.
     */
    public function getThumbnailFromString(string $data, int $resolution = 25, string $output = 'png'): false|DocumentToolsResponse
    {
        $postObj = new stdClass();
        $postObj->file = new stdClass();
        $postObj->file->data = base64_encode($data);
        $postObj->file->mime = "@file/pdf";
        $postObj->resolution = $resolution;
        $postObj->output = $output;

        return $this->client->request('/pdf/thumbnail', 'POST', [], json_encode($postObj));
    }

    public function getOcrFromPath(string $path): false|DocumentToolsResponse
    {
        if(!file_exists($path)) {
            throw new Exception('File not found in path: ' . $path);
        }

        if (!$this->isPDF($path)) {
            throw new Exception('File is not PDF: ' . $path);
        }

        $content = file_get_contents($path);

        return $this->getOcrFromString($content);
    }

    public function getOcrFromString(string $data): false|DocumentToolsResponse
    {
        $postObj = new stdClass();
        $postObj->file = new stdClass();
        $postObj->file->data = base64_encode($data);
        $postObj->file->mime = "@file/pdf";

        return $this->client->request('/pdf/ocr', 'POST', [], json_encode($postObj));
    }


    /**
     * Checks if the file at the given filePath is a PDF file.
     *
     * @param string $filePath The path to the file to check.
     * @return bool True if the file is a PDF, false otherwise.
     */
    private function isPDF($filePath): bool
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE); // Returns a resource for fileinfo
        $mime = finfo_file($finfo, $filePath); // Get the mime type
        finfo_close($finfo); // Close the resource
        if ($mime == 'application/pdf') {
            return true;
        } else {
            return false;
        }
    }
}