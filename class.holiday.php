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
	 */
	public function setglobe($globenum, $r, $g, $b) {
	
		if ( $globenum < 0 || $globenum > $this->NUM_GLOBES - 1) {
			return false;
		}
		
		if ( $r < 0 || $r > 63) $r = 0;
		if ( $g < 0 || $g > 63) $g = 0;
		if ( $b < 0 || $b > 63) $b = 0;
		
		$this->globes[$globenum] = array ((int)$r, (int)$g, (int)$b);
		
	}//end function setglobe()

	/**
	 * Sets the whole string to a particular colour
	 */
	public function fill($r, $g, $b) {
		
		foreach ($this->globes as $globenum => $globe_val) {
			$this->globes[$globenum][0] = (int)$r;
			$this->globes[$globenum][1] = (int)$g;
			$this->globes[$globenum][2] = (int)$b;
		}
		
	}//end function fill()

	/**
	 * Returns an array representing a globe's RGB colour value
	 */
	public function getglobe($globenum) {
		if ( $globenum < 0 || $globenum > $this->NUM_GLOBES) {
			return false;
		}
		
		return $this->globes[$globenum];
		
	}//end function getglobe()

	
	/**
	 * Rotate all of the globes around - up if TRUE, down if FALSE
	 */
	public function chase($direction = true) {
		return;
	}//end function chase()
	
	/**
	 * Rotate all of the globes up if TRUE, down if FALSE
	 * Set the new start of the string to the colour values
	 */
	public function rotate($newr, $newg, $newb, $direction = true) {
		return;
	}//end function rotate()


	/**
	 * The gradient function takes a start colour, end colour and steps count and animates
	 * a gradient from the start to end in steps/second
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
	 */
	public function render() {
	
		$hol_vals = array();
	
		$endpoint = 'http://'.$this->addr.'/device/light/setlights';
		foreach ($this->globes as $globe) {
			$hol_vals[] = sprintf("#%02X%02X%02X", $globe[0], $globe[1], $globe[2]);
		}
		$msg = array('lights' => $hol_vals);
		$msg_json = json_encode($hol_msg);
		return $this->http_put($endpoint, $msg_json);


	}//end function render()
	
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
			return $response;
		}
	
	}

}
 
?>