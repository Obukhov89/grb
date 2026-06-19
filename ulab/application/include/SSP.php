<?php


class SSP {
    static function db()
    {
        global $DB;

        return $DB;
    }

    //static function simple ( $request, $conn, $table, $primaryKey, $columns )
    static function simple ($request = '', $conn = [], $table = '', $primaryKey = '', $columns = [], $whereCustom = '')
    {

//        $bindings = array();
        $db = self::db();

        // Build the SQL query string from the request
//        $limit = self::limit( $request, $columns );
//        $order = self::order( $request, $columns );
//        $where = self::filter( $request, $columns, $bindings );

        /*if ($whereCustom) {
            if ($where) {
                $where .= ' AND ' . $whereCustom;
            } else {
                $where .= 'WHERE ' . $whereCustom;
            }
        }*/

        // Main query to actually get the data
//        $data = self::sql_exec( $db, $bindings,
//            "SELECT `".implode("`, `", self::pluck($columns, 'db'))."`
//			 FROM `$table`
//			 $where
//			 $order
//			 $limit"
//        );

        // Data set length after filtering
//        $resFilterLength = self::sql_exec( $db, $bindings,
//            "SELECT COUNT(`{$primaryKey}`)
//			 FROM   `$table`
//			 $where"
//        );
//        $recordsFiltered = $resFilterLength[0][0];

        // Total data set length
//        $resTotalLength = self::sql_exec( $db,
//            "SELECT COUNT(`{$primaryKey}`)
//			 FROM   `$table`"
//        );
//        $recordsTotal = $resTotalLength[0][0];

        /*
         * Output
         */
//        return array(
//            "draw"            => isset ( $request['draw'] ) ?
//                intval( $request['draw'] ) :
//                0,
//            "recordsTotal"    => intval( $recordsTotal ),
//            "recordsFiltered" => intval( $recordsFiltered ),
//            "data"            => self::data_output( $columns, $data )
//        );
    }
}