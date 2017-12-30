<?php

class SliderAdminModel extends Model {

    function __construct() {
        parent::__construct();
    }

    public function allSliders() {
        return $this->DB()->select(TABLE_PREFIX . "sliders");
    }

    function create($data) {
        return $this->DB()->insert(TABLE_PREFIX . "sliders", $data);
    }

    function update($data, $where) {
        $this->DB()->update(TABLE_PREFIX . "sliders", $data, $where);
    }

    function delete($id) {
        return $this->DB()->delete(TABLE_PREFIX . 'sliders', array(
            'id' => $id
        ));
    }

    function deleteBulk($where) {
        return $this->DB()->delete(TABLE_PREFIX . 'sliders', $where);
    }

    function getSliderByID($id) {
        $slider = $this->DB()->select(TABLE_PREFIX . "sliders", "*", array(
            'id' => $id,
        ));
        return $slider;
    }

    /**
     * @param string $search_query Query string
     * @return array 
     */
    function sliderRowsTable($search_query = "") {
        $where = array();
        if ($search_query != "") {
            $where = "title LIKE '%$search_query%' OR link LIKE '%$search_query%'";
        }
        return $this->DB()->select(TABLE_PREFIX . "sliders", "*", $where);
    }

}
