<?php
/**
 * @author @lmorillas
 *
 * Adds exhibit format to cargo queries
 */


class CargoExhibitFormat extends CargoDeferredFormat {

    function allowedParameters() {
        return array( 'height', 'width', 'zoom', 'sort', 'view', 'columns', 'facets', 'start', 'end', 'color', 'topunit', 'toppx', 'bottompx', 'latlng', 'zoom', 'center' );
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


    function sortKey(){
        if ( array_key_exists( 'sort', $this->displayParams ) ) {
             $attrs['data-ex-orders'] = $this->to_ex_param($this->displayParams['sort']);
         }
    }


    function createMap(){
        $maps_script = '<link rel="exhibit-extension" href="http://api.simile-widgets.org/exhibit/current/extensions/map/map-extension.js"/>';
        #$maps_script = '<script src="http://api.simile-widgets.org/exhibit/current/extensions/map/map-extension.js"></script>';
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
        return Html::element( 'div', $attrs );
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
        return Html::element( 'div', $attrs );
    }

    /**
    * @param $fields_list array
    *
    */
    function createTabular($field_list){
        $attrs = array();
        $attrs['data-ex-role'] = 'view';
        $attrs['data-ex-view-class'] = 'Tabular';
        $attrs["data-ex-paginate"] = "true";

        $attrs["data-ex-columns"] = implode(',',
            array_map("CargoExhibitFormat::concatenate_dot", $field_list));

        if ( array_key_exists( "labels", $this->displayParams ) ) {
            $attrs["data-ex-column-labels"] = $this->displayParams['labels'];
        }
        else {
            $attrs["data-ex-column-labels"] = implode(',', array_map("ucfirst", $field_list));
        }

        if ( array_key_exists( 'sort', $this->displayParams ) ) {
             $attrs['data-ex-orders'] = $this->to_ex_param($this->displayParams['sort']);
         }

        return Html::element( 'div', $attrs );
    }

    function createFacets( $facets ){
    // explode facets and create the div for each of them
        $text = '';
        $text .= $this->createSearch( "Search" );
        foreach ($facets as $f) {
             $attrs = array(
                'data-ex-role' => "facet",
                'data-ex-collapsible' => "true",
                'data-ex-collapsed' => "true",
                'data-ex-expression' => '.' . $f,
                'data-ex-show-missing' => 'false',
                'data-ex-facet-label' => ucfirst($f),
                'style' => "float: left; width: 24%; margin: 0 1% 0 0;"
                );
        $text .=  Html::element( 'div', $attrs);
        }
        return Html::rawElement( 'div', array("class" => "facets", "style"=>"overflow: hidden; width: 100%;"), $text);
    }

    /**
    * @param string $title
    * @return string
    */

    function createSearch ( $title ) {
        $attrs = array(
            'data-ex-role' => "exhibit-facet",
            'data-ex-facet-class' => "TextSearch",
            'data-ex-facet-label' => $title,
            'style' => "float: left; width: 24%; margin: 0 1% 0 0;"
            );
        return Html::element( 'div', $attrs);
    }

    function createLens ($_field_list) {
        $lens = '<table data-ex-role="lens" class="cargoTable" style="display: none;">';
        $lens .= '<caption><strong data-ex-content=".label"></strong></caption>';
        foreach( $_field_list as $field) {
            if ($field != "label" and strpos( $field, '__' ) === false and
               strpos( $field, '  ' ) === false) {
               $th = "<strong>" . ucfirst( $field ) . "</strong>";
               $lens .= "<tr data-ex-if-exists=\".$field\"><td>$th</td><td data-ex-content=\".$field\"></td></tr>";
            }
        }
        $lens .= '</table>';
        return $lens;
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

        $this->mOutput->addModules( 'ext.cargo.exhibit' );
        $this->mOutput->addModuleStyles( 'ext.cargo.main' );

        // resulting output
        $text = "";

        // Now js is loaded after page is ready
        // $ex_script = '<script src="http://api.simile-widgets.org/exhibit/HEAD/exhibit-api.js?autoCreate=false&amp;bundle=false"></script>';
        $ex_script = '<script src="http://api.simile-widgets.org/exhibit/current/exhibit-api.js?autoCreate=false"></script>';
        $this->mOutput->addHeadItem( $ex_script, $ex_script );

        $ce = SpecialPage::getTitleFor( 'CargoExport' );

        $field_list = array();
        foreach ( $sqlQueries as $sqlQuery ) {
            foreach ( $sqlQuery->mAliasedFieldNames as $alias => $fieldName ) {
                $field_list[] = $alias;
            }
        }

        $csv_properties = '';
        if ( ! in_array( "label", $field_list ) ){
            // first field will be label!
            $field_list[0] = 'label';
            $csv_properties = 'data-ex-properties="' . implode( ',', $field_list) . '"';
        }

        $queryParams = $this->sqlQueriesToQueryParams( $sqlQueries );

        // format csv
        $queryParams['format'] = 'csv';
        $queryParams['limit'] = '1000';

        $dataurl = htmlentities( $ce->getFullURL( $queryParams ) );

        // Data imported as csv
        $datalink = "<link href=\"$dataurl\" type=\"text/csv\" rel=\"exhibit/data\" data-ex-has-column-titles=\"true\" $csv_properties />";

        $this->mOutput->addHeadItem($datalink, $datalink);

        $this->displayParams = $displayParams;

        // Search
        // $text .=  $this->createSearch("Search");

        // lense
        $text .= $this->createLens($_field_list);

        // Facets
        if ( array_key_exists( 'facets', $displayParams ) ) {
            $facets = $displayParams['facets'];
            $facets = array_map('trim', explode( ',' , $facets));
            $text .= $this->createFacets( $facets );
            }
        else{
            $text .= $this->createFacets( array_slice($field_list, 0, 3));
        }

        // View
        $this->views = array();

        if ( array_key_exists( 'view', $displayParams) ){
            $this->views = array_map( 'ucfirst', explode(',', $displayParams['view']));
        }
        else {  // default views:  $this->views
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
                    $text_views = $text_views . $this->createTabular($field_list);
                }
            }

        if ( count($this->views) > 1 ){
            $text .=  Html::rawElement( 'div',
                array('data-ex-role'=>"viewPanel"),
                $text_views);
        }
        else {
            $text .=  $text_views;
        }

        return $text;
    }

    /**
    * Initializes $this->views[]
    */
    function automateViews( $sqlQueries ){
        // map ?
        $tmp = $this->hasCoordinates( $sqlQueries );
        if ( count($tmp) > 0 ){
            $this->displayParams['latlng'] = $tmp[0];
            $this->views[] = 'Map';
        }
        // timeline ?
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
