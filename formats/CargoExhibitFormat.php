<?php
/**
 * @author @lmorillas
 */

class CargoExhibitFormat extends CargoDeferredFormat {

	function allowedParameters() {
		return array( 'height', 'width', 'zoom' );
	}


	/**
	 *
	 * @param array $valuesTable
	 * @param array $formattedValuesTable
	 * @param array $fieldDescriptions
	 * @param array $displayParams
	 * @return string HTML
	 * @throws MWException
	 */
	function queryAndDisplay( $sqlQueries, $displayParams, $querySpecificParams = null ) {
		// Add necessary JS scripts.

        //  Exhibit Scripts

		return "Hello, Exhibit";
	}
}

