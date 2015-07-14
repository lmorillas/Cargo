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
        $ex_script = '<script src="http://api.simile-widgets.org/exhibit/current/exhibit-api.js"></script>';
        $maps_script = '<link rel="exhibit-extension" href="http://api.simile-widgets.org/exhibit/current/extensions/map/map-extension.js"/>';
        // '<link href="http://beta.programaseducativosaragon.es/index.php?title=Billionaires_csv?action=raw" type="text/csv" rel="exhibit/data" />';

        $this->mOutput->addHeadItem( $ex_script, $ex_script );
        $this->mOutput->addHeadItem( $maps_script, $maps_script );
        //$this->mOutput->addHeadItem( $billio, $billio );


        $ce = SpecialPage::getTitleFor( 'CargoExport' );
        $queryParams = $this->sqlQueriesToQueryParams( $sqlQueries );
        $queryParams['format'] = 'csv';
        $queryParams['limit'] = '1000';
        $dataurl = $ce->getFullURL( $queryParams );
        // Data imported as csv
        $datalink = "<link href=\"$dataurl\" type=\"text/csv\" rel=\"exhibit/data\" />";

        $this->mOutput->addHeadItem($datalink, $datalink);

        if ( array_key_exists( 'width', $displayParams ) ) {
            $width = $displayParams['width'];
            // Add on "px", if no unit is defined.
            if ( is_numeric( $width ) ) {
                $width .= "px";
            }
        } else {
            $width = "100%";
        }

        // Dumb attrs. The format must extract/deduce them.
        $attrs = array(
            'class' => 'cargoExhibit',
            'style' => "width: $width",
            'data-ex-role' => "view",
            'data-ex-view-class' =>"Map",
            'data-ex-latlng' => ".coords",
            'data-ex-center'=> "41.6561, -0.8773",
            'data-ex-zoom' => "8",
            'data-ex-map-height' => "540"
            );

        $text = Html::rawElement( 'div', $attrs, '' );

        return $text;
    }
}
