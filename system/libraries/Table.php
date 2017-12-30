<?php

class Table {

    public $caption;
    private $columns = array();
    private $rows;
    private $pagenavi;

    public function __construct($caption = "") {
        $this->caption = $caption;
    }

    /**
     * Define the columns that are going to be used in the table
     * @param array $columns the array of columns to use with the table Description
     * @return array
     */
    public function add_columns($columns = array()) {
        return $this->columns = $columns;
    }

    /**
     * Add rows of records in the table
     * @return string
     */
    public function add_rows($row) {
        $this->rows = $row;
    }

    public function add_pagenavi($html) {
        $this->pagenavi = $html;
    }

    /**
     * Render HTML
     * @return string
     */
    public function createView() {
        $table = '<div class="panel panel-primary">';

        if ($this->caption and $this->caption != '')
            $table .= '<div class="panel-heading">' . $this->caption . '</div>';

        $table .= '<div class="panel-body">
            <div class="table-responsive">
                <table class="table table-striped" style="margin-bottom:0;"><thead><tr>';

        foreach ($this->columns as $key => $value) {
            $table .= '<th id="' . $key . '">' . $value . '</th>';
        }

        $table .= "</tr></thead><tbody>" . $this->rows . "</tbody></table></div></div>";

        if ($this->pagenavi and $this->pagenavi != "") {
            $table .= '<div class="panel-footer">';
            $table .= $this->pagenavi;
            $table .= "</div>";
        }

        $table .= "</div>";

        return $table;
    }

}
