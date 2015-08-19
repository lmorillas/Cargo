<?php
/**
 * @author @lmorillas
 *
 * Adds exhibit format to cargo queries
 */



class CargoExhibitFormat extends CargoDeferredFormat {

    function allowedParameters() {
        return array( 'height', 'width', 'zoom', 'lens','sort', 'view', 'columns', 'facets', 'start', 'end', 'color', 'topunit', 'toppx', 'bottompx', 'latlng', 'zoom', 'center' );
    }

    static function fieldWithEq( $f ){
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

        return implode(',', array_map("CargoExhibitFormat::concatenate_dot", $params ) );
    }


    function sortKey($attrs){
        if ( array_key_exists( 'sort', $this->displayParams ) ) {
             $attrs['data-ex-orders'] = $this->to_ex_param($this->displayParams['sort']);
         }
    }


    function createMap(){
        $maps_script = '<link rel="exhibit-extension" href="http://api.simile-widgets.org/exhibit/current/extensions/map/map-extension.js"/>';
        $this->mOutput->addHeadItem( $maps_script, $maps_script );

         // div
        $attrs = array();
        $attrs['data-ex-role'] = 'view';
        $attrs["data-ex-view-class"] = "Map";

        if ( array_key_exists( "latlng", $this->displayParams ) ) {
            $attrs["data-ex-latlng"] = $this->concatenate_dot($this->displayParams['latlng']);
        }
        if ( array_key_exists( "color", $this->displayParams ) ) {
            $attrs["data-ex-color-key"] = $this->concatenate_dot($this->displayParams['color']);
        }
        if ( array_key_exists( "center", $this->displayParams ) ) {
            $attrs["data-ex-center"] = $this->displayParams['center'];
        }
        else {
            $attrs["data-ex-autoposition"] = "true";
        }
        if ( array_key_exists( "zoom", $this->displayParams ) ) {
            $attrs["data-ex-zoom"] = $this->displayParams['zoom'];
        }
        return Html::rawElement( 'div', $attrs );
    }

    function createTimeline(){
        // timeline script
        $timeline_script = '<link rel="exhibit-extension" href="http://api.simile-widgets.org/exhibit/current/extensions/time/time-extension.js"/>';
        $this->mOutput->addHeadItem( $timeline_script, $timeline_script );

        // div
        $attrs = array();
        $attrs['data-ex-role'] = 'view';
        $attrs["data-ex-view-class"] = "Timeline";

        if ( array_key_exists( "start", $this->displayParams ) ) {
            $attrs["data-ex-start"] = $this->concatenate_dot($this->displayParams['start']);
        }
        if ( array_key_exists( "end", $this->displayParams ) ) {
            $attrs["data-ex-end"] = $this->concatenate_dot($this->displayParams['end']);
        }
        if ( array_key_exists( "color", $this->displayParams ) ) {
            $attrs["data-ex-color-key"] = $this->concatenate_dot($this->displayParams['color']);
        }
        if ( array_key_exists( "topunit", $this->displayParams ) ) {
            $attrs["data-ex-top-band-unit"] = $this->displayParams['topunit'];
        }
        if ( array_key_exists( "toppx", $this->displayParams ) ) {
            $attrs["data-ex-top-band-pixels-per-unit"] = $this->displayParams['toppx'];
        }
        if ( array_key_exists( "bottompx", $this->displayParams ) ) {
            $attrs["data-ex-bottom-band-pixels-per-unit"] = $this->displayParams['bottompx'];
        }
        return Html::rawElement( 'div', $attrs );
    }

    function createDefaultView () {
        $attrs = array();
        $attrs['data-ex-role'] = 'view';
        $this->sortKey($attrs);
        return Html::rawElement( 'div', $attrs );
    }

    /**
    * @param $fields string
    *
    */
    function createTabular($fields){
        $attrs = array();
        $attrs['data-ex-role'] = 'view';
        $attrs['data-ex-view-class'] = 'Tabular';
        $attrs["data-ex-paginate"] = "true";

        $field_list =  explode( ',' , $fields);
        $field_list = array_map( "CargoExhibitFormat::fieldWithEq", $field_list);  // fields with =

        $attrs["data-ex-columns"] = implode(',',
            array_map("CargoExhibitFormat::concatenate_dot", $field_list));

        if ( array_key_exists( "labels", $this->displayParams ) ) {
            $attrs["data-ex-column-labels"] = $this->displayParams['labels'];
        }
        else {
            $attrs["data-ex-column-labels"] = implode(',', array_map("ucfirst", $field_list));
        }
        //$this->sortKey($attrs);
        return Html::rawElement( 'div', $attrs );
    }

    function createFacets( $facets ){
    // explode facets and create the div for each of them
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
        if ( array_key_exists( 'lens', $this->displayParams ) ) {
                $lens = $this->to_ex_param( $this->displayParams['lens'] );
                // Add on "px", if no unit is defined.
                 $attrs = array(
                    'data-ex-role' => "lens",
                    'style' => "display: None;"
                    );
                return Html::rawElement( 'div', $attrs, $lens );
            }
        return '';
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

        // resulting output
        $text = "";

        $this->hasCoordinates( $sqlQueries );

        // Add necessary JS scripts.
        //  Exhibit Scripts
        $ex_script = '<script src="http://api.simile-widgets.org/exhibit/current/exhibit-api.js"></script>';
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
            $facets = array_map('trim', explode( ',' , $facets));
            $text = $text .  $this->createFacets( $facets );
            }
        else{
            $fields = $queryParams['fields'];
            $field_list =  explode( ',' , $fields);
            $field_list = array_map( "CargoExhibitFormat::fieldWithEq", $field_list);  // fields with =
            $text = $text .  $this->createFacets( array_slice($field_list, 0, 3));
        }


        // View
        $this->views = array();

        if ( array_key_exists( 'view', $displayParams) ){
            $this->views = array_map( 'ucfirst', explode(',', $displayParams['view']));
        }
        else {  // default views
            $this->automateViews($sqlQueries);
        }

        $text_views = '';

        foreach($this->views as $view){
            switch ( $view ) {
                case "Timeline":
                    $text_views = $text_views . $this->createTimeline();
                    break;
                case "Map":
                    $text_views = $text_views . $this->createMap();
                    break;
                case "Tabular":
                    $fields = $queryParams['fields'];
                    $text_views = $text_views . $this->createTabular($fields);
                }
            }

        if ( count($this->views) > 1 ){
            $text = $text . Html::rawElement( 'div',
                array('data-ex-role'=>"viewPanel"),
                $text_views);
        }
        else {
            $text = $text . $text_views;
        }

        // add generic lens
        $text = $text . $this->createLens();

        return $text;
    }

    function automateViews( $sqlQueries ){
        $tmp = $this->hasCoordinates( $sqlQueries );
        if ( count($tmp) > 0 ){
            $this->displayParams['latlng'] = $tmp[0];
            $this->views[] = 'Map';
        }
        $tmp = $this->hasDate( $sqlQueries );
        if ( count($tmp) > 0 ){
            $this->views[] = 'Timeline';
            $this->displayParams['start'] = $tmp[0];
            /*  think on it before
            if (count($tmp) > 1) {
                $this->displayParams['end'] = $tmp[0];
            }
            */
        }
        if (count($this->views) == 0)
            $this->views[] = 'Tabular';  // default view?
    }

    function hasCoordinates( $sqlQueries ){
        $coordinatesFields = array();

        foreach ( $sqlQueries as $query){
            $fieldDescriptions = $query->mFieldDescriptions;
            // print_r( $fieldDescriptions );
            foreach ( $fieldDescriptions as $field => $description ) {
                if ( $description->mType == 'Coordinates' ) {
                    $coordinatesFields[] = $field;
                }
            }
        }
        return $coordinatesFields;
    }

    function hasDate($sqlQueries){
        $dateFields = array();

        foreach ( $sqlQueries as $query){
            $fieldDescriptions = $query->mFieldDescriptions;
            foreach ( $fieldDescriptions as $field => $description ) {
                if ( $description->mType == 'Date' || $description->mType == 'Datetime' ) {
                    $dateFields[] = $field;
                }
            }
        }
        return $dateFields;
    }


}
