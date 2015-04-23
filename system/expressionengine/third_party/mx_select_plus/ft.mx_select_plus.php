<?php
if ( !defined( 'BASEPATH' ) )
	exit( 'No direct script access allowed' );

require_once PATH_THIRD . 'mx_select_plus/config.php';

/**
 *  MX Select Plus Class for ExpressionEngine2
 *
 * @package  ExpressionEngine
 * @subpackage Fieldtypes
 * @category Fieldtypes
 * @author    Max Lazar <max@eec.ms>
 * @copyright Copyright (c) 2012 Max Lazar
 * @license
 */

class Mx_select_plus_ft extends EE_Fieldtype
{
	/**
	 * Fieldtype Info
	 *
	 * @var array
	 */

	public $info = array(
		'name'     => MX_SELECT_NAME,
		'version'  => MX_SELECT_VER );

	// Parser Flag (preparse pairs?)
	var $has_array_data = true;

	/**
	 * PHP5 construct
	 */
	function __construct() {
		parent::__construct();
		$this->EE->lang->loadfile( MX_SELECT_KEY );

	}

	// --------------------------------------------------------------------

	/**
	 * validate function.
	 *
	 * @access public
	 * @param mixed   $data
	 * @return void
	 */
	function validate( $data ) {
		$valid = TRUE;

	}

	// --------------------------------------------------------------------

	/**
	 * display_field function.
	 *
	 * @access public
	 * @param mixed   $data
	 * @return void
	 */
	public function display_field( $data, $cell = false ) {
		$js = "";
		$field_options = array();

		$this->EE->load->helper( 'custom_field' );

		$data = array(
			'name'  => ( $cell )  ? $this->cell_name : $this->field_name,
			'id'  => str_replace( array( "[", "]" ), "_", $this->field_name ),
			'value'  => decode_multi_field( $data ),
			'class'  =>  $this->field_name.( ( $cell ) ?  '_col_id_'.$this->col_id : '' ),
			'allow_new_options'   => ( $this->settings['allow_new_options'] == 'y' || $this->settings['allow_new_options'] == 'o' ) ?  "true" : "false",
			'allow_deselect'   => ( isset($this->settings['allow_deselect'])) ?  (($this->settings['allow_deselect'] == 'y') ? "true" : "false") : "true"
		);

		if ( !$cell )
			$js .='$("#'.$data['id'].'").chosen({no_results_text: "'.lang( 'no_results' ).'", add_new_options: '.$data['allow_new_options'].', cell_obj:false, add_new: "'.$data['id'].'", allow_single_deselect: '.$data['allow_deselect'].', group_class: "#'.$data['id'].'", callback: function() {}});
					$("div.publish_field.publish_mx_select_plus").css({"overflow-y" : "visible"});
					$("#low-variables-form").css({"overflow": "visible"});
					$("#low-variables-form").parents(".pageContents:first").css({"overflow": "visible"});
		';

		$this->_add_js_css( $cell );

		$this->_insert_js( $js );

		$attr = array (
			( $this->settings['multiselect'] == 'y' ) ? 'multiple' : '',
			'data-placeholder="' . $this->settings['placeholder'] . '"',
			'class="'.$data['class'].'"',
			'style="' .'width:'.( ( isset( $this->settings['min_width'] ) ) ? $this->settings['min_width'] : "100%" ).';'. '"',
			'id="' . $data['id']. '"',
			'dir="' . $this->_data_help( $this->settings, 'field_text_direction' ) . '"',
			'data-no="'.$data['allow_new_options'].'"',
			'data-deselect="'.$data['allow_deselect'].'"'
		);

		$this->one_time_options( $data["value"] );

		$field_options = ( is_array( $this->settings['options'] ) ) ? array(""=>"") + $this->settings['options'] : array();

		// add function for DB
		if (isset($this->settings['db_request'])) {
			if (substr(strtolower(trim($this->settings['db_request'])), 0, 6) == 'select')
			{
				$optgroup = (strpos(strtolower(trim($this->settings['db_request'])),  'optgroup') === false ) ? FALSE : TRUE ;

				$query = $this->EE->db->query($this->settings['db_request']);
				if ( $query->num_rows() > 0 ) {
					foreach ( $query->result_array() as $key => $val )
					{
							if ($optgroup) {
								$optgroup = $val['optgroup'];
								$field_options[$optgroup][$val['option_name']] = $val['option_label'];
							} else {
								$field_options[$val['option_name']] = $val['option_label'];
							}
					}
				}
			}
		}

		return form_dropdown( $data['name'].'[]', $field_options, $data["value"], implode( ' ', $attr ) );

	}

	// @TODO
	//	The fix is the change eec_matrix_cols > col_settings > change ‘TEXT’ to ‘LONGTEXT’
	//	need to gift TheJae dev lic
	//

	/**
	 * one_time_options function.
	 *
	 * @access public
	 * @param mixed   $values
	 * @return void
	 */
	public function one_time_options( $values ) {
		if
		( $this->settings['allow_new_options'] != 'o' ) {
			return;
		}

		foreach ( $values as $key ) {
			if
			( !in_array( $key, $this->settings['options'] ) ) {
				$this->settings['options'][$key] = $key;
			}
		}

		return;
	}

	/**
	 * Displays the cell
	 *
	 * @access public
	 * @param unknown $data The cell data
	 */
	public function display_cell( $data ) {

		return $this->display_field( $data, true );
	}


	/**
	 * display_var_field function.
	 *
	 * @access public
	 * @param mixed   $data
	 * @return void
	 */
	public function display_var_field( $data ) {

		return $this->display_field( $data, false );
	}

	/**
	 * _add_js_css function.
	 *
	 * @access private
	 * @return void
	 */
	function _add_js_css( $cell = false ) {
		$theme_url =  $this->EE->config->item( 'theme_folder_url' ) . 'third_party/mx_select_plus';
		if ( !isset( $this->EE->session->cache[MX_SELECT_KEY]['header'] ) ) {
			$this->EE->cp->add_to_head( '<script type="text/javascript" src="'.$theme_url . '/js/chosen.jquery.min.js"></script>' );
			$this->EE->cp->add_to_head( '<link rel="stylesheet" type="text/css" href="' .$theme_url. '/css/chosen.css" />' );
			$this->EE->session->cache[MX_SELECT_KEY]['header'] = true;
		};

		if ( $cell && !isset( $this->EE->session->cache[MX_SELECT_KEY]['cell'] ) ) {
			$this->EE->cp->add_to_foot( '<script type="text/javascript" src="' .$theme_url. '/js/mx_select.js"></script>' );
			$this->EE->session->cache[MX_SELECT_KEY]['cell'] = true;
		}

	}


	function _sql_wizard () {


	}

	/**
	 * _get_field_options function.
	 *
	 * @access private
	 * @param mixed   $data
	 * @return void
	 */
	function _get_field_options( $data ) {

		if ( ! is_array( $this->settings['options'] ) ) {
			foreach ( explode( "\n", trim( $this->settings['options'] ) ) as $v ) {
				$v = trim( $v );

				$field_options[form_prep( $v )] = form_prep( $v );
			}
		}
		else {
			$field_options = $this->settings['options'];
		}

		return $field_options;
	}

	/**
	 * _insert_js function.
	 *
	 * @access private
	 * @param mixed   $js
	 * @return void
	 */
	private function _insert_js( $js ) {
		$this->EE->cp->add_to_foot( '<script type="text/javascript">'.$js.'</script>' );
	}



	/**
	 * replace_tag function.
	 *
	 * @access public
	 * @param mixed   $data
	 * @param string  $params  (default: '')
	 * @param string  $tagdata (default: '')
	 * @return void
	 */
	function replace_tag( $data, $params = '', $tagdata = '' ) {
		$r = '';
		$count = 1;

		if ( !$tagdata ) {
			return $this->replace_ul( $data, $params );
		}

		$this->EE->load->helper( 'custom_field' );

		$data = decode_multi_field( $data );

		// dp we need to sort?
		if ( isset( $params['sort'] ) ) {
			$sort = strtolower( $params['sort'] );

			if ( $sort == 'asc' ) {
				sort( $data );
			}
			else if ( $sort == 'desc' ) {
					rsort( $data );
				}
		}

		// process offset and limit parametrs
		if ( isset( $params['offset'] ) || isset( $params['limit'] ) ) {
			$offset = isset( $params['offset'] ) ? $params['offset'] : 0;
			$limit = isset( $params['limit'] ) ? $params['limit'] : count( $data );
			$data = array_splice( $data, $offset, $limit );
		}

		if ( !isset( $params['all_options'] ) ) {
			foreach ( $data as $option ) {
				$tagdata_tmp = $this->EE->TMPL->swap_var_single( 'option', $option, $tagdata );
				$tagdata_tmp = $this->EE->TMPL->swap_var_single( 'count', $count , $tagdata_tmp );

				if ( isset( $this->settings['options'][$option] ) ) {
					$tagdata_tmp = $this->EE->TMPL->swap_var_single( 'option_name', $this->settings['options'][$option], $tagdata_tmp );
				} else {
					$tagdata_tmp = $this->EE->TMPL->swap_var_single( 'option_name', $option, $tagdata_tmp );
				}

				$r .= $tagdata_tmp;

				$count++;
			}

		} else {

			foreach ( $this->settings['options'] as $key => $val ) {

				$selected = ( in_array( $key, $data ) ) ? 1 : 0;

				$tagdata_tmp = $this->EE->TMPL->swap_var_single( 'option', $key, $tagdata );

				$tagdata_tmp = $this->EE->TMPL->swap_var_single( 'option_name', $this->settings['options'][$key], $tagdata_tmp );

				$tagdata_tmp = $this->EE->TMPL->swap_var_single( 'selected', $selected, $tagdata_tmp );

				$tagdata_tmp = $this->EE->TMPL->swap_var_single( 'count', $count , $tagdata_tmp );

				$r .= $tagdata_tmp;

				$count++;
			}

		}

		$r = $this->EE->TMPL->swap_var_single( 'total_results', count($data) , $r );

		if ( isset( $params['backspace'] ) ) {
			$r = substr( $r, 0, -$params['backspace'] );
		}


		return $r;
	}

	/**
	 * replace_ul function.
	 *
	 * @access public
	 * @param mixed   $data
	 * @param array   $params (default: array())
	 * @return void
	 */
	public function replace_ul( $data, $params = array() ) {
		return "<ul>"."\n" . $this->replace_tag( $data, $params, "<li>{option}</li>"."\n" ) . '</ul>';
	}

	/**
	 * replace_ul function.
	 *
	 * @access public
	 * @param mixed   $data
	 * @param array   $params (default: array())
	 * @return void
	 */
	public function replace_ol( $data, $params = array() ) {
		return "<ol>"."\n" . $this->replace_tag( $data, $params, "<li>{option}</li>"."\n" ) . '</ol>';
	}

	/**
	 * Display Cell Settings
	 *
	 * @access public
	 * @param unknown $cell_settings array The cell settings
	 * @return array Label and form inputs
	 */
	public function display_cell_settings( $cell_settings ) {
		return $this->_build_settings( $cell_settings );
	}


	/**
	 * display_settings function.
	 *
	 * @access public
	 * @param mixed   $data
	 * @return void
	 */
	public function display_settings( $data ) {
		foreach
		( $this->_build_settings( $data ) as $v ) {
			$this->EE->table->add_row( $v );
		}
	}

	/**
	 * display_var_settings function.
	 *
	 * @access public
	 * @param mixed   $data
	 * @return void
	 */
	public function display_var_settings( $data ) {
		return $this->_build_settings( $data, 'lv' );
	}


	/**
	 * build_settings function.
	 *
	 * @access public
	 * @param mixed   $data
	 * @return void
	 */
	function _build_settings( $data, $type = false ) {
		if ( $type == "lv" ) {
			$prefix = 'variable_settings['.MX_SELECT_KEY.']';
		}
		else {
			$prefix = MX_SELECT_KEY . '_';
		}

		//variable_settings
		return array (
			array( lang( 'placeholder', 'placeholder' ), form_input( $prefix . '[placeholder]', $this->_data_help( $data, 'placeholder' ) ) ),
			array( lang( 'multiselect', 'multiselect' ), form_dropdown( $prefix . '[multiselect]', array( 'y' => lang( 'yes' ), 'n' => lang( 'no' ) ), $this->_data_help( $data, 'multiselect', 'n' ) ) ),
			array( lang( 'allow_new_options', 'allow_new_options' ), form_dropdown( $prefix . '[allow_new_options]', array( 'y' => lang( 'yes' ), 'n' => lang( 'no' ), 'o' => lang( 'one_time' ) ), $this->_data_help( $data, 'allow_new_options', 'n' ) ) ),
			array( lang( 'allow_deselect', 'allow_deselect' ), form_dropdown( $prefix . '[allow_deselect]', array( 'y' => lang( 'yes' ), 'n' => lang( 'no' ) ), $this->_data_help( $data, 'allow_deselect', 'y' ) ) ),
			//array( lang( 'source', 'source' ), form_dropdown( $prefix . '[source]', array( 'stadart_list' => lang( 'stadart_list' ), 'db' => lang( 'db' ), 'json' => lang( 'json' ) ), $this->_data_help( $data, 'source', 'stadart_list' ) ) ),
			array( lang( 'min_width', 'min_width' ), form_input( $prefix . '[min_width]', $this->_data_help( $data, 'min_width', '300px' ) ) ),
			array( lang( 'field_list_items', 'field_list_items' ), form_textarea( $prefix . '[options]', $this->_options( $this->_data_help( $data, 'options' ) ) ) ),

			array( lang( 'db_request', 'db_request' ), form_textarea( $prefix . '[db_request]',$this->_data_help( $data, 'db_request' )  ) )

		);

		//
	}

	/**
	 * _data_help function.
	 *
	 * @access private
	 * @param mixed   $data
	 * @param string  $default (default: '')
	 * @return void
	 */
	function _data_help( $data, $key, $default = '' ) {
		return ( empty( $data[$key] ) or $data[$key] == '' ) ? $default : $data[$key];
	}

	/**
	 * _data_help function.
	 *
	 * @access private
	 * @param mixed   $data
	 * @param mixed   $key
	 * @param string  $default (default: '')
	 * @return void
	 */
	function _options( $options=array() ) {
		$r = '';

		if ( !is_array( $options ) ) {
			return $r;
		}

		foreach	( $options as $name => $label ) {

			//needs to rewrite this block
			if (is_array($label)) {
				if ( $r !== '' ) {
						$r .= "\n";
				}
				$r .= '[['.$name.']]';

				foreach	( $label as $n => $l ) {
					if ( $r !== '' ) {
						$r .= "\n";
					}

					if ( !$n && !$l ) $n = $l = ' ';

					$r .= htmlspecialchars( $n );

					if ( $n != $l )  $r .= ' : '.$l;
				}

			} else {

				if ( $r !== '' ) {
					$r .= "\n";
				}

				if ( !$name && !$label ) $name = $label = ' ';

				$r .= htmlspecialchars( $name );

				if ( $name != $label )  $r .= ' : '.$label;
			}
		}

		return $r;

	}
	/**
	 * save_cell_settings function.
	 *
	 * @access public
	 * @param mixed   $data
	 * @return void
	 */
	function save_cell_settings( $data ) {

		return $this->save_settings( $data );

	}

	/**
	 * save_var_settings function.
	 *
	 * @access public
	 * @param mixed   $var_settings
	 * @return void
	 */
	function save_var_settings( $var_settings ) {

		return $this->save_settings( $var_settings, 'lv' );

	}

	/**
	 * save_settings function.
	 *
	 * @access public
	 * @param mixed   $data
	 * @return void
	 */
	function save_settings( $data, $type = false ) {
		$pattern = '#'.'\[\['.'(.*?)' .'\]\]'.'#s';
		$current_optgroup = false;

		$prefix = MX_SELECT_KEY . '_';


		if ( $type == "lv" )
			$data[$prefix] = $data;

		if ( isset( $data[$prefix] ) ) {

			foreach ( $data[$prefix] as $key => $val ) {

				if ( $key == "options" ) {

					$out = array();
					foreach ( explode( "\n", $val ) as $option ) {


						// check for optgroups
				        if (is_string($option)
				          && preg_match($pattern, $option, $matches)
				        )
				        {
				         $optgroup = $matches[1];
				         $current_optgroup = $optgroup;
				        } else {
				        	$value_name = explode( " : ", $option, 2 );

				        	if (!$current_optgroup) {
								$out[$value_name[0]] = isset( $value_name[1] ) ? $value_name[1] : $value_name[0];
							} else
							{
								$out[$current_optgroup][$value_name[0]] = isset( $value_name[1] ) ? $value_name[1] : $value_name[0];
							}


				        }

					}
					$val = $out;

				}

				$data[$key] = $val;

			}

		}

		return $data;

	}
	// --------------------------------------------------------------------


	// --------------------------------------------------------------------
	/**
	 * install function.
	 *
	 * @access public
	 * @return void
	 */
	function install() {
		return array(
			'' => ''
		);

	}

	/**
	 * save function.
	 *
	 * @access public
	 * @param mixed   $data
	 * @return void
	 */
	function save( $data ) {
		$this->save_options( $data );

		if ( !empty( $data ) ) {
			$data = ( is_array() ) ? implode( '|', $data ) : $data;
		} else {
			$data = $data;
		}


		return $data;
	}


	/**
	 * save_var_field function.
	 *
	 * @access public
	 * @param mixed   $data
	 * @return void
	 */
	function save_var_field( $data ) {
		return $this->save( $data );
	}


	/**
	 * save_cell function.
	 *
	 * @access public
	 * @param mixed   $data
	 * @return void
	 */
	function save_cell( $data ) {

		if ( !is_array( $data ) ) {
			return;
		}

		$r = array ();
		foreach ( $data as $k => $v ) {
			$r[] = $v;

		}

		return $this->save( $r );

	}

	/**
	 * save_options function.
	 *
	 * @access public
	 * @param mixed   $data
	 * @return void
	 */
	function save_options( $data ) {

		if ( isset( $this->EE->session->cache[MX_SELECT_KEY]['new_field'] ) ) {

			return;
		}

		if ( !isset( $_POST['new_field'] ) ) {
			return;
		}


		/*	if ($this->settings['allow_new_options'] != 'y')
		{

			return;
		}
	*/

		$type = false;
		foreach ( $_POST['new_field'] as $key=>$new_field ) {

			$col_id = false;
			if ( !empty( $this->EE->safecracker ) ) {

				if ( strpos( $key, "col_id_" ) !== FALSE ) {
					$key = explode( "_col_id_", $key );
					$field_name = $key[0];
					$col_id = $key[1];
				} else {
					$field_name = $key;
				}
				$field_id = $this->EE->safecracker->get_field_data( $field_name );
				$field_id = $field_id['field_id'];

			} elseif
			( isset( $this->var_id ) ) {
				$key = explode( "_", $key );
				$field_id = $key[1];
				$col_id = false;
				$type = 'lv';
			} else {
				$key = explode( "_", $key );
				$field_id = $key[2];
				$col_id = ( ( isset( $key[5] ) ) ? $key[5] : false ) ;
			}

			$this->update_settings_live( $new_field, $field_id, $col_id, $type );
		}

		$this->EE->session->cache[MX_SELECT_KEY]['new_field'] = true;

		return true;

	}


	/**
	 * update_settings_live function.htmlspecialchars
	 *
	 * @access public
	 * @param mixed   $data
	 * @param mixed   $field_id
	 * @param bool    $type     (default: false)
	 * @return void
	 */
	function update_settings_live( $data, $field_id,  $col_id = false, $type = false ) {

		if
		( !$type ) {
			$this->EE->load->library( 'api' ); $this->EE->api->instantiate( 'channel_fields' );

			$current_settings = $this->EE->api_channel_fields->get_settings( $field_id );

			/*if ($this->settings['allow_new_options'] != 'y')
			{
				return;
			}*/

			if ( $current_settings['field_type'] != "matrix" ) {
				$this->EE->db->select( 'field_settings' );
				$this->EE->db->where( 'field_id', $field_id );
				$query = $this->EE->db->get( 'channel_fields' );

				if ( $query->num_rows() > 0 ) {
					$field_list_items = unserialize( base64_decode( $query->row()->field_settings ) );

					if ( $field_list_items['allow_new_options'] != 'y') {
						return;
					}

					foreach  ( $data as $key => $val ) {
						$field_list_items['options'][$val] = $val;
					}

					$this->EE->db->where( 'field_id', $field_id );
					$this->EE->db->set( 'field_settings', base64_encode( serialize( $field_list_items ) ) );
					$this->EE->db->update( 'channel_fields' );
				}


			}

			if ( $current_settings['field_type'] == "matrix" ) {
				$this->EE->db->select( 'col_settings' );
				$this->EE->db->where( 'field_id', $field_id );
				$this->EE->db->where( 'col_id', $col_id );
				$query = $this->EE->db->get( 'matrix_cols' );

				if ( $query->num_rows() > 0 ) {
					$col_settings = unserialize( base64_decode( $query->row()->col_settings ) );

					if ( $col_settings['allow_new_options'] != 'y' ) {
						return;
					}

					foreach  ( $data as $key => $val ) {
						$col_settings['options'][$val] = $val;
					}

					$this->EE->db->where( 'field_id', $field_id );
					$this->EE->db->where( 'col_id', $col_id );
					$this->EE->db->set( 'col_settings', base64_encode( serialize( $col_settings ) ) );
					$this->EE->db->update( 'matrix_cols' );

				}


			}

		} else {
			$this->EE->db->select( 'variable_settings' );
			$this->EE->db->where( 'variable_id', $field_id );
			$query = $this->EE->db->get( 'low_variables' );

			if ( $query->num_rows() > 0 ) {
				$variable_settings = unserialize( base64_decode( $query->row()->variable_settings ) );

				if ( $variable_settings['allow_new_options'] != 'y' ) {
					return;
				}

				foreach  ( $data as $key => $val ) {
					$variable_settings['options'][$val] = $val;
				}

				$this->EE->db->where( 'variable_id', $field_id );
				$this->EE->db->set( 'variable_settings', base64_encode( serialize( $variable_settings ) ) );
				$this->EE->db->update( 'low_variables' );

			}

		}

	}

}

// END mx_select_plus_ft class

/* End of file ft.mx_select_plus.php */
/* Location: ./expressionengine/third_party/mx_select_plus/ft.mx_select_plus.php */
