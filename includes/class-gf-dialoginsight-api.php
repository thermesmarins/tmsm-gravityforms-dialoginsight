<?php

/**
 * Gravity Forms DialogInsight API Library.
 *
 * @since     1.0.0
 * @package   GravityForms
 * @author    Nicolas Mollet
 */
class GF_DialogInsight_API {

	/**
	 * DialogInsight account API key.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string $api_key DialogInsight account API key.
	 */
	protected $api_key;

	/**
	 * DialogInsight account Key ID.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string $key_id DialogInsight account Key ID.
	 */
	protected $key_id;

	/**
	 * DialogInsight webservice url.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string $webservice_url DialogInsight webservice url.
	 */
	protected $webservice_url = 'https://app.mydialoginsight.com/webservices/ofc4/';

	/**
	 * Initialize API library.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param string $api_key (default: '') DialogInsight API key.
	 * @param string $key_id  (default: '') DialogInsight Key ID.
	 *
	 */
	public function __construct( $api_key = '', $key_id = '' ) {

		// Assign API key to object.
		$this->api_key = $api_key;
		$this->key_id  = $key_id;

	}

	/**
	 * Get current account details.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @uses   GF_DialogInsight_API::process_request()
	 * @throws Exception
	 *
	 * @return array
	 */
	public function account_details() {

		return $this->process_request();

	}

	/**
	 * Get all DialogInsight Projects.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @uses   GF_DialogInsight_API::process_request()
	 * @throws Exception
	 *
	 * @return array
	 */
	public function get_projects() {
		$response = $this->process_request( 'Projects', 'List' );
		$projects = $response['Records'];

		return $projects;
	}

	/**
	 * Get a specific DialogInsight list member.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param string $list_id       DialogInsight list ID.
	 * @param string $email_address Email address.
	 *
	 * @uses   GF_DialogInsight_API::process_request()
	 * @throws Exception
	 *
	 * @return array
	 */
	public function get_list_member( $list_id, $email_address ) {

		// Prepare subscriber hash.
		$subscriber_hash = md5( strtolower( $email_address ) );

		return $this->process_request( 'lists/' . $list_id . '/members/' . $subscriber_hash );

	}

	/**
	 * Get all merge fields for a DialogInsight list.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param array $params Params.
	 *
	 * @uses   GF_DialogInsight_API::process_request()
	 * @throws Exception
	 *
	 * @return array
	 */
	public function get_list_merge_fields( $params ) {

		$response      = $this->process_request( 'Projects', 'Get', $params );
		$projectfields = $response['ProjectInfo']['ProjectFields'];

		return $projectfields;
	}

	/**
	 * Get all optins for a DialogInsight list.
	 *
	 * @since  1.0.5
	 * @access public
	 *
	 * @param array $params Params.
	 *
	 * @uses   GF_DialogInsight_API::process_request()
	 * @throws Exception
	 *
	 * @return array
	 */
	public function get_list_merge_optins( $params ) {

		$response      = $this->process_request( 'Projects', 'Get', $params );
		$projectoptins = $response['ProjectInfo']['OptinFields'];

		return $projectoptins;
	}

	/**
	 * Add or update a DialogInsight list member.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param array $params Params.
	 *
	 * @uses   GF_DialogInsight_API::process_request()
	 * @throws Exception
	 *
	 * @return array
	 */
	public function update_list_member( $params ) {

		$response = $this->process_request( 'Contacts', 'Merge', $params );

		return $response;

	}

	/**
	 * Process DialogInsight API request.
	 *
	 * @since  1.0.0
	 * @access private
	 *
	 * @param string $service    Request path.
	 * @param string $method     Request method. Defaults to GET.
	 * @param array  $data       Request data.
	 * @param string $return_key Array key from response to return. Defaults to null (return full response).
	 *
	 * @throws Exception if API request returns an error, exception is thrown.
	 *
	 * @return array
	 */
	private function process_request( $service = '', $method = 'Get', $data = array(), $return_key = null ) {

		// If API key is not set, throw exception.
		if ( rgblank( $this->api_key ) ) {
			throw new Exception( 'API key must be defined to process an API request.' );
		}

		// If Key ID is not set, throw exception.
		if ( rgblank( $this->key_id ) ) {
			throw new Exception( 'Key ID must be defined to process an API request.' );
		}

		// If $path is empty
		if ( rgblank( $service ) ) {
			$service = 'Projects';
			$method  = 'List';
		}

		// Build base request URL.
		$request_url = $this->webservice_url . $service . '.ashx';

		// Add request URL parameters if needed.
		$request_url = add_query_arg( array( 'method' => $method ), $request_url );

		$body = array(
			'AuthKey' => array(
				'idKey' => $this->key_id,
				'Key'   => $this->api_key,
			),
		);
		$body = array_merge( $body, $data );

		// If $path is empty
		if ( rgblank( $service ) ) {
			$service = 'Projects';
			$method  = 'List';
		}


		// Build base request arguments.
		$args = array(
			'body'      => json_encode( $body ),
			'method'    => 'POST',
			'headers'   => array(
				'Accept'       => 'application/json',
				'Content-Type' => 'application/json',
			),
			'sslverify' => apply_filters( 'https_local_ssl_verify', false ),
			'timeout'   => apply_filters( 'http_request_timeout', 30 ),
		);

		// Get request response.
		$response = wp_remote_request( $request_url, $args );

		// If request was not successful, throw exception.
		if ( is_wp_error( $response ) ) {
			throw new GF_DialogInsight_Exception( $response->get_error_message() );
		}

		// Decode response body.
		$response['body'] = json_decode( $response['body'], true );

		// Get the response code.
		$response_code = wp_remote_retrieve_response_code( $response );

		if ( $response_code != 200 ) {

			// If status code is set, throw exception.
			if ( isset( $response['body']['ErrorCode'] ) ) {

				// Initialize exception.
				$exception = new GF_DialogInsight_Exception( $response['body']['ErrorCode'], $response_code );
				$exception->setDetail( $response['body']['ErrorMessage'] );
				$exception->setErrors( $response['body']['ErrorMessage'] );
				throw $exception;

			}

			throw new GF_DialogInsight_Exception( wp_remote_retrieve_response_message( $response ), $response_code );

		}

		if($response['body']['Success'] !== true){

			$exception = new GF_DialogInsight_Exception( $response['body']['ErrorCode'], $response_code );
			$exception->setDetail( $response['body']['ErrorMessage'] );
			$exception->setErrors( $response['body']['ErrorMessage'] );
			throw $exception;
		}

		// Remove links from response.
		unset( $response['body']['_links'] );

		// If a return key is defined and array item exists, return it.
		if ( ! empty( $return_key ) && isset( $response['body'][ $return_key ] ) ) {
			return $response['body'][ $return_key ];
		}

		return $response['body'];

	}

}

/**
 * Gravity Forms DialogInsight Exception.
 *
 * @since     1.0.0
 * @package   GravityForms
 * @author    Nicolas Mollet
 */
class GF_DialogInsight_Exception extends Exception {

	/**
	 * Additional details about the exception.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string $detail Additional details about the exception.
	 */
	protected $detail;

	/**
	 * Exception error messages.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    array $errors Exception error messages.
	 */
	protected $errors;

	/**
	 * Get additional details about the exception.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return string|null
	 */
	public function getDetail() {

		return $this->detail;

	}

	/**
	 * Get exception error messages.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return array|null
	 */
	public function getErrors() {

		return $this->errors;

	}

	/**
	 * Determine if exception has additional details.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return bool
	 */
	public function hasDetail() {

		return ! empty( $this->detail );

	}

	/**
	 * Determine if exception has error messages.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return bool
	 */
	public function hasErrors() {

		return ! empty( $this->errors );

	}

	/**
	 * Set exception details.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param string $detail Additional details about the exception.
	 */
	public function setDetail( $detail ) {

		$this->detail = $detail;

	}

	/**
	 * Set exception error messages.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param string $detail Additional error messages about the exception.
	 */
	public function setErrors( $errors ) {

		$this->errors = $errors;

	}

}
