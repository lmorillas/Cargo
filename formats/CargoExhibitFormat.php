<?php
/**
 * @author @lmorillas
 */


function fieldWithEq( $f ){
        $containsEquals = strpos( $f, '=' );
        if ( $containsEquals ){
            return  explode("=", $f)[1];
        }
        else{
            return $f;
        }
    }

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


function returnValue ( $keyVal ){
    $_kv = explode("=", $keyVal);
    return $_kv[count($_kv) - 1];
}

function fieldsForExhibit( $fields ){
    $containsEquals = strpos( $fields, '=' );
    if ( $containsEquals ){
        return implode(',', array_map( "returnValue", explode( ',', $fields )));
    }
    else{
        return $fields;
    }

}


class CargoExhibitFormat extends CargoDeferredFormat {

    function allowedParameters() {
        return array( 'height', 'width', 'zoom', 'lens','sort', 'view', 'columns', 'facets' );
    }

    function sortKey(){
        if ( array_key_exists( 'sort', $this->displayParams ) ) {
             $this->attrs['data-ex-orders'] = to_ex_param($this->displayParams['sort']);
         }
    }

    /**
    * @param string $param
    * @param string $attr
    */
    function checkParam($param, $attr){
        if ( array_key_exists( $param, $this->displayParams ) ) {
             $this->attrs['data-ex-' . $attr] = to_ex_param($this->displayParams[$param]);
         }

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
    }

    function createTimeline(){
        $timeline_script = '<link rel="exhibit-extension" href="http://api.simile-widgets.org/exhibit/current/extensions/time/time-extension.js"/>';
        $this->mOutput->addHeadItem( $timeline_script, $timeline_script );

        // div
        $this->attrs["data-ex-view-class"] = "Timeline";
        $this->attrs["data-ex-start"] = ".time";
        $this->attrs["data-ex-color-key"] = ".username";
        $this->attrs["data-ex-top-band-unit"] = "minute";
        $this->attrs["data-ex-top-band-pixels-per-unit"] = "140";
        $this->attrs["data-ex-bottom-band-pixels-per-unit"] = "500";
    }

    function createDefaultView () {
        $this->attrs = array();
        $this->attrs['data-ex-role'] = 'view';
        $this->sortKey();
    }

    function createTabular($fields){
        $this->attrs['data-ex-view-class'] = 'Tabular';

        $field_list =  explode( ',' , $fields);
        $field_list = array_map( "fieldWithEq", $field_list);

        $this->attrs["data-ex-columns"] = implode(',', array_map("concatenate_dot", $field_list));

        if ( array_key_exists( "labels", $this->displayParams ) ) {
            $this->attrs["data-ex-column-labels"] = $this->displayParams['labels'];
        }
        else {
            $this->attrs["data-ex-column-labels"] = implode(',', array_map("ucfirst", $field_list));
        }
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
        return Html::rawElement( 'div', array("class" => "facets"), $text);
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

    function createLens () {
        $text = "";
        if ( array_key_exists( 'lens', $this->displayParams ) ) {
                $lens = to_ex_param( $this->displayParams['lens'] );
                // Add on "px", if no unit is defined.
                 $attrs = array(
                    'data-ex-role' => "lens",
                    );
                $text = $text .  Html::rawElement( 'div', $attrs, $lens );
            }
        return $text;
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
        $this->displayParams = $displayParams;



        // Search
        $text = $text . $this->createSearch("Search");

        // Facets
        if ( array_key_exists( 'facets', $displayParams ) ) {
            $facets = $displayParams['facets'];
            $text = $text .  $this->createFacets( $facets );
            }


        // View
        $this->createDefaultView();
        if ( array_key_exists( 'view', $displayParams) ){
             $view = ucfirst( $displayParams['view'] );
             switch ($view) {
                case "Timeline":
                    $this->createTimeline();
                    break;
                case "Map":
                    $this->createMap();
                    break;
                case "Tabular":
                    $fields = $queryParams['fields'];
                    $this->createTabular($fields);
            }
        }
        // test others

        $text = $text .  Html::rawElement( 'div', $this->attrs );
        $text = $text . $this->createLens();

        return $text;
    }
}
