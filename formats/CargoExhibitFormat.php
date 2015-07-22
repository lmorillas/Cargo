<?php
/**
 * @author @lmorillas
 */


    /**
     * @param string $p
     * @return string
     **/
function concatenate_dot ($p) { return '.' . trim($p); }

    /**
     * @param array $param_list
     * @return array
     **/
function to_ex_param( $param_list ) {
    $params = explode( ',' , $param_list);

    return implode(',', array_map("concatenate_dot", $params ) );
}


class CargoExhibitFormat extends CargoDeferredFormat {

    function allowedParameters() {
        return array( 'height', 'width', 'zoom', 'lens','sort', 'view', 'columns', 'facets' );
    }


    function createMap(){
        $maps_script = '<link rel="exhibit-extension" href="http://api.simile-widgets.org/exhibit/current/extensions/map/map-extension.js"/>';
        $this->mOutput->addHeadItem( $maps_script, $maps_script );

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

/*
        if ( array_key_exists( 'width', $displayParams ) ) {
            $width = $displayParams['width'];
            // Add on "px", if no unit is defined.
            if ( is_numeric( $width ) ) {
                $width .= "px";
            }
        } else {
            $width = "100%";
        }
*/
    }

    function createTimeline(){
        $timeline_script = '<link rel="exhibit-extension" href="http://api.simile-widgets.org/exhibit/current/extensions/time/time-extension.js"/>';
        $this->mOutput->addHeadItem( $timeline_script, $timeline_script );

        // div
        $attrs = array(
            "data-ex-role" => "view",
            "data-ex-view-class" => "Timeline",
            "data-ex-start" => ".time",
            "data-ex-color-key" => ".username",
            "data-ex-top-band-unit" => "minute",
            "data-ex-top-band-pixels-per-unit" => "140",
            "data-ex-bottom-band-pixels-per-unit" => "500"
            );
        return Html::rawElement( 'div', $attrs );
    }

    function createFacets( $facets ){
    // explode facets and create the div for each of them
        $facets = explode( ',' , $facets);
        $text = '';
        foreach ($facets as $f) {
             $attrs = array(
                'data-ex-role' => "facet",
                'data-ex-collapsible' => "true",
                'data-ex-collapsed' => "true",
                'data-ex-expression' => '.' . $f,
                'data-ex-show-missing' => 'false',
                'data-ex-facet-label' => ucfirst($f)
                );
        $text = $text . Html::rawElement( 'div', $attrs);
        }
        return $text;
    }

    /**
    * @param string $title
    * @return string
    */
    function createSearch ( $title ) {
        $attrs = array(
            'data-ex-role' => "exhibit-facet",
            'data-ex-facet-class' => "TextSearch",
            'data-ex-facet-label' => $title
            );
        return Html::rawElement( 'div', $attrs);

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
        // resulting output
        $text = "";
        $this->mOutput->addHeadItem( $ex_script, $ex_script );

        $ce = SpecialPage::getTitleFor( 'CargoExport' );
        $queryParams = $this->sqlQueriesToQueryParams( $sqlQueries );

        // format csv
        $queryParams['format'] = 'csv';
        $queryParams['limit'] = '1000';
        $dataurl = $ce->getFullURL( $queryParams );

        // Data imported as csv
        $datalink = "<link href=\"$dataurl\" type=\"text/csv\" rel=\"exhibit/data\" />";
        $this->mOutput->addHeadItem($datalink, $datalink);

        if ( array_key_exists( 'lens', $displayParams ) ) {
            $lens = to_ex_param( $displayParams['lens'] );
            // Add on "px", if no unit is defined.
             $attrs = array(
                'data-ex-role' => "lens",
                );
            $text = $text .  Html::rawElement( 'div', $attrs, $lens );
        }

        // Search
        $text = $text . $this->createSearch("Search");

        // Facets
        if ( array_key_exists( 'facets', $displayParams ) ) {
            $facets = $displayParams['facets'];
            $text = $text .  $this->createFacets( $facets );
            }
        }

        // View
        if ( array_key_exists( 'view', $displayParams) ){
             $view = ucfirst( $displayParams['view'] );
             switch ($view) {
                case "Timeline":
                    $text = $text . $this->createTimeline();
                        break;
            }
        }

        /*
        $attrs = array(
            'class' => 'cargoExhibit',
            'data-ex-role' => "view",
            );

        if ( array_key_exists( 'sort', $displayParams ) ) {
             $attrs['data-ex-orders'] = to_ex_param($displayParams['sort']);
         }

         if ( array_key_exists( 'columns', $displayParams ) ) {
             $attrs['data-ex-columns'] = to_ex_param($displayParams['columns']);
         }

        $text = $text . Html::rawElement( 'div', $attrs, '' );
        */

        return $text;
    }
}
