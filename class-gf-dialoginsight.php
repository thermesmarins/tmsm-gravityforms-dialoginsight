<?php

GFForms::include_feed_addon_framework();

/**
 * Gravity Forms DialogInsight Add-On.
 *
 * @since     1.0.0
 * @package   GravityForms
 * @author    Nicolas Mollet
 */
class GFDialogInsight extends GFFeedAddOn {

	/**
	 * Contains an instance of this class, if available.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    object $_instance If available, contains an instance of this class.
	 */
	private static $_instance = null;

	/**
	 * Defines the version of the DialogInsight Add-On.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string $_version Contains the version, defined from dialoginsight.php
	 */
	protected $_version = GF_DIALOGINSIGHT_VERSION;

	/**
	 * Defines the minimum Gravity Forms version required.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string $_min_gravityforms_version The minimum version required.
	 */
	protected $_min_gravityforms_version = '1.9.12';

	/**
	 * Defines the plugin slug.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string $_slug The slug used for this plugin.
	 */
	protected $_slug = 'tmsm-gravityforms-dialoginsight';

	/**
	 * Defines the main plugin file.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string $_path The path to the main plugin file, relative to the plugins folder.
	 */
	protected $_path = 'tmsm-gravityforms-dialoginsight/dialoginsight.php';

	/**
	 * Defines the full path to this class file.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string $_full_path The full path.
	 */
	protected $_full_path = __FILE__;

	/**
	 * Defines the URL where this Add-On can be found.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string The URL of the Add-On.
	 */
	protected $_url = 'https://github.com/thermesmarins/tmsm-gravityforms-dialoginsight';

	/**
	 * Defines the title of this Add-On.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string $_title The title of the Add-On.
	 */
	protected $_title = 'Gravity Forms DialogInsight Add-On';

	/**
	 * Defines the short title of the Add-On.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string $_short_title The short title.
	 */
	protected $_short_title = 'DialogInsight';

	/**
	 * Defines if Add-On should use Gravity Forms servers for update data.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    bool
	 */
	protected $_enable_rg_autoupgrade = false;

	/**
	 * Defines the capabilities needed for the DialogInsight Add-On
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    array $_capabilities The capabilities needed for the Add-On
	 */
	protected $_capabilities = array( 'gravityforms_dialoginsight', 'gravityforms_dialoginsight_uninstall' );

	/**
	 * Defines the capability needed to access the Add-On settings page.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string $_capabilities_settings_page The capability needed to access the Add-On settings page.
	 */
	protected $_capabilities_settings_page = 'gravityforms_dialoginsight';

	/**
	 * Defines the capability needed to access the Add-On form settings page.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string $_capabilities_form_settings The capability needed to access the Add-On form settings page.
	 */
	protected $_capabilities_form_settings = 'gravityforms_dialoginsight';

	/**
	 * Defines the capability needed to uninstall the Add-On.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string $_capabilities_uninstall The capability needed to uninstall the Add-On.
	 */
	protected $_capabilities_uninstall = 'gravityforms_dialoginsight_uninstall';

	/**
	 * Defines the DialogInsight list field tag name.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string $merge_var_name The DialogInsight list field tag name; used by gform_dialoginsight_field_value.
	 */
	protected $merge_var_name = '';

	/**
	 * Contains an instance of the DialogInsight API library, if available.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    object $api If available, contains an instance of the DialogInsight API library.
	 */
	private $api = null;

	/**
	 * Get an instance of this class.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return GFDialogInsight
	 */
	public static function get_instance() {

		if ( null === self::$_instance ) {
			self::$_instance = new self;
		}

		return self::$_instance;

	}

	/**
	 * Autoload the required libraries.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @uses   GFAddOn::is_gravityforms_supported()
	 */
	public function pre_init() {

		parent::pre_init();

		if ( $this->is_gravityforms_supported() ) {

			// Load the Mailgun API library.
			if ( ! class_exists( 'GF_DialogInsight_API' ) ) {
				require_once( 'includes/class-gf-dialoginsight-api.php' );
			}

		}

	}

	/**
	 * Plugin starting point. Handles hooks, loading of language files and PayPal delayed payment support.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @uses   GFFeedAddOn::add_delayed_payment_support()
	 */
	public function init() {

		parent::init();

	}

	/**
	 * Remove unneeded settings.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function uninstall() {

		parent::uninstall();

		GFCache::delete( 'dialoginsight_plugin_settings' );
		delete_option( 'gf_dialoginsight_settings' );
		delete_option( 'gf_dialoginsight_version' );

	}

	/**
	 * Register needed styles.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return array
	 */
	public function styles() {

		$styles = array(
			array(
				'handle'  => $this->_slug . '_form_settings',
				'src'     => $this->get_base_url() . '/css/form_settings.css',
				'version' => $this->_version,
				'enqueue' => array( 'admin_page' => array( 'form_settings' ) ),
			),
		);

		return array_merge( parent::styles(), $styles );

	}

	/**
	 * Configures the settings which should be rendered on the add-on settings tab.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return array
	 */
	public function plugin_settings_fields() {

		return array(
			array(
				'description' => '',
				'fields'      => array(
					array(
						'name'  => 'keyId',
						'label' => esc_html__( 'DialogInsight Key ID', 'tmsm-gravityforms-dialoginsight' ),
						'type'  => 'text',
						'class' => 'medium',
					),
					array(
						'name'              => 'apiKey',
						'label'             => esc_html__( 'DialogInsight API Key', 'tmsm-gravityforms-dialoginsight' ),
						'type'              => 'text',
						'class'             => 'medium',
						'feedback_callback' => array( $this, 'initialize_api' ),
					),

				),
			),


		);

	}

	/**
	 * Configures the settings which should be rendered on the feed edit page.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return array
	 */
	public function feed_settings_fields() {

		return array(
			array(
				'title'  => esc_html__( 'DialogInsight Feed Settings', 'tmsm-gravityforms-dialoginsight' ),
				'fields' => array(
					array(
						'name'     => 'feedName',
						'label'    => esc_html__( 'Name', 'tmsm-gravityforms-dialoginsight' ),
						'type'     => 'text',
						'required' => true,
						'class'    => 'medium',
						'tooltip'  => sprintf(
							'<h6>%s</h6>%s',
							esc_html__( 'Name', 'tmsm-gravityforms-dialoginsight' ),
							esc_html__( 'Enter a feed name to uniquely identify this setup.', 'tmsm-gravityforms-dialoginsight' )
						),
					),
					array(
						'name'     => 'dialoginsightProject',
						'label'    => esc_html__( 'DialogInsight Project', 'tmsm-gravityforms-dialoginsight' ),
						'type'     => 'dialoginsight_project',
						'required' => true,
						'tooltip'  => sprintf(
							'<h6>%s</h6>%s',
							esc_html__( 'DialogInsight Project', 'tmsm-gravityforms-dialoginsight' ),
							esc_html__( 'Select the DialogInsight project you would like to add your contacts to.',
								'tmsm-gravityforms-dialoginsight' )
						),
					),


				),
			),
			array(
				'dependency' => 'dialoginsightProject',
				'fields'     => array(
					array(
						'name'     => 'dialoginsightList',
						'label'    => esc_html__( 'DialogInsight List', 'tmsm-gravityforms-dialoginsight' ),
						'type'     => 'dialoginsight_list',
						'required' => true,
						'tooltip'  => sprintf(
							'<h6>%s</h6>%s',
							esc_html__( 'DialogInsight List', 'tmsm-gravityforms-dialoginsight' ),
							esc_html__( 'Select the DialogInsight list you would like to add your contacts to.', 'tmsm-gravityforms-dialoginsight' )
						),
					),
					array(
						'name'      => 'mappedFields',
						'label'     => esc_html__( 'Map Fields', 'tmsm-gravityforms-dialoginsight' ),
						'type'      => 'field_map',
						'field_map' => $this->merge_vars_field_map(),
						'tooltip'   => sprintf(
							'<h6>%s</h6>%s',
							esc_html__( 'Map Fields', 'tmsm-gravityforms-dialoginsight' ),
							esc_html__( 'Associate your DialogInsight merge tags to the appropriate Gravity Form fields by selecting the appropriate form field from the list.',
								'tmsm-gravityforms-dialoginsight' )
						),
					),
					array(
						'name'    => 'optinCondition',
						'label'   => esc_html__( 'Conditional Logic', 'tmsm-gravityforms-dialoginsight' ),
						'type'    => 'feed_condition',
						'tooltip' => sprintf(
							'<h6>%s</h6>%s',
							esc_html__( 'Conditional Logic', 'tmsm-gravityforms-dialoginsight' ),
							esc_html__( 'When conditional logic is enabled, form submissions will only be exported to DialogInsight when the conditions are met. When disabled all form submissions will be exported.',
								'tmsm-gravityforms-dialoginsight' )
						),
					),
					array( 'type' => 'save' ),
				),
			),
		);

	}

	/**
	 * Define the markup for the dialoginsight_project type field.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param array $field The field properties.
	 * @param bool  $echo  Should the setting markup be echoed. Defaults to true.
	 *
	 * @return string
	 */
	public function settings_dialoginsight_project( $field, $echo = true ) {

		// Initialize HTML string.
		$html = '';

		// If API is not initialized, return.
		if ( ! $this->initialize_api() ) {
			return $html;
		}

		try {


			// Get lists.
			$projects = $this->api->get_projects();

		} catch ( Exception $e ) {

			// Log that contact lists could not be obtained.
			$this->log_error( __METHOD__ . '(): Could not retrieve DialogInsight projects; ' . $e->getMessage() );

			// Display error message.
			printf( esc_html__( 'Could not load DialogInsight projects. %sError: %s', 'tmsm-gravityforms-dialoginsight' ), '<br/>',
				$e->getMessage() );

			return;

		}

		// If no lists were found, display error message.
		if ( empty( $projects ) ) {

			// Log that no lists were found.
			$this->log_error( __METHOD__ . '(): Could not load DialogInsight projects; no project found.' );
			//$this->log_error( var_export($projects, true) );

			// Display error message.
			printf( esc_html__( 'Could not load DialogInsight projects. %sError: %s', 'tmsm-gravityforms-dialoginsight' ), '<br/>',
				esc_html__( 'No lists found.', 'tmsm-gravityforms-dialoginsight' ) );

			return;

		}

		// Log number of lists retrieved.
		$this->log_debug( __METHOD__ . '(): Number of projects: ' . count( $projects ) );

		// Initialize select options.
		$options = array(
			array(
				'label' => esc_html__( 'Select a DialogInsight Project', 'tmsm-gravityforms-dialoginsight' ),
				'value' => '',
			),
		);

		// Loop through DialogInsight lists.
		foreach ( $projects as $project ) {

			// Add list to select options.
			$options[] = array(
				'label' => esc_html( $project['ProjectName'] ),
				'value' => esc_attr( $project['idProject'] ),
			);

		}

		// Add select field properties.
		$field['type']     = 'select';
		$field['choices']  = $options;
		$field['onchange'] = 'jQuery(this).parents("form").submit();';

		// Generate select field.
		$html = $this->settings_select( $field, false );

		if ( $echo ) {
			echo $html;
		}

		return $html;

	}

	/**
	 * Define the markup for the dialoginsight_list type field.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param array $field The field properties.
	 * @param bool  $echo  Should the setting markup be echoed. Defaults to true.
	 *
	 * @return string
	 */
	public function settings_dialoginsight_list( $field, $echo = true ) {

		// Initialize HTML string.
		$html = '';

		// If API is not initialized, return.
		if ( ! $this->initialize_api() ) {
			return $html;
		}

		// Get current Project ID.
		$project_id = $this->get_setting( 'dialoginsightProject' );

		// Prepare list request parameters.
		$params = array( 'idProject' => $project_id );


		try {

			// Log contact lists request parameters.
			$this->log_debug( __METHOD__ . '(): Retrieving contact lists; params: ' . print_r( $params, true ) );

			// Get lists.
			$lists = $this->api->get_lists( $params );

		} catch ( Exception $e ) {

			// Log that contact lists could not be obtained.
			$this->log_error( __METHOD__ . '(): Could not retrieve DialogInsight contact lists; ' . $e->getMessage() );

			// Display error message.
			printf( esc_html__( 'Could not load DialogInsight contact lists. %sError: %s', 'tmsm-gravityforms-dialoginsight' ), '<br/>',
				$e->getMessage() );

			return;

		}

		//$this->log_error( __METHOD__ . '(): Lists:' );
		//$this->log_error( var_export($lists, true) );

		// If no lists were found, display error message.
		if ( empty( $lists ) ) {

			// Log that no lists were found.
			$this->log_error( __METHOD__ . '(): Could not load DialogInsight contact lists; no lists found.' );

			// Display error message.
			printf( esc_html__( 'Could not load DialogInsight contact lists. %sError: %s', 'tmsm-gravityforms-dialoginsight' ), '<br/>',
				esc_html__( 'No lists found.', 'tmsm-gravityforms-dialoginsight' ) );

			return;

		}

		// Log number of lists retrieved.
		$this->log_debug( __METHOD__ . '(): Number of lists: ' . count( $lists ) );

		// Initialize select options.
		$options = array(
			array(
				'label' => esc_html__( 'Select a DialogInsight List', 'tmsm-gravityforms-dialoginsight' ),
				'value' => '',
			),
		);

		// Loop through DialogInsight lists.
		foreach ( $lists as $list ) {

			// Add list to select options.
			$options[] = array(
				'label' => esc_html( $list['Labels'][0]['Value'] ),
				'value' => esc_attr( $list['Code'] ),
			);

		}

		// Add select field properties.
		$field['type']     = 'select';
		$field['choices']  = $options;
		$field['onchange'] = 'jQuery(this).parents("form").submit();';

		// Generate select field.
		$html = $this->settings_select( $field, false );

		if ( $echo ) {
			echo $html;
		}

		return $html;

	}

	/**
	 * Return an array of DialogInsight list fields which can be mapped to the Form fields/entry meta.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return array
	 */
	public function merge_vars_field_map() {

		// Initialize field map array.
		$field_map = array();

		// If unable to initialize API, return field map.
		if ( ! $this->initialize_api() ) {
			return $field_map;
		}

		// Get current Project ID.
		$project_id = $this->get_setting( 'dialoginsightProject' );

		// Prepare list request parameters.
		$params = array( 'idProject' => $project_id );

		try {

			// Get merge fields.
			$merge_fields = $this->api->get_list_merge_fields( $params );

		} catch ( Exception $e ) {

			// Log error.
			$this->log_error( __METHOD__ . '(): Unable to get merge fields for DialogInsight list; ' . $e->getMessage() );

			return $field_map;

		}

		//$this->log_error( __METHOD__ . '(): Fields:' );
		//$this->log_error( var_export($merge_fields, true) );


		// If merge fields exist, add to field map.
		if ( ! empty( $merge_fields ) ) {

			// Loop through merge fields.
			foreach ( $merge_fields as $merge_field ) {

				// Define required field type.
				$field_type = null;

				// If this is an email merge field, set field types to "email" or "hidden".
				if ( 'EMail' === $merge_field['Code'] ) {
					$field_type = array( 'email', 'hidden' );
				}

				// If this is an address merge field, set field type to "address".
				if ( 'address' === $merge_field['DataType'] ) {
					$field_type = array( 'address' );
				}

				// Add to field map.
				$field_map[ $merge_field['Code'] ] = array(
					'name'       => $merge_field['Code'],
					'label'      => $merge_field['Labels'][0]['Value'],
					'required'   => $merge_field['isRequired'],
					'field_type' => $field_type,
				);

			}

		}

		return $field_map;
	}

	/**
	 * Prevent feeds being listed or created if the API key isn't valid.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return bool
	 */
	public function can_create_feed() {

		return $this->initialize_api();

	}

	/**
	 * Configures which columns should be displayed on the feed list page.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return array
	 */
	public function feed_list_columns() {

		return array(
			'feedName' => esc_html__( 'Name', 'tmsm-gravityforms-dialoginsight' ),
		);

	}

	/**
	 * Define which field types can be used for the group conditional logic.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @uses   GFAddOn::get_current_form()
	 * @uses   GFCommon::get_label()
	 * @uses   GF_Field::get_entry_inputs()
	 * @uses   GF_Field::get_input_type()
	 * @uses   GF_Field::is_conditional_logic_supported()
	 *
	 * @return array
	 */
	public function get_conditional_logic_fields() {

		// Initialize conditional logic fields array.
		$fields = array();

		// Get the current form.
		$form = $this->get_current_form();

		// Loop through the form fields.
		foreach ( $form['fields'] as $field ) {

			// If this field does not support conditional logic, skip it.
			if ( ! $field->is_conditional_logic_supported() ) {
				continue;
			}

			// Get field inputs.
			$inputs = $field->get_entry_inputs();

			// If field has multiple inputs, add them as individual field options.
			if ( $inputs && 'checkbox' !== $field->get_input_type() ) {

				// Loop through the inputs.
				foreach ( $inputs as $input ) {

					// If this is a hidden input, skip it.
					if ( rgar( $input, 'isHidden' ) ) {
						continue;
					}

					// Add input to conditional logic fields array.
					$fields[] = array(
						'value' => $input['id'],
						'label' => GFCommon::get_label( $field, $input['id'] ),
					);

				}

			} else {

				// Add field to conditional logic fields array.
				$fields[] = array(
					'value' => $field->id,
					'label' => GFCommon::get_label( $field ),
				);

			}

		}

		return $fields;

	}

	// # FEED PROCESSING -----------------------------------------------------------------------------------------------

	/**
	 * Process the feed, subscribe the user to the list.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param array $feed  The feed object to be processed.
	 * @param array $entry The entry object currently being processed.
	 * @param array $form  The form object currently being processed.
	 *
	 * @return array
	 */
	public function process_feed( $feed, $entry, $form ) {

		// Log that we are processing feed.
		$this->log_debug( __METHOD__ . '(): Processing feed.' );

		// If unable to initialize API, log error and return.
		if ( ! $this->initialize_api() ) {
			$this->add_feed_error( esc_html__( 'Unable to process feed because API could not be initialized.', 'tmsm-gravityforms-dialoginsight' ),
				$feed, $entry, $form );

			return $entry;
		}

		// Set current merge variable name.
		$this->merge_var_name = 'EMail';

		// Get field map values.
		$field_map = $this->get_field_map_fields( $feed, 'mappedFields' );

		// Get mapped email address.
		$email = $this->get_field_value( $form, $entry, $field_map['EMail'] );

		// If email address is invalid, log error and return.
		if ( GFCommon::is_invalid_or_empty_email( $email ) ) {
			$this->add_feed_error( esc_html__( 'A valid Email address must be provided.', 'tmsm-gravityforms-dialoginsight' ), $feed, $entry, $form );

			return $entry;
		}

		/**
		 * Prevent empty form fields erasing values already stored in the mapped DialogInsight MMERGE fields
		 * when updating an existing subscriber.
		 *
		 * @param bool  $override If the merge field should be overridden.
		 * @param array $form     The form object.
		 * @param array $entry    The entry object.
		 * @param array $feed     The feed object.
		 */
		$override_empty_fields = gf_apply_filters( 'gform_dialoginsight_override_empty_fields', array( $form['id'] ), true, $form, $entry, $feed );

		// Log that empty fields will not be overridden.
		if ( ! $override_empty_fields ) {
			$this->log_debug( __METHOD__ . '(): Empty fields will not be overridden.' );
		}

		// Initialize array to store merge vars.
		$merge_vars = array();

		// Loop through field map.
		foreach ( $field_map as $name => $field_id ) {

			// If no field is mapped, skip it.
			if ( rgblank( $field_id ) ) {
				continue;
			}

			// If this is the email field, skip it.
			//if ( $name === 'EMail' ) {
			//	continue;
			//}

			// Set merge var name to current field map name.
			$this->merge_var_name = $name;

			// Get field object.
			$field = GFFormsModel::get_field( $form, $field_id );

			// Get field value.
			$field_value = $this->get_field_value( $form, $entry, $field_id );

			// If field value is empty and we are not overriding empty fields, skip it.
			if ( empty( $field_value ) && ( ! $override_empty_fields || ( is_object( $field ) && 'address' === $field->get_input_type() ) ) ) {
				continue;
			}

			$merge_vars[ 'f_' . $name ] = $field_value;

		}

		// Define initial member, member found and member status variables.
		$member        = false;
		$member_found  = false;
		$member_status = null;

		try {

			// Log that we are checking if user is already subscribed to list.
			$this->log_debug( __METHOD__ . "(): Checking to see if $email is already on the list (disabled)" );

		} catch ( Exception $e ) {

			// If the exception code is not 404, abort feed processing.
			if ( 404 !== $e->getCode() ) {

				// Log that we could not get the member information.
				$this->add_feed_error( sprintf( esc_html__( 'Unable to check if email address is already used by a member: %s',
					'tmsm-gravityforms-dialoginsight' ), $e->getMessage() ), $feed, $entry, $form );

				return $entry;

			}

			// Log member status.
			$this->log_debug( __METHOD__ . "(): $email was not found on list." );

		}

		/**
		 * Modify whether a user that currently has a status of unsubscribed on your list is resubscribed.
		 * By default, the user is resubscribed.
		 *
		 * @param bool  $allow_resubscription If the user should be resubscribed.
		 * @param array $form                 The form object.
		 * @param array $entry                The entry object.
		 * @param array $feed                 The feed object.
		 */
		$allow_resubscription = gf_apply_filters( array( 'gform_dialoginsight_allow_resubscription', $form['id'] ), true, $form, $entry, $feed );

		// If member is unsubscribed and resubscription is not allowed, exit.
		if ( 'unsubscribed' == $member_status && ! $allow_resubscription ) {
			$this->log_debug( __METHOD__ . '(): User is unsubscribed and resubscription is not allowed.' );

			return;
		}

		// If member status is not defined, set to subscribed.
		$member_status = isset( $member_status ) ? $member_status : 'subscribed';

		$list_id    = $feed['meta']['dialoginsightList'];
		$project_id = $feed['meta']['dialoginsightProject'];

		// Prepare transaction type for filter.
		$transaction = $member_found ? 'Update' : 'Subscribe';

		$action = $member_found ? 'updated' : 'added';

		$merge_vars[ 'optin_' . $list_id ] = true;


		// Prepare request parameters.
		$params = array(
			'idProject'    => $project_id,
			'Records'      => array(
				array(
					'ID'   => array(
						'key_f_EMail' => $email,
					),
					'Data' => $merge_vars,
				),
			),
			'MergeOptions' => array(
				'AllowInsert'            => true,
				'AllowUpdate'            => true,
				'SkipDuplicateRecords'   => false,
				'SkipUnmatchedRecords'   => false,
				'ReturnRecordsOnSuccess' => false,
				'ReturnRecordsOnError'   => false,
				'FieldOptions'           => null,
			),
		);

		try {

			// Log the subscriber to be added or updated.
			$this->log_debug( __METHOD__ . "(): Subscriber to be {$action}: " . $email );

			// Add or update subscriber.
			$response = $this->api->update_list_member( $params );
			//$this->log_debug( __METHOD__ . "(): Params for {$action}: " . print_r( $params, true ) );
			$this->log_debug( __METHOD__ . "(): Params for {$action}: " . json_encode( $params ) );
			$this->log_debug( __METHOD__ . "(): Response for {$action}: " . print_r( $response, true ) );

			// Log that the subscription was added or updated.
			$this->log_debug( __METHOD__ . "(): Subscriber successfully {$action}." );

		} catch ( Exception $e ) {

			// Log that subscription could not be added or updated.
			$this->add_feed_error( sprintf( esc_html__( 'Unable to add/update subscriber: %s', 'tmsm-gravityforms-dialoginsight' ),
				$e->getMessage() ), $feed, $entry, $form );

			// Log field errors.
			if ( $e->hasErrors() ) {
				$this->log_error( __METHOD__ . '(): Field errors when attempting subscription: ' . print_r( $e->getErrors(), true ) );
			}

			return;

		}

	}

	/**
	 * Returns the value of the selected field.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param array  $form     The form object currently being processed.
	 * @param array  $entry    The entry object currently being processed.
	 * @param string $field_id The ID of the field being processed.
	 *
	 * @uses   GFAddOn::get_full_name()
	 * @uses   GF_Field::get_value_export()
	 * @uses   GFFormsModel::get_field()
	 * @uses   GFFormsModel::get_input_type()
	 * @uses   GFDialogInsight::get_full_address()
	 * @uses   GFDialogInsight::maybe_override_field_value()
	 *
	 * @return array
	 */
	public function get_field_value( $form, $entry, $field_id ) {

		// Set initial field value.
		$field_value = '';

		// Set field value based on field ID.
		switch ( strtolower( $field_id ) ) {

			// Form title.
			case 'form_title':
				$field_value = rgar( $form, 'title' );
				break;

			// Entry creation date.
			case 'date_created':

				// Get entry creation date from entry.
				$date_created = rgar( $entry, strtolower( $field_id ) );

				// If date is not populated, get current date.
				$field_value = empty( $date_created ) ? gmdate( 'Y-m-d H:i:s' ) : $date_created;
				break;

			// Entry IP and source URL.
			case 'ip':
			case 'source_url':
				$field_value = rgar( $entry, strtolower( $field_id ) );
				break;

			default:

				// Get field object.
				$field = GFFormsModel::get_field( $form, $field_id );

				if ( is_object( $field ) ) {

					// Check if field ID is integer to ensure field does not have child inputs.
					$is_integer = $field_id == intval( $field_id );

					// Get field input type.
					$input_type = GFFormsModel::get_input_type( $field );

					if ( $is_integer && 'address' === $input_type ) {

						// Get full address for field value.
						$field_value = $this->get_full_address( $entry, $field_id );

					} else if ( $is_integer && 'name' === $input_type ) {

						// Get full name for field value.
						$field_value = $this->get_full_name( $entry, $field_id );

					} else if ( $is_integer && 'checkbox' === $input_type ) {

						// Initialize selected options array.
						$selected = array();

						// Loop through checkbox inputs.
						foreach ( $field->inputs as $input ) {
							$index = (string) $input['id'];
							if ( ! rgempty( $index, $entry ) ) {
								$selected[] = $this->maybe_override_field_value( rgar( $entry, $index ), $form, $entry, $index );
							}
						}

						// Convert selected options array to comma separated string.
						$field_value = implode( ', ', $selected );

					} else if ( 'phone' === $input_type && $field->phoneFormat == 'standard' ) {

						// Get field value.
						$field_value = rgar( $entry, $field_id );

						// Reformat standard format phone to match DialogInsight format.
						// Format: NPA-NXX-LINE (404-555-1212) when US/CAN.
						if ( ! empty( $field_value ) && preg_match( '/^\D?(\d{3})\D?\D?(\d{3})\D?(\d{4})$/', $field_value, $matches ) ) {
							$field_value = sprintf( '%s-%s-%s', $matches[1], $matches[2], $matches[3] );
						}

					} else {

						// Use export value if method exists for field.
						if ( is_callable( array( 'GF_Field', 'get_value_export' ) ) ) {
							$field_value = $field->get_value_export( $entry, $field_id );
						} else {
							$field_value = rgar( $entry, $field_id );
						}

					}

				} else {

					// Get field value from entry.
					$field_value = rgar( $entry, $field_id );

				}

		}

		return $this->maybe_override_field_value( $field_value, $form, $entry, $field_id );

	}

	/**
	 * Use the legacy gform_dialoginsight_field_value filter instead of the framework gform_SLUG_field_value filter.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param string $field_value The field value.
	 * @param array  $form        The form object currently being processed.
	 * @param array  $entry       The entry object currently being processed.
	 * @param string $field_id    The ID of the field being processed.
	 *
	 * @return string
	 */
	public function maybe_override_field_value( $field_value, $form, $entry, $field_id ) {

		return gf_apply_filters( 'gform_dialoginsight_field_value', array( $form['id'], $field_id ), $field_value, $form['id'], $field_id, $entry,
			$this->merge_var_name );

	}

	/**
	 * Initializes DialogInsight API if credentials are valid.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param string $api_key DialogInsight API key.
	 *
	 * @uses   GFAddOn::get_plugin_setting()
	 * @uses   GFAddOn::log_debug()
	 * @uses   GFAddOn::log_error()
	 * @uses   GF_DialogInsight_API::account_details()
	 *
	 * @return bool|null
	 */
	public function initialize_api( $api_key = null ) {

		$key_id = null;

		// If API is alredy initialized, return true.
		if ( ! is_null( $this->api ) ) {
			return true;
		}
		$api_key = $this->get_plugin_setting( 'apiKey' );
		$key_id  = $this->get_plugin_setting( 'keyId' );

		$this->log_debug( __METHOD__ . '(): API Key:' . $api_key );
		$this->log_debug( __METHOD__ . '(): Key ID:' . $key_id );


		// If the API key is blank, do not run a validation check.
		if ( rgblank( $api_key ) || rgblank( $key_id ) ) {
			$this->log_debug( __METHOD__ . '(): API Key or Key ID empty.' );

			return null;
		}

		// Log validation step.
		$this->log_debug( __METHOD__ . '(): Validating API Info.' );

		// Setup a new DialogInsight object with the API credentials.
		$dialoginsight = new GF_DialogInsight_API( $api_key, $key_id );

		try {

			// Retrieve account information.
			$dialoginsight->account_details();

			// Assign API library to class.
			$this->api = $dialoginsight;

			// Log that authentication test passed.
			$this->log_debug( __METHOD__ . '(): DialogInsight successfully authenticated.' );
			//$this->log_debug( __METHOD__ . '(): Return body 1:'. var_export($dialoginsight, true) );
			//$this->log_debug( __METHOD__ . '(): Return body 2:'. var_export($dialoginsight->account_details(), true) );
			return true;

		} catch ( Exception $e ) {

			// Log that authentication test failed.
			$this->log_error( __METHOD__ . '(): Unable to authenticate with DialogInsight; ' . $e->getMessage() );

			return false;

		}

	}

	/**
	 * Returns the combined value of the specified Address field.
	 * Street 2 and Country are the only inputs not required by DialogInsight.
	 * If other inputs are missing DialogInsight will not store the field value, we will pass a hyphen when an input is empty.
	 * DialogInsight requires the inputs be delimited by 2 spaces.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param array  $entry    The entry currently being processed.
	 * @param string $field_id The ID of the field to retrieve the value for.
	 *
	 * @return array|null
	 */
	public function get_full_address( $entry, $field_id ) {

		// Initialize address array.
		$address = array(
			'addr1'   => str_replace( '  ', ' ', trim( rgar( $entry, $field_id . '.1' ) ) ),
			'addr2'   => str_replace( '  ', ' ', trim( rgar( $entry, $field_id . '.2' ) ) ),
			'city'    => str_replace( '  ', ' ', trim( rgar( $entry, $field_id . '.3' ) ) ),
			'state'   => str_replace( '  ', ' ', trim( rgar( $entry, $field_id . '.4' ) ) ),
			'zip'     => trim( rgar( $entry, $field_id . '.5' ) ),
			'country' => trim( rgar( $entry, $field_id . '.6' ) ),
		);

		// Get address parts.
		$address_parts = array_values( $address );

		// Remove empty address parts.
		$address_parts = array_filter( $address_parts );

		// If no address parts exist, return null.
		if ( empty( $address_parts ) ) {
			return null;
		}

		// Replace country with country code.
		if ( ! empty( $address['country'] ) ) {
			$address['country'] = GF_Fields::get( 'address' )->get_country_code( $address['country'] );
		}

		return $address;

	}

}
