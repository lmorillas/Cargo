<?php
/**
 * CargoDisplayMap - class for the #cargo_display_map parser function.
 *
 * @author Yaron Koren
 * @ingroup Cargo
 */

class CargoDisplayMap {

	/**
	 * Handles the #cargo_display_map parser function - displays a
	 * map showing a single point.
	 *
	 * This function is based conceptually on the #display_map
	 * parser function defined by the Maps extension.
	 *
	 * @param Parser $parser
	 * @return string
	 */
	public static function run( &$parser ) {
		$params = func_get_args();
		array_shift( $params ); // we already know the $parser...

		$pointStr = null;
		$serviceStr = null;
		$heightStr = null;
		$widthStr = null;
		$zoomStr = null;

		foreach ( $params as $param ) {
			$parts = explode( '=', $param, 2 );
			if ( count( $parts ) != 2 ) {
				continue;
			}
			$key = trim( $parts[0] );
			$value = trim( $parts[1] );
			if ( $key == 'point' ) {
				$pointStr = $value;
			} elseif ( $key == 'service' ) {
				$serviceStr = $value;
			} elseif ( $key == 'height' ) {
				$heightStr = $value;
			} elseif ( $key == 'width' ) {
				$widthStr = $value;
			} elseif ( $key == 'zoom' ) {
				$zoomStr = $value;
			}
		}

		// If $pointStr is null, it means that the 'point' parameter
		// was not set for #cargo_display_map - display an error
		// message. If, however, it is blank, it means that it was set,
		// but to a blank value, most likely via a template call. In
		// that case, just don't display anything.
		if ( $pointStr === null ) {
			return CargoUtils::formatError( "Error: 'point' parameter must be set." );
		}
		if ( $pointStr === '' ) {
			return;
		}

		// Simulate a query with the appropriate mapping format.
		// Ideally, both this code and the #cargo_query code would
		// call some separate mapping code, but that's not the case
		// yet.
		if ( $serviceStr == 'googlemaps' ) {
			$mappingFormat = new CargoGoogleMapsFormat( $parser->getOutput() );
		} else {
			$mappingFormat = new CargoOpenLayersFormat( $parser->getOutput() );
		}

		list ( $lat, $lon ) = CargoUtils::parseCoordinatesString( $pointStr );
		$valuesTable = array( array( 'Coords  lat' => $lat, 'Coords  lon' => $lon ) );
		$formattedValuesTable = $valuesTable;
		$coordsDesc = new CargoFieldDescription();
		$coordsDesc->mType = 'Coordinates';
		$fieldDescriptions = array( 'Coords' => $coordsDesc );
		$displayParams = array();
		if ( $heightStr != null ) {
			$displayParams['height'] = $heightStr;
		}
		if ( $widthStr != null ) {
			$displayParams['width'] = $widthStr;
		}
		if ( $zoomStr != null ) {
			$displayParams['zoom'] = $zoomStr;
		}

		$text = $mappingFormat->display( $valuesTable, $formattedValuesTable, $fieldDescriptions,
			$displayParams );

		return $parser->insertStripItem( $text, $parser->mStripState );
	}

}
