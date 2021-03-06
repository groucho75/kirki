<?php

class Kirki_Style_Fonts extends Kirki_Style {

	public $firstrun = true;

	function __construct() {

		// Make sure the link is only added once
		if ( $this->firstrun ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'google_font' ), 105 );
			$this->firstrun = false;
		}

	}

	function styles( $control, $styles = array() ) {

		// The value of this control
		$value = get_theme_mod( $control['setting'], $control['default'] );

		// Check if this is a font-family control
		$is_font_family = isset( $control['output']['property'] ) && 'font-family' == $control['output']['property'] ? true : false;
		// Check if this is a font-size control
		$is_font_size   = isset( $control['output']['property'] ) && 'font-size'   == $control['output']['property'] ? true : false;
		// Check if this is a font-weight control
		$is_font_weight = isset( $control['output']['property'] ) && 'font-weight' == $control['output']['property'] ? true : false;

		if ( $is_font_family ) {

			$styles[$control['output']['element']]['font-family'] = $value;

		} else if ( $is_font_size ) {

			// Get the unit we're going to use for the font-size.
			$units = isset( $control['output']['units'] ) ? $control['output']['units'] : 'px';
			$styles[$control['output']['element']]['font-size'] = $value . $units;

		} else if ( $is_font_weight ) {

			$styles[$control['output']['element']]['font-weight'] = $value;

		}

		return $styles;

	}

	function google_link() {

		// Get the global config and controls
		global $kirki;
		$controls = $kirki->get_controls();
		$config   = $kirki->get_config();

		// Get an array of all the google fonts
		$google_fonts = Kirki_Fonts::get_google_fonts();

		$fonts = array();
		foreach ( $controls as $control ) {

			// The value of this control
			$value = get_theme_mod( $control['setting'], $control['default'] );

			if ( isset( $control['output'] ) ) {

				// Check if this is a font-family control
				$is_font_family = isset( $control['output']['property'] ) && 'font-family' == $control['output']['property'] ? true : false;
				// Check if this is a font-weight control
				$is_font_weight = isset( $control['output']['property'] ) && 'font-weight' == $control['output']['property'] ? true : false;
				// Check if this is a font subset control
				$is_font_subset = isset( $control['output']['property'] ) && 'font-subset' == $control['output']['property'] ? true : false;

				if ( $is_font_family ) {
					$fonts[]['font-family'] = $value;
				} else if ( $is_font_weight ) {
					$fonts[]['font-weight'] = $value;
				} else if ( $is_font_subset ) {
					$fonts[]['subsets'] = $value;
				}

			}

		}

		foreach ( $fonts as $font ) {

			if ( isset( $font['font-family'] ) ) {

				$font_families   = ( ! isset( $font_families ) ) ? array() : $font_families;
				$font_families[] = $font['font-family'];

				if ( Kirki_Fonts::is_google_font( $font['font-family'] ) ) {
					$has_google_font = true;
				}

			}

			if ( isset( $font['font-weight'] ) ) {

				$font_weights   = ( ! isset( $font_weights ) ) ? array() : $font_weights;
				$font_weights[] = $font['font-weight'];

			}

			if ( isset( $font['subsets'] ) ) {

				$font_subsets   = ( ! isset( $font_subsets ) ) ? array() : $font_subsets;
				$font_subsets[] = $font['subsets'];

			}

		}

		$font_families = ( ! isset( $font_families ) || empty( $font_families ) ) ? false : $font_families;
		$font_weights  = ( ! isset( $font_weights )  || empty( $font_weights ) )  ? '400' : $font_weights;
		$font_subsets  = ( ! isset( $font_subsets )  || empty( $font_subsets ) )  ? 'all' : $font_subsets;

		if ( ! isset( $has_google_font ) || ! $has_google_font ) {
			$font_families = false;
		}

		return ( $font_families ) ? Kirki_Fonts::get_google_font_uri( $font_families, $font_weights, $font_subsets ) : false;

	}

	/**
	 * Enqueue Google fonts if necessary
	 */
	function google_font() {

		$google_link = $this->google_link();

		if ( $google_link ) {
			wp_register_style( 'kirki_google_fonts', $google_link );
			wp_enqueue_style( 'kirki_google_fonts' );
		}

	}

}
