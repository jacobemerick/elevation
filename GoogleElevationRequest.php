<?php

/**
 * This is the main class for Google Elevation API wrappper
 * For licensing and examples:
 *
 * @see https://github.com/jacobemerick/elevation
 *
 * @author jacobemerick (http://home.jacobemerick.com/)
 * @version 1.0 (2013-11-21)
 */

class GoogleElevationRequest
{

    /**
     * URL endpoint for API requests
     */
    protected static $URL = 'http://maps.googleapis.com/maps/api/elevation/%s';

    /**
     * Flag on whether or not the requesting device has a sensor
     * Note: the 'sensor' parameter is a required field for the API
     */
    protected $has_sensor = false;

    /**
     * Array to hold onto coordinates
     * Coordinates will be saved as lat/long pairs
     */
    protected $coordinate_array = array();

    /**
     * Placeholder construct - we don't need anything instantiated
     */
    public function __construct() {}

    /**
     * Add a new coordinate pair to the coordinate array
     *
     * @param   float   $latitude   latitude in decimal format
     * @param   flat    $longitude  longitude in decimal format
     */
    public function addCoordinate($latitude, $longitude)
    {
        $coordinate = array($latitude, $longitude);
        array_push($this->coordinate_array, $coordinate);
    }

    /**
     * Format the URL endpoint with all the parameters
     * Note: this does not validate the parameters
     * @url https://developers.google.com/maps/documentation/elevation/#ElevationRequests
     *
     * @param   string  $output_format  google output format (currently json or xml)
     * @return  string  full url endpoint for the request
     */
    protected function buildURL($output_format)
    {
        $query = array(
            'locations' => $this->buildCoordinateParameter(),
            'sensor' => ($this->has_sensor) ? 'true' : 'false',
        );
        
        $url = sprintf(self::$URL, $output_format);
        $url .= '?' . http_build_query($query);
        return $url;
    }

    /**
     * Prepares the coordinates for the final url parameter
     * Structured in such a way to handle a single point or multiple points
     *
     * @return  string  list of locations formatted for the googles
     */
    protected function buildCoordinateParameter()
    {
        $coordinate_list = array();
        foreach ($this->coordinate_array as $coordinate) {
            array_push($coordinate_list, implode(',', $coordinate));
        }
        return implode('|', $coordinate_list);
    }

    /**
     * Fetch the response as a JSON string
     * @url https://developers.google.com/maps/documentation/elevation/#ElevationResponses
     *
     * @return  string  json response from the googles
     */
    public function fetchJSON()
    {
        $url = $this->buildURL('json');
        return $this->executeRequest($url);
    }

    /**
     * Fetch the response as a XML string (yes, a string, you'll need to do the SimpleXML)
     * @url https://developers.google.com/maps/documentation/elevation/#ElevationResponses
     *
     * @return  string  xml response from the googles
     */
    public function fetchXML()
    {
        $url = $this->buildURL('xml');
        return $this->executeRequest($url);
    }

    /**
     * Actual request execution step via the curl
     * Accepts fully built and parameterized endpoint and asks google for information
     *
     * @param   $url    string  full endpoint for the service request
     * @return  string  string response from the request
     */
    protected function executeRequest($url)
    {
        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, TRUE);
        return curl_exec($handle);
    }

}
