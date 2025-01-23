<?php
namespace DocumentTools;
class DocumentToolsClient
{
    /**
     * Represents the URL of the API where the data will be retrieved from.
     */
    private string $url;
    /**
     * Represents the key used for authentication or access control.
     */
    private string $key;

    /**
     * Constructor for initializing the class with URL and key values.
     *
     * @param string $url The base URL for the API requests.
     * @param string $key The key to be used for authentication.
     */
    public function __construct(string $url, string $key)
    {
        $this->url = (str_ends_with($url, '/')) ? $url : $url .'/';
        $this->key = $key;
    }

    /**
     * Retrieves the URL associated with the current instance.
     *
     * @return string The URL associated with the current instance.
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Returns the key stored in the object.
     *
     * @return string The key stored in the object.
     */
    public function getKey():string
    {
        return $this->key;
    }

    /**
     * Makes an HTTP request to the specified path using cURL.
     *
     * @param string $path The path for the HTTP request.
     * @param string $method The HTTP method to use (default is 'GET').
     * @param array $headers The headers to include in the request.
     * @param mixed $payload The data payload to send with the request.
     * @return false|DocumentToolsResponse Returns a DocumentToolsResponse object if successful, false otherwise.
     */
    public function request(string $path, string $method = 'GET', $headers = [], $payload = null): false|DocumentToolsResponse
    {
        try {
            if (!array_key_exists('Content-Type', $headers)) $headers['Content-Type'] = 'application/json';
            ///
            /// If the paths is a full http request
            if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
                $ch = curl_init($path);
            } else {
                $ch = curl_init($this->url . ((str_starts_with($path, '/')) ? substr($path,1) : $path)); // Initialise cURL
            }
            ///
            /// Add the key
            ///
            if(!array_key_exists('X-Auth-Token', $headers)){
                $headers['X-Auth-Token'] = $this->key;
            }

            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // Inject the token into the header
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($ch, CURLOPT_FRESH_CONNECT, TRUE);

            ///
            /// Add the payload
            ///
            if (!empty($payload)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            }

            curl_setopt($ch, CURLOPT_ACCEPTTIMEOUT_MS, 600000);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, 600000);

            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

            $result = curl_exec($ch); // Execute the cURL statement

            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            curl_close($ch);

            return new DocumentToolsResponse($httpcode, $result);
        } catch (\Exception $ex) {
            error_log("cUrl Exception: " . $ex->getMessage() . ': ' . $ex->getLine() . '. File: ' . $ex->getFile());
            return false;
        }
    }
}