<?php
/**
 * Holiday class implementation for Holiday by Moorescloud
 *
 * Homepage and documentation: http://dev.moorescloud.com
 *
 * @author Avi Miller <avi.miller@gmail.com>
 * @copyright 2013 Avi Miller
 * @license MIT
 */
 
Class Holiday {

	// Holiday has 50 globes
	public $NUM_GLOBES = 50;
	
	// Internal array for the globes
	private $globes = array();
	
	// Internal variable for hostname
	private $addr;
	
	// Internal URL base for RESTful API
	private $api_url;
	
	/** 
	 * If remote, we require the remote address. Currently, no checking of the address is
	 * performed
	 *
	 * @param string $addr Remote Holiday IP address or fully-qualified domain name
	 *
	 */	
	function __construct($addr) {
		if ( $addr == '') return false;
		
		// Initialise an array of globes set to all zeroes
		for ($globe = 0; $globe < $this->NUM_GLOBES; $globe++) {
			$this->globes[$globe] = array(0, 0, 0);
		}

		$this->addr = $addr;
		$this->api_url = 'http://'.$addr.'/iotas/0.1/device/moorescloud.holiday/localhost/';
		
	}//end function __construct()

	/**
	 * Set a globe
	 *
	 * @param int $globenum The number of the globe to set
	 * @param int $r The red colour value
	 * @param int $g The green colour value
	 * @param int $b The blue colour value
	 *
	 */
	public function setglobe($globenum, $r, $g, $b) {
	
		// Can't set a globe that doesn't exist
		if ( $globenum < 0 || $globenum > $this->NUM_GLOBES - 1) return false;

		// Can't use values that are not integers
		if (!is_int($globenum) || !is_int($r) || !is_int($g) || !is_int($b)) return false;

		// Can't set colour values below 0 or greater than 255.
		if ($r < 0 || $r > 255 || $g < 0 || $g > 255 || $b < 0 || $b > 255) return false;

		$this->globes[$globenum] = array ($r, $g, $b);
		
	}//end function setglobe()

	/**
	 * Sets the whole string to a particular colour
	 *
	 * @param int $r The red colour value to use for the entire string
	 * @param int $g The green colour value to use for the entire string
	 * @param int $b The blue colour value to use for the entire string
	 *
	 */
	public function fill($r, $g, $b) {

		// Can't use values that are not integers
		if (!is_int($r) || !is_int($g) || !is_int($b)) return false;
		
		// Can't set colour values below 0 or greater than 255.
		if ($r < 0 || $r > 255 || $g < 0 || $g > 255 || $b < 0 || $b > 255) return false;
		
		foreach ($this->globes as $globenum => $globe_val) {
			$this->globes[$globenum][0] = (int)$r;
			$this->globes[$globenum][1] = (int)$g;
			$this->globes[$globenum][2] = (int)$b;
		}
		
	}//end function fill()

	/**
	 * Returns an array representing a globe's RGB colour value
	 *
	 * @param int $globenum The globe you want to query
	 *
	 * @return array Returns an array of RGB value for the globe
	 *
	 */
	public function getglobe($globenum) {
		if ( $globenum < 0 || $globenum > $this->NUM_GLOBES) {
			return false;
		}
		
		return $this->globes[$globenum];
		
	}//end function getglobe()

	
	/**
	 * Rotate all of the globes around - up if TRUE, down if FALSE
	 *
	 * @param bool $direction Set which direction to chase, UP if true, DOWN if false
	 *
	 */
	public function chase($direction = true) {
		return;
	}//end function chase()
	
	/**
	 * Rotate all of the globes up if TRUE, down if FALSE
	 * Set the new start of the string to the colour values
	 *
	 * @param int $newr New starting red colour value
	 * @param int $newg New starting green colour value
	 * @param int $newb New starting blue colour value
	 * @param bool $direction Rotate all globes UP if true or DOWN if false
	 *
	 */
	public function rotate($newr, $newg, $newb, $direction = true) {
		return;
	}//end function rotate()


	/**
	 * The gradient function takes a start colour, end colour and steps count and animates
	 * a gradient from the start to end in steps/second
	 *
	 * @param array $begin An array of RGB values for the beginning of the gradient
	 * @param array $end An array of RGB values for the end of the gradient
	 * @param int $steps The number of steps (per second) to use between the start and end values
	 *
	 * @return string Returns the response from the RESTful API
	 *
	 */
	public function gradient($begin, $end, $steps) {
	
		if (!is_array($begin) || !is_array($end) || !is_int($steps)) return false;
		if (count($begin) > 3 || count($end) > 3) return false;
		
		$endpoint = $this->api_url.'gradient';
		$msg = array('begin' => $begin, 'end' => $end, 'steps' => $steps);
		$msg_json = json_encode($msg);
		return $this->http_put($endpoint, $msg_json);
	
	}//end function gradient()


	/**
	 * The render function uses the Holiday RESTful API to control the lights
	 *
	 * This function doesn't take any parameters. It uses the internal state of the globes set
	 * by either the fill() or setglobe() methods to "paint" the current state onto the Holiday
	 *
	 * @return string Returns the response from the RESTful API
	 */
	public function render() {
	
		$globe_colours = array();
	
		$endpoint = 'http://'.$this->addr.'/device/light/setlights';
		foreach ($this->globes as $globe) {
			$globe_colours[] = sprintf("#%02X%02X%02X", $globe[0], $globe[1], $globe[2]);
		}
		$msg = array('lights' => $globe_colours);
		$msg_json = json_encode($msg);
		echo($msg_json)."\n";
		return $this->http_put($endpoint, $msg_json);


	}//end function render()
	
	
	/**
	 * This function sends the JSON data via PUT to the Holiday RESTful API
	 *
	 * @param string $endpoint The API endpoint to use
	 * @param string $data The JSON-encoded data to send to the endpoint
	 *
	 * @return string Returns false if the endpoint doesn't respond or the response from the API
	 *
	 */
	 private function http_put($endpoint, $data) {
	
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $endpoint);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($data)));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT"); 
		curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
		$response = curl_exec($ch);
		curl_close($ch);
		
		if (!$response) {
			return false;
		} else {
			return json_decode($response);
		}
	
	}

}
 
?>