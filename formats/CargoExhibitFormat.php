<?php
/**
 * @author @lmorillas
 */

function concatenate_dot ($p) { return '.' . $p; }

function to_ex_param( $param_list ) {
    $params = explode( ',' , $param_list);
    return implode(',' array_map($params, concatenate_dot ));

}

class CargoExhibitFormat extends CargoDeferredFormat {

    function allowedParameters() {
        return array( 'height', 'width', 'zoom', 'lens','sort', 'view', 'columns' );
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
        // resulting output
        $text = "";
        $this->mOutput->addHeadItem( $ex_script, $ex_script );
        $this->mOutput->addHeadItem( $maps_script, $maps_script );

        $ce = SpecialPage::getTitleFor( 'CargoExport' );
        $queryParams = $this->sqlQueriesToQueryParams( $sqlQueries );

        // format csv
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

        if ( array_key_exists( 'lens', $displayParams ) ) {
            $lens = to_ex_param( $displayParams['lens'] );
            // Add on "px", if no unit is defined.
             $attrs = array(
                'data-ex-role' => "lens",
                );
            $text = $text .  Html::rawElement( 'div', $attrs, $lens );
        }

        $text = $text . <<<END
<div class="ext_search" data-ex-role="exhibit-facet" data-ex-facet-class="TextSearch" data-ex-facet-label="Search in the map"></div>
END;

        // Dumb attrs. The format must extract/deduce them.
        $attrs_map = array(
            'class' => 'cargoExhibit',
            'style' => "width: $width",
            'data-ex-role' => "view",
            'data-ex-view-class' =>"Map",
            'data-ex-latlng' => ".coords",
            'data-ex-center'=> "41.6561, -0.8773",
            'data-ex-zoom' => "8",
            'data-ex-map-height' => "540"
            );

        $attrs = array(
            'class' => 'cargoExhibit',
            'data-ex-role' => "view",
            );

        if ( array_key_exists( 'view', $displayParams) ){
            $attrs['data-ex-view-class'] = ucfirst( $displayParams['view'] );
        }

        if ( array_key_exists( 'sort', $displayParams ) ) {
             $attrs['data-ex-orders'] = to_ex_param($displayParams['sort']);
         }

         if ( array_key_exists( 'columns', $displayParams ) ) {
             $attrs['data-ex-columns'] = to_ex_param($displayParams['columns']);
         }

        $text = $text . Html::rawElement( 'div', $attrs, '' );



            return $text;
    }
}
