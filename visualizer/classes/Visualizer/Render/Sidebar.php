<?php

// +----------------------------------------------------------------------+
// | Copyright 2013  Madpixels  (email : visualizer@madpixels.net)        |
// +----------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify |
// | it under the terms of the GNU General Public License, version 2, as  |
// | published by the Free Software Foundation.                           |
// |                                                                      |
// | This program is distributed in the hope that it will be useful,      |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of       |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the        |
// | GNU General Public License for more details.                         |
// |                                                                      |
// | You should have received a copy of the GNU General Public License    |
// | along with this program; if not, write to the Free Software          |
// | Foundation, Inc., 51 Franklin St, Fifth Floor, Boston,               |
// | MA 02110-1301 USA                                                    |
// +----------------------------------------------------------------------+
// | Author: Eugene Manuilov <eugene@manuilov.org>                        |
// +----------------------------------------------------------------------+
/**
 * Base class for all chart sidebar groups.
 *
 * @category Visualizer
 * @package Render
 *
 * @since 1.0.0
 * @abstract
 */
abstract class Visualizer_Render_Sidebar extends Visualizer_Render {

	/**
	 * The array of font families accepted by visualization API.
	 *
	 * @since 1.0.0
	 *
	 * @static
	 * @access protected
	 * @var array
	 */
	protected static $_fontFamilies = array(
		'Arial'         => 'Arial',
		'Sans Serif'    => 'Sans Serif',
		'serif'         => 'Serif',
		'Arial black'   => 'Wide',
		'Arial Narrow'  => 'Narrow',
		'Comic Sans MS' => 'Comic Sans MS',
		'Courier New'   => 'Courier New',
		'Garamond'      => 'Garamond',
		'Georgia'       => 'Georgia',
		'Tahoma'        => 'Tahoma',
		'Verdana'       => 'Verdana',
	);

	/**
	 * The Yes/No array.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 * @var array
	 */
	protected $_yesno;

	/**
	 * Whether this chart supports animation or not.
	 *
	 * @access protected
	 * @var bool
	 */
	protected $_supportsAnimation = true;

	/**
	 * Which library does this this chart implement?
	 *
	 * @access protected
	 * @var string
	 */
	protected $_library = null;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @param array $data The data what has to be associated with this render.
	 */
	public function __construct( $data = array() ) {
		parent::__construct( $data );

		$this->_yesno = array(
			''  => '',
			'1' => esc_html__( 'Yes', 'visualizer' ),
			'0' => esc_html__( 'No', 'visualizer' ),
		);

		$this->hooks();
	}

	/**
	 * Renders chart title settings.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function _renderChartTitleSettings() {
		self::_renderTextItem(
			esc_html__( 'Chart Title', 'visualizer' ),
			'title',
			$this->title,
			esc_html__( 'Text to display above the chart.', 'visualizer' )
		);

		self::_renderColorPickerItem(
			esc_html__( 'Chart Title Color', 'visualizer' ),
			'titleTextStyle[color]',
			isset( $this->titleTextStyle['color'] ) ? $this->titleTextStyle['color'] : null,
			'#000'
		);

		self::_renderTextAreaItem(
			esc_html__( 'Chart Description', 'visualizer' ),
			'description',
			$this->description,
			sprintf(
				// translators: %1$s - HTML link tag, %2$s - HTML closing link tag.
				esc_html__( 'Description to display in the structured data schema as explained %1$shere%2$s', 'visualizer' ),
				'<a href="https://developers.google.com/search/docs/data-types/dataset#dataset" target="_blank">',
				'</a>'
			)
		);
	}

	/**
	 * Add the correct description for the manual configuration box.
	 */
	protected function _renderManualConfigDescription() {
		self::_renderSectionStart();
			self::_renderSectionDescription(
				'<span class="viz-gvlink">' . sprintf(
				// translators: %1$s - HTML link tag, %2$s - HTML closing link tag, %3$s - HTML link tag, %4$s - HTML closing link tag.
					__( 'Configure the graph by providing configuration variables right from the %1$sGoogle Visualization API%2$s. You can refer to to some examples %3$shere%4$s.', 'visualizer' ), '<a href="https://developers.google.com/chart/interactive/docs/gallery/?#configuration-options" target="_blank">',
					'</a>',
					'<a href="https://docs.themeisle.com/article/728-manual-configuration" target="_blank">',
					'</a>'
				) . '</span>'
			);
	}

	/**
	 * Add the correct example for the manual configuration box.
	 */
	protected function _renderManualConfigExample() {
		return '{
			"vAxis": {
				"ticks": [5, 10, 15, 20],
				"titleTextStyle": {
					"color": "red"
				},
				"textPosition": "in"
			}
		}';
	}

	/**
	 * Renders chart advanced settings group.
	 *
	 * @access protected
	 */
	protected function _renderAdvancedSettings() {
		// Chart control settings.
		$this->_renderChartControlsGroup();

		if ( Visualizer_Module_Admin::proFeaturesLocked() ) {
			self::_renderGroupStart( esc_html__( 'Frontend Actions', 'visualizer' ) );
		} else {
			self::_renderGroupStart( esc_html__( 'Frontend Actions', 'visualizer' ) . '<span class="dashicons dashicons-lock"></span>', '', apply_filters( 'visualizer_pro_upsell_class', 'only-pro-feature', 'chart-frontend-actions' ), 'vz-frontend-actions' );
			echo '<div style="position: relative">';
		}
			self::_renderSectionStart();
				self::_renderSectionDescription( esc_html__( 'Configure frontend actions that need to be shown.', 'visualizer' ) );
			self::_renderSectionEnd();

			$this->_renderActionSettings();
		if ( ! Visualizer_Module_Admin::proFeaturesLocked() ) {
			echo apply_filters( 'visualizer_pro_upsell', '', 'frontend-actions' );
			echo '</div>';
		}
		self::_renderGroupEnd();

		self::_renderGroupStart( esc_html__( 'Manual Configuration', 'visualizer' ) );
			$this->_renderManualConfigDescription();
			self::_renderTextAreaItem(
				esc_html__( 'Configuration', 'visualizer' ),
				'manual',
				$this->manual,
				sprintf(
					// translators: %s - the format.
					esc_html__( 'One per line in valid JSON (key:value) format e.g. %s', 'visualizer' ),
					'<br><code>' . $this->_renderManualConfigExample() . '</code>'
				),
				'',
				array( 'rows' => 5 )
			);

		self::_renderSectionEnd();
		self::_renderGroupEnd();

	}

	/**
	 * Renders chart action buttons group.
	 *
	 * @access protected
	 */
	protected function _renderActionSettings() {
		global $wp_version;
		$disable_actions    = version_compare( $wp_version, '4.7.0', '<' );
		// default open this section when not testing through cypress because cypress expects to click and open each section
		// and may not like finding a section is already open.
		self::_renderSectionStart( esc_html__( 'Actions', 'visualizer' ), ! defined( 'TI_CYPRESS_TESTING' ) );
			self::_renderCheckboxItem(
				esc_html__( 'Print', 'visualizer' ),
				'actions[]',
				isset( $this->actions ) && in_array( 'print', $this->actions, true ) ? true : false,
				'print',
				$disable_actions ? '<span class="viz-section-error">' . esc_html__( 'Upgrade to at least WordPress 4.7 to use this.', 'visualizer' ) . '</span>' : esc_html__( 'To enable printing the chart/data.', 'visualizer' ),
				$disable_actions
			);
			self::_renderCheckboxItem(
				esc_html__( 'CSV', 'visualizer' ),
				'actions[]',
				isset( $this->actions ) && in_array( 'csv;application/csv', $this->actions, true ) ? true : false,
				'csv;application/csv',
				$disable_actions ? '<span class="viz-section-error">' . esc_html__( 'Upgrade to at least WordPress 4.7 to use this.', 'visualizer' ) . '</span>' : esc_html__( 'To enable downloading the data as a CSV.', 'visualizer' ),
				$disable_actions
			);

			$disabled   = ! self::is_excel_enabled();
			self::_renderCheckboxItem(
				esc_html__( 'Excel', 'visualizer' ),
				'actions[]',
				isset( $this->actions ) && in_array( 'xls;application/vnd.ms-excel', $this->actions, true ) ? true : false,
				'xls;application/vnd.ms-excel',
				$disable_actions ? '<span class="viz-section-error">' . esc_html__( 'Upgrade to at least WordPress 4.7 to use this.', 'visualizer' ) . '</span>' : ( $disabled ? '<span class="viz-section-error">' . esc_html__( 'Enable the ZIP and XML extensions to use this setting.', 'visualizer' ) . '</span>' : esc_html__( 'To enable downloading the data as an Excel spreadsheet.', 'visualizer' ) ),
				$disable_actions || $disabled
			);
			self::_renderCheckboxItem(
				esc_html__( 'Copy', 'visualizer' ),
				'actions[]',
				isset( $this->actions ) && in_array( 'copy', $this->actions, true ) ? true : false,
				'copy',
				$disable_actions ? '<span class="viz-section-error">' . esc_html__( 'Upgrade to at least WordPress 4.7 to use this.', 'visualizer' ) . '</span>' : esc_html__( 'To enable copying the data to the clipboard.', 'visualizer' ),
				$disable_actions
			);

			// not all charts support downloading as an image.
			$disabled   = ! $this->can_chart_have_action( 'image' );
			self::_renderCheckboxItem(
				esc_html__( 'Download Image', 'visualizer' ),
				'actions[]',
				isset( $this->actions ) && in_array( 'image', $this->actions, true ) ? true : false,
				'image',
				$disable_actions ? '<span class="viz-section-error">' . esc_html__( 'Upgrade to at least WordPress 4.7 to use this.', 'visualizer' ) . '</span>' : ( $disabled ? '<span class="viz-section-error">' . esc_html__( 'Not supported for this chart type.', 'visualizer' ) . '</span>' : esc_html__( 'To download the chart as an image.', 'visualizer' ) ),
				$disable_actions || $disabled
			);
		self::_renderSectionEnd();
	}

	/**
	 * Checks if the Excel module can be enabled.
	 */
	private static function is_excel_enabled() {
		$vendor_file = VISUALIZER_ABSPATH . '/vendor/autoload.php';
		if ( is_readable( $vendor_file ) ) {
			include_once( $vendor_file );
		}

		if ( version_compare( phpversion(), '5.6.0', '<' ) ) {
			do_action( 'themeisle_log_event', Visualizer_Plugin::NAME, sprintf( 'PHP version %s not supported', phpversion() ), 'error', __FILE__, __LINE__ );
			return false;
		}

		return class_exists( 'OpenSpout\Writer\Common\Creator\WriterEntityFactory' ) && extension_loaded( 'zip' ) && extension_loaded( 'xml' ) && extension_loaded( 'fileinfo' );
	}

	/**
	 * Renders select item.
	 *
	 * @since 1.0.0
	 *
	 * @static
	 * @access public
	 * @param string $title The title of the select item.
	 * @param string $name The name of the select item.
	 * @param string $value The actual value of the select item.
	 * @param array  $options The array of select options.
	 * @param string $desc The description of the select item.
	 * @param bool   $multiple Is this a multiple select box.
	 * @param array  $classes Any additional classes.
	 * @param array  $attributes Custom attributes.
	 */
	public static function _renderSelectItem( $title, $name, $value, array $options, $desc, $multiple = false, $classes = array(), $attributes = array() ) {
		$atts   = '';
		if ( $attributes ) {
			foreach ( $attributes as $k => $v ) {
				$atts   .= ' data-visualizer-' . $k . '=' . esc_attr( $v );
			}
		}
		echo '<div class="viz-section-item">';
			echo '<a class="more-info" href="javascript:;">[?]</a>';
			echo '<b>', $title, '</b>';
			echo '<select class="control-select ', implode( ' ', $classes ) , '" name="', $name, '" ', ( $multiple ? 'multiple' : '' ), ' ' , $atts, '>';
		foreach ( $options as $key => $label ) {
			// phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
			$extra      = $multiple && is_array( $value ) ? ( in_array( $key, $value ) ? 'selected' : '' ) : selected( $key, $value, false );
			echo '<option value="', $key, '"', $extra, '>';
			echo $label;
			echo '</option>';
		}
			echo '</select>';
			echo '<p class="viz-section-description">', $desc, '</p>';
		echo '</div>';
	}

	/**
	 * Renders color picker item.
	 *
	 * @since 1.0.0
	 *
	 * @static
	 * @access protected
	 * @param string $title The title of the select item.
	 * @param string $name The name of the select item.
	 * @param string $value The actual value of the select item.
	 * @param string $default The default value of the color picker.
	 */
	protected static function _renderColorPickerItem( $title, $name, $value, $default ) {
		echo '<div class="viz-section-item">';
			echo '<b>', $title, '</b>';
			echo '<div>';
				echo '<input type="text" class="color-picker-hex color-picker" data-alpha-enabled="true" name="', $name, '" maxlength="7" placeholder="', esc_attr__( 'Hex Value', 'visualizer' ), '" value="', is_null( $value ) ? $default : esc_attr( $value ), '" data-default-color="', $default, '">';
			echo '</div>';
		echo '</div>';
	}

	/**
	 * Renders text item.
	 *
	 * @since 1.0.0
	 *
	 * @static
	 * @access protected
	 * @param string $title The title of the select item.
	 * @param string $name The name of the select item.
	 * @param string $value The actual value of the select item.
	 * @param string $desc The description of the select item.
	 * @param string $placeholder The placeholder for the input.
	 * @param string $type The type for the input (out of number, email, tel etc., default is text).
	 * @param array  $custom_attributes The custom attributes.
	 */
	protected static function _renderTextItem( $title, $name, $value, $desc, $placeholder = '', $type = 'text', $custom_attributes = array(), $extra_class = array() ) {
		$attributes     = '';
		if ( $custom_attributes ) {
			foreach ( $custom_attributes as $k => $v ) {
				$attributes .= ' ' . $k . '="' . esc_attr( $v ) . '"';
			}
		}
		$extra_class[] = 'viz-section-item';
		echo '<div class="' . esc_attr( implode( ' ', $extra_class ) ) . '">';
			echo '<a class="more-info" href="javascript:;">[?]</a>';
			echo '<b>', $title, '</b>';
			echo '<input type="', $type, '" class="control-text" ', $attributes, ' name="', $name, '" value="', esc_attr( $value ), '" placeholder="', $placeholder, '">';
			echo '<p class="viz-section-description">', $desc, '</p>';
		echo '</div>';
	}

	/**
	 * Renders the beginning of a group.
	 *
	 * @since 1.0.0
	 *
	 * @static
	 * @access public
	 * @param string $title The title of this group.
	 * @param string $html Any additional HTML.
	 * @param string $class Any additional classes.
	 */
	public static function _renderGroupStart( $title, $html = '', $class = '', $id = '' ) {
		echo '<li id="' . $id . '" class="viz-group ' . $class . '">';
			echo '<h3 class="viz-group-title">', $title, '</h3>';
			echo $html;
			echo '<ul class="viz-group-content">';
	}

	/**
	 * Renders the ending of a group.
	 *
	 * @since 1.0.0
	 *
	 * @static
	 * @access public
	 */
	public static function _renderGroupEnd() {
			echo '</ul>';
		echo '</li>';
	}

	/**
	 * Renders the beginning of a section.
	 *
	 * @since 1.0.0
	 *
	 * @static
	 * @access public
	 * @param string  $title The title of this section. If the title is empty, no title will be displayed.
	 * @param boolean $open Determines whether the section items block has to be expanded or collapsed.
	 */
	public static function _renderSectionStart( $title = false, $open = true ) {

		if ( ! empty( $title ) ) {
			echo '<li class="viz-subsection">';
			echo '<span class="viz-section-title">', $title, '</span>';
		} else {
			echo '<li class=" ">';
		}
			echo '<div class="viz-section-items section-items', $open ? ' open' : '', '">';
	}

	/**
	 * Renders the ending of a section.
	 *
	 * @since 1.0.0
	 *
	 * @public
	 * @access protected
	 * @param string $html Any addition HTML to add.
	 */
	public static function _renderSectionEnd( $html = '' ) {
			echo '</div>';
			echo $html;
		echo '</li>';
	}

	/**
	 * Renders section description block.
	 *
	 * @since 1.0.0
	 *
	 * @static
	 * @access public
	 * @param string $description The description text.
	 */
	public static function _renderSectionDescription( $description, $classes = '' ) {
		echo '<div class="viz-section-item">';
			echo '<div class="viz-section-description ' . $classes . '">', $description, '</div>';
		echo '</div>';
	}

	/**
	 * Renders format field according to series type.
	 *
	 * @since 1.3.0
	 *
	 * @access protected
	 * @param int $index The index of the series.
	 */
	protected function _renderFormatField( $index = 0 ) {
		switch ( $this->__series[ $index + 1 ]['type'] ) {
			case 'number':
				self::_renderTextItem(
					esc_html__( 'Number Format', 'visualizer' ),
					'series[' . $index . '][format]',
					isset( $this->series[ $index ]['format'] ) ? $this->series[ $index ]['format'] : '',
					sprintf(
						// translators: %1$s - HTML link tag, %2$s - HTML closing link tag.
						esc_html__( 'Enter custom format pattern to apply to this series value, similar to the %1$sICU pattern set%2$s. Use something like #,### to get 1,234 as output, or $# to add dollar sign before digits. Pay attention that if you use &#37; percentage format then your values will be multiplied by 100.', 'visualizer' ),
						'<a href="http://icu-project.org/apiref/icu4c/classDecimalFormat.html#_details" target="_blank">',
						'</a>'
					),
					'#,###.##'
				);
				break;
			case 'date':
			case 'datetime':
			case 'timeofday':
				self::_renderTextItem(
					esc_html__( 'Date Format', 'visualizer' ),
					'series[' . $index . '][format]',
					isset( $this->series[ $index ]['format'] ) ? $this->series[ $index ]['format'] : '',
					sprintf(
						// translators: %1$s - HTML link tag, %2$s - HTML closing link tag.
						esc_html__( 'Enter custom format pattern to apply to this series value, similar to the %1$sICU date and time format%2$s.', 'visualizer' ),
						'<a href="https://unicode-org.github.io/icu/userguide/format_parse/datetime/#datetime-format-syntax" target="_blank">',
						'</a>'
					),
					'eeee, dd LLLL yyyy'
				);
				break;
		}
	}

	/**
	 * Render a checkbox item
	 */
	protected static function _renderCheckboxItem( $title, $name, $value, $default, $desc, $disabled = false ) {
		echo '<div class="viz-section-item">';
			echo '<a class="more-info" href="javascript:;">[?]</a>';
			echo '<b>', $title, '</b>';
			// phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
			echo '<input type="checkbox" class="control-check" value="', $default, '" name="', $name, '" ', ( $value == $default ? 'checked' : '' ), ' ', ( $disabled ? 'disabled=disabled' : '' ), '>';
			echo '<p class="viz-section-description">', $desc, '</p>';
		echo '</div>';
	}

	/**
	 * Render a textarea item.
	 */
	protected static function _renderTextAreaItem( $title, $name, $value, $desc, $placeholder = '', $custom_attributes = array() ) {
		$attributes     = '';
		if ( $custom_attributes ) {
			foreach ( $custom_attributes as $k => $v ) {
				$attributes .= ' ' . $k . '="' . esc_attr( $v ) . '"';
			}
		}
		echo '<div class="viz-section-item">';
			echo '<a class="more-info" href="javascript:;">[?]</a>';
			echo '<b>', $title, '</b>';
			echo '<textarea class="control-text" ', $attributes, ' name="', $name, '" placeholder="', $placeholder, '">', $value, '</textarea>';
			echo '<p class="viz-section-description">', $desc, '</p>';
		echo '</div>';
	}

	/**
	 * Returns the library this chart implements.
	 */
	public function getLibrary() {
		return $this->_library;
	}

	/**
	 * Loads generic libraries conditionally.
	 */
	protected function load_dependent_assets( $libs ) {
		if ( in_array( 'moment', $libs, true ) && ! wp_script_is( 'moment', 'registered' ) ) {
			wp_register_script( 'moment' );
		}

		if ( in_array( 'numeral', $libs, true ) && ! wp_script_is( 'numeral', 'registered' ) ) {
			wp_register_script( 'numeral', VISUALIZER_ABSURL . 'js/lib/numeral.min.js', array(), Visualizer_Plugin::VERSION );
		}

	}

	/**
	 * Renders save chart as image setting group.
	 *
	 * @access protected
	 */
	protected function _renderChartImageSettings() {
		// Default enable if amp is active.
		$is_amp = function_exists( 'amp_is_enabled' ) && amp_is_enabled();
		$this->save_chart_image = null === $this->save_chart_image && $is_amp ? true : $this->save_chart_image;

		$is_create_chart = true;
		if ( filter_input( INPUT_GET, 'library', FILTER_VALIDATE_BOOLEAN ) ) {
			if ( filter_input( INPUT_GET, 'action' ) === Visualizer_Plugin::ACTION_EDIT_CHART ) {
				$is_create_chart = false;
			}
		}
		$this->lazy_load_chart = null === $this->lazy_load_chart && $is_create_chart ? true : $this->lazy_load_chart;

		self::_renderSectionStart( esc_html__( 'Save chart as an image inside Media Library', 'visualizer' ), false );
			self::_renderCheckboxItem(
				esc_html__( 'Save inside media library?', 'visualizer' ),
				'save_chart_image',
				$this->save_chart_image ? true : false,
				'yes',
				esc_html__( 'To enable save the image as an inside media library.', 'visualizer' ),
				false
			);
		self::_renderSectionEnd();

		self::_renderSectionStart( esc_html__( 'Lazy rendering of chart', 'visualizer' ), false );
			self::_renderCheckboxItem(
				esc_html__( 'Enable lazy rendering of chart?', 'visualizer' ),
				'lazy_load_chart',
				$this->lazy_load_chart ? true : false,
				'yes',
				esc_html__( 'To enable lazy chart rendering.', 'visualizer' ),
				false
			);
		self::_renderSectionEnd();
	}

	/**
	 * Renders chart controls group.
	 *
	 * @access protected
	 */
	protected function _renderChartControlsGroup() {
		if ( 'google' !== $this->getLibrary() ) {
			return;
		}
		if ( Visualizer_Module_Admin::proFeaturesLocked() ) {
			self::_renderGroupStart( esc_html__( 'Chart Data Filter Configuration', 'visualizer' ) );
		} else {
			self::_renderGroupStart( esc_html__( 'Chart Data Filter Configuration', 'visualizer' ) . '<span class="dashicons dashicons-lock"></span>', '', apply_filters( 'visualizer_pro_upsell_class', 'only-pro-feature', 'chart-filter-controls' ), 'vz-data-controls' );
			echo '<div style="position: relative">';
		}
		self::_renderSectionStart();
		self::_renderSectionDescription(
			'<span class="viz-gvlink">' . sprintf(
			// translators: %1$s - HTML link tag, %2$s - HTML closing link tag.
				__( 'Configure the data filter controls by providing configuration variables right from the %1$sChart Controls API%2$s. ', 'visualizer' ),
				'<a href="https://developers.google.com/chart/interactive/docs/gallery/controls#controls-gallery" target="_blank">',
				'</a>'
			) . '</span>'
		);
		self::_renderSectionEnd();
		$this->_renderChartControlsSettings();
		if ( ! Visualizer_Module_Admin::proFeaturesLocked() ) {
			echo apply_filters( 'visualizer_pro_upsell', '', 'data-filter-configuration' );
			echo '</div>';
		}
		self::_renderGroupEnd();
	}

	/**
	 * Renders chart controls setting.
	 *
	 * @access protected
	 */
	protected function _renderChartControlsSettings() {

		self::_renderSectionStart( esc_html__( 'Options', 'visualizer' ), false );
		$control_type = ! empty( $this->controls['controlType'] ) ? $this->controls['controlType'] : '';

		self::_renderSelectItem(
			esc_html__( 'Filter Type', 'visualizer' ),
			'controls[controlType]',
			$control_type,
			array(
				''                  => '',
				'StringFilter'      => esc_html__( 'String Filter', 'visualizer' ),
				'NumberRangeFilter' => esc_html__( 'Number Range Filter', 'visualizer' ),
				'CategoryFilter'    => esc_html__( 'Category Filter', 'visualizer' ),
				'ChartRangeFilter'  => esc_html__( 'Chart Range Filter', 'visualizer' ),
				'DateRangeFilter'   => esc_html__( 'Date Range Filter', 'visualizer' ),
			),
			'',
			false,
			array( 'vz-controls-opt' )
		);

		$column_index = [ 'false' => '' ];
		$column_label = [ 'false' => '' ];
		if ( ! empty( $this->__series ) ) {
			foreach ( $this->__series as $key => $column ) {
				$column_type            = isset( $column['type'] ) ? $column['type'] : '';
				$label                  = isset( $column['label'] ) ? $column['label'] : '';
				$column_label[ $label ] = $label;
				$column_index[ $key ]   = sprintf(
					// translators: %1$d - the column key, %2$s - the column type.
					__( '%1$d — Column Type: %2$s ', 'visualizer' ),
					$key,
					ucfirst( $column_type )
				);
			}
		}

		self::_renderSelectItem(
			esc_html__( 'Filter By Column Index', 'visualizer' ),
			'controls[filterColumnIndex]',
			isset( $this->controls['filterColumnIndex'] ) ? $this->controls['filterColumnIndex'] : '',
			$column_index,
			'',
			false,
			array( 'vz-controls-opt' )
		);

		self::_renderSelectItem(
			esc_html__( 'Filter By Column Label', 'visualizer' ),
			'controls[filterColumnLabel]',
			! empty( $this->controls['filterColumnLabel'] ) ? $this->controls['filterColumnLabel'] : '',
			$column_label,
			'',
			false,
			array( 'vz-controls-opt' )
		);

		self::_renderSelectItem(
			esc_html__( 'Use Formatted Value', 'visualizer' ),
			'controls[useFormattedValue]',
			! empty( $this->controls['useFormattedValue'] ) ? $this->controls['useFormattedValue'] : '',
			array(
				'false' => esc_html__( 'False', 'visualizer' ),
				'true'  => esc_html__( 'True', 'visualizer' ),
			),
			'',
			false
		);

		self::_renderTextItem(
			esc_html__( 'Min Value', 'visualizer' ),
			'controls[minValue]',
			! empty( $this->controls['minValue'] ) ? $this->controls['minValue'] : '',
			esc_html__( 'Minimum allowed value for the range lower extent.', 'visualizer' ),
			'',
			'text'
		);

		self::_renderTextItem(
			esc_html__( 'Max Value', 'visualizer' ),
			'controls[maxValue]',
			! empty( $this->controls['maxValue'] ) ? $this->controls['maxValue'] : '',
			esc_html__( 'Maximum allowed value for the range higher extent.', 'visualizer' ),
			'',
			'text'
		);

		self::_renderSelectItem(
			esc_html__( 'Match Type', 'visualizer' ),
			'controls[matchType]',
			! empty( $this->controls['matchType'] ) ? $this->controls['matchType'] : '',
			array(
				'prefix' => esc_html__( 'Prefix', 'visualizer' ),
				'exact'  => esc_html__( 'Exact', 'visualizer' ),
				'any'    => esc_html__( 'Any', 'visualizer' ),
			),
			'',
			false
		);

		self::_renderSelectItem(
			esc_html__( 'Case Sensitive', 'visualizer' ),
			'controls[caseSensitive]',
			! empty( $this->controls['caseSensitive'] ) ? $this->controls['caseSensitive'] : '',
			array(
				'false' => esc_html__( 'False', 'visualizer' ),
				'true'  => esc_html__( 'True', 'visualizer' ),
			),
			'',
			false
		);

		self::_renderSectionEnd();

		self::_renderSectionStart( esc_html__( 'UI Options', 'visualizer' ), false );

		self::_renderTextItem(
			esc_html__( 'Label', 'visualizer' ),
			'controls[ui][label]',
			! empty( $this->controls['ui']['label'] ) ? $this->controls['ui']['label'] : '',
			'',
			'',
			'text'
		);

		self::_renderTextItem(
			esc_html__( 'Label Separator', 'visualizer' ),
			'controls[ui][labelSeparator]',
			! empty( $this->controls['ui']['labelSeparator'] ) ? $this->controls['ui']['labelSeparator'] : '',
			'',
			'',
			'text'
		);

		self::_renderTextItem(
			esc_html__( 'Caption', 'visualizer' ),
			'controls[ui][caption]',
			! empty( $this->controls['ui']['caption'] ) ? $this->controls['ui']['caption'] : '',
			'',
			'',
			'text'
		);

		self::_renderSelectItem(
			esc_html__( 'Label Stacking', 'visualizer' ),
			'controls[ui][labelStacking]',
			! empty( $this->controls['ui']['labelStacking'] ) ? $this->controls['ui']['labelStacking'] : '',
			array(
				'horizontal' => esc_html__( 'Horizontal', 'visualizer' ),
				'vertical'   => esc_html__( 'Vertical', 'visualizer' ),
			),
			'',
			false
		);

		self::_renderSelectItem(
			esc_html__( 'Orientation', 'visualizer' ),
			'controls[ui][orientation]',
			! empty( $this->controls['ui']['orientation'] ) ? $this->controls['ui']['orientation'] : '',
			array(
				'horizontal' => esc_html__( 'Horizontal', 'visualizer' ),
				'vertical'   => esc_html__( 'Vertical', 'visualizer' ),
			),
			'',
			false
		);

		self::_renderSelectItem(
			esc_html__( 'Show Range Values', 'visualizer' ),
			'controls[showRangeValues]',
			! empty( $this->controls['showRangeValues'] ) ? $this->controls['showRangeValues'] : '',
			array(
				'true'  => esc_html__( 'True', 'visualizer' ),
				'false' => esc_html__( 'False', 'visualizer' ),
			),
			'',
			false
		);

		self::_renderSelectItem(
			esc_html__( 'Allow Multiple', 'visualizer' ),
			'controls[allowMultiple]',
			! empty( $this->controls['allowMultiple'] ) ? $this->controls['allowMultiple'] : '',
			array(
				'true'  => esc_html__( 'True', 'visualizer' ),
				'false' => esc_html__( 'False', 'visualizer' ),
			),
			'',
			false
		);

		self::_renderSelectItem(
			esc_html__( 'Allow Typing', 'visualizer' ),
			'controls[allowTyping]',
			! empty( $this->controls['allowTyping'] ) ? $this->controls['allowTyping'] : '',
			array(
				'true'  => esc_html__( 'True', 'visualizer' ),
				'false' => esc_html__( 'False', 'visualizer' ),
			),
			'',
			false
		);
		self::_renderSectionEnd();
	}
}
