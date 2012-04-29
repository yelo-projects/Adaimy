<?php
/**
 * Represents a response returned by a controller.
 *
 * @package sapphire
 * @subpackage control
 */
class SS_HTTPResponse {
	
	/**
	 * @var array
	 */
	protected static $status_codes = array(
		100 => 'Continue',
		101 => 'Switching Protocols',
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',
		301 => 'Moved Permanently',
		302 => 'Found',
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',
		307 => 'Temporary Redirect',
		400 => 'Bad Request',
		401 => 'Unauthorized',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Timeout',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Request Entity Too Large',
		414 => 'Request-URI Too Long',
		415 => 'Unsupported Media Type',
		416 => 'Request Range Not Satisfiable',
		417 => 'Expectation Failed',
		422 => 'Unprocessable Entity',
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Timeout',
		505 => 'HTTP Version Not Supported',
	);
	
	/**
	 * @var array
	 */
	protected static $redirect_codes = array(
		301,
		302,
		303,
		304,
		305,
		307
	);
	
	/**
	 * @var Int
	 */
	protected $statusCode = 200;
	
	/**
	 * @var String
	 */
	protected $statusDescription = "OK";
	
	/**
	 * HTTP Headers like "Content-Type: text/xml"
	 *
	 * @see http://en.wikipedia.org/wiki/List_of_HTTP_headers
	 * @var array
	 */
	protected $headers = array(
		"Content-Type" => "text/html; charset=\"utf-8\"",
	);
	
	/**
	 * @var string
	 */
	protected $body = null;
	
	/**
	 * Create a new HTTP response
	 * 
	 * @param $body The body of the response
	 * @param $statusCode The numeric status code - 200, 404, etc
	 * @param $statusDescription The text to be given alongside the status code. 
	 *  See {@link setStatusCode()} for more information.
	 */
	function __construct($body = null, $statusCode = null, $statusDescription = null) {
		$this->body = $body;
		if($statusCode) $this->setStatusCode($statusCode, $statusDescription);
	}
	
	/**
	 * @param String $code
	 * @param String $description Optional. See {@link setStatusDescription()}.
	 *  No newlines are allowed in the description.
	 *  If omitted, will default to the standard HTTP description
	 *  for the given $code value (see {@link $status_codes}).
	 */
	function setStatusCode($code, $description = null) {
		if(isset(self::$status_codes[$code])) $this->statusCode = $code;
		else user_error("Unrecognised HTTP status code '$code'", E_USER_WARNING);
		
		if($description) $this->statusDescription = $description;
		else $this->statusDescription = self::$status_codes[$code];
	}
	
	/**
	 * The text to be given alongside the status code ("reason phrase").
	 * Caution: Will be overwritten by {@link setStatusCode()}.
	 * 
	 * @param String $description 
	 */
	function setStatusDescription($description) {
		$this->statusDescription = $description;
	}
	
	/**
	 * @return Int
	 */
	function getStatusCode() {
		return $this->statusCode;
	}

	/**
	 * @return string Description for a HTTP status code
	 */
	function getStatusDescription() {
		return str_replace(array("\r","\n"), '', $this->statusDescription);
	}
	
	/**
	 * Returns true if this HTTP response is in error
	 */
	function isError() {
		return $this->statusCode && ($this->statusCode < 200 || $this->statusCode > 399);
	}
	
	function setBody($body) {
		$this->body = $body;
	}
	
	function getBody() {
		return $this->body;
	}
	
	/**
	 * Add a HTTP header to the response, replacing any header of the same name.
	 * 
	 * @param string $header Example: "Content-Type"
	 * @param string $value Example: "text/xml" 
	 */
	function addHeader($header, $value) {
		$this->headers[$header] = $value;
	}
	
	/**
	 * Return the HTTP header of the given name.
	 * 
	 * @param string $header
	 * @returns string
	 */
	function getHeader($header) {
		if(isset($this->headers[$header])) {
			return $this->headers[$header];			
		} else {
			return null;
		}
	}
	
	/**
	 * @return array
	 */
	function getHeaders() {
		return $this->headers;
	}
	
	/**
	 * Remove an existing HTTP header by its name,
	 * e.g. "Content-Type".
	 *
	 * @param unknown_type $header
	 */
	function removeHeader($header) {
		if(isset($this->headers[$header])) unset($this->headers[$header]);
	}
	
	function redirect($dest, $code=302) {
		if(!in_array($code, self::$redirect_codes)) $code = 302;
		$this->statusCode = $code;
		$this->headers['Location'] = $dest;
	}

	/**
	 * Send this HTTPReponse to the browser
	 */
	function output() {
		// Attach appropriate X-Include-JavaScript and X-Include-CSS headers
		if(Director::is_ajax()) {
			Requirements::include_in_response($this);
		}

		if(in_array($this->statusCode, self::$redirect_codes) && headers_sent($file, $line)) {
			$url = $this->headers['Location'];
			echo
			"<p>Redirecting to <a href=\"$url\" title=\"Please click this link if your browser does not redirect you\">$url... (output started on $file, line $line)</a></p>
			<meta http-equiv=\"refresh\" content=\"1; url=$url\" />
			<script type=\"text/javascript\">setTimeout('window.location.href = \"$url\"', 50);</script>";
		} else {
			if(!headers_sent()) {
				header($_SERVER['SERVER_PROTOCOL'] . " $this->statusCode " . $this->getStatusDescription());
				foreach($this->headers as $header => $value) {
					header("$header: $value");
				}
			}
			
			// Only show error pages or generic "friendly" errors if the status code signifies
			// an error, and the response doesn't have any body yet that might contain
			// a more specific error description.
			if(Director::isLive() && $this->isError() && !$this->body) {
				Debug::friendlyError($this->statusCode, $this->getStatusDescription());
			} else {
				echo $this->body;
			}
			
		}
	}
	
	/**
	 * Returns true if this response is "finished", that is, no more script execution should be done.
	 * Specifically, returns true if a redirect has already been requested
	 */
	function isFinished() {
		return in_array($this->statusCode, array(301, 302, 401, 403));
	}
	
	/**
	 * @deprecated 2.4 Use {@link HTTP::getLinksIn()} on DOMDocument.
	 */
	public function getLinks() {
		user_error (
			'SS_HTTPResponse->getLinks() is deprecated, please use HTTP::getLinksIn() or DOMDocument.', E_USER_NOTICE
		);
		
		$attributes = array('id', 'href', 'class');
		$links      = array();
		$results    = array();
		
		if(preg_match_all('/<a[^>]+>/i', $this->body, $links)) foreach($links[0] as $link) {
			$processedLink = array();
			foreach($attributes as $attribute) {
				$matches = array();
				if(preg_match('/' . $attribute  . '\s*=\s*"([^"]+)"/i', $link, $matches)) {
					$processedLink[$attribute] = $matches[1];
				}
			}
			$results[] = $processedLink;
		}
		
		return $results;
    }
	
}

/**
 * A {@link SS_HTTPResponse} encapsulated in an exception, which can interrupt the processing flow and be caught by the
 * {@link RequestHandler} and returned to the user.
 *
 * Example Usage:
 * <code>
 * throw new SS_HTTPResponse_Exception('This request was invalid.', 400);
 * throw new SS_HTTPResponse_Exception(new SS_HTTPResponse('There was an internal server error.', 500));
 * </code>
 *
 * @package sapphire
 * @subpackage control
 */
class SS_HTTPResponse_Exception extends Exception {
	
	protected $response;
	
	/**
	 * @see SS_HTTPResponse::__construct();
	 */
	 public function __construct($body = null, $statusCode = null, $statusDescription = null) {
	 	if($body instanceof SS_HTTPResponse) {
	 		$this->setResponse($body);
	 	} else {
	 		$this->setResponse(new SS_HTTPResponse($body, $statusCode, $statusDescription));
	 	}
	 	
	 	parent::__construct($this->getResponse()->getBody(), $this->getResponse()->getStatusCode());
	 }
	 
	 /**
	  * @return SS_HTTPResponse
	  */
	 public function getResponse() {
	 	return $this->response;
	 }
	 
	 /**
	  * @param SS_HTTPResponse $response
	  */
	 public function setResponse(SS_HTTPResponse $response) {
	 	$this->response = $response;
	 }
	
}