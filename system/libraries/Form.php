<?php

class Form {

    private $title;

    /**
     * Form's attributes
     * 
     * @var array
     */
    private $attr = array();

    /**
     * Field list
     *
     * @var array
     */
    private $children = array();

    /**
     * List of Element
     * 
     * @var array
     */
    private $elements = array();

    /**
     * Type list of input field
     *
     * @var array
     */
    private $inputTypes = array(
        'button', 'checkbox', 'color', 'date', 'datetime', 'datetime-local', 'email', 'file', 'hidden', 'image', 'month',
        'number', 'password', 'radio', 'range', 'reset', 'search', 'submit', 'tel', 'text', 'time', 'url', 'week',
    );

    function __construct($title = "", $attr = array()) {
        $this->title = $title;
        $this->attr = $attr;
    }

    /**
     * Add a field to form
     * 
     * @param type $child Field name
     * @param type $type Type of the field
     * @param array $options
     */
    public function add($child, $type = null, array $options = array()) {
        $this->children[$child] = array(
            'type' => $type,
            'options' => $options,
        );

        $data = "";
        $description = "";
        if (array_key_exists('data', $options)) {
            if (is_string($options['data'])) {
                $data = htmlentities(stripslashes($options['data']), ENT_QUOTES | ENT_IGNORE, 'UTF-8', FALSE);
            }else{
                $data= $options['data'];
            }
        }
        if (array_key_exists('description', $options))
            $description = $options['description'];
        if (!array_key_exists('attr', $options))
            $options['attr'] = array();
        if (!array_key_exists('class', $options['attr'])) {
            if ($type != 'button')
                $options['attr']['class'] = "form-control";
            else
                $options['attr']['class'] = "btn";
        }else {
            if ($type != 'button')
                $options['attr']['class'] .= " form-control";
            else
                $options['attr']['class'] .= " btn";
        }

        $label = (is_array($options) && array_key_exists('label', $options)) ? $options['label'] : ucfirst($child);
        // Render input fields
        if ($type == "choice") { //Render group type
            if (is_array($options) and array_key_exists('choices', $options) and is_array($options['choices'])) {
                $choices = $options['choices'];
                $multiple = false;
                $expanded = false;
                $inline = FALSE;
                if (array_key_exists('multiple', $options) and $options['multiple'] == true)
                    $multiple = true;
                if (array_key_exists('expanded', $options) and $options['expanded'] == true)
                    $expanded = true;
                if (array_key_exists('inline', $options) and $options['inline'] == true)
                    $inline = true;

                $before = '<div class="form-group">
                            <label for="' . $child . '" class="col-sm-2 control-label">' . $label . ':</label>
                        <div class="col-sm-5">';
                $after = "</div></div>";

                if (!$multiple and ! $expanded) { // Render selectbox
                    $select = $before;
                    $select .= "<select name=\"{$child}\" id=\"{$child}\" " . $this->implodeAttr($options['attr']) . ">";
                    foreach ($choices as $k => $v) {
                        if ($data == $k)
                            $select .= "<option value=\"{$k}\" selected=\"selected\">{$v}</option>";
                        else
                            $select .= "<option value=\"{$k}\">{$v}</option>";
                    }
                    $select .= "</select>";
                    if ($description != "") {
                        $select .= '<div class="help-block">' . $description . '</div>';
                    }
                    $select .= $after;
                    $this->elements[$child] = $select;
                } elseif ($multiple and ! $expanded) { // Render listbox
                    $listbox = $before;
                    $listbox .= "<select name=\"{$child}[]\" id=\"{$child}\"" . $this->implodeAttr($options['attr']) . " multiple=\"multiple\">";
                    foreach ($choices as $k => $v) {
                        if (is_array($data) and in_array($k, $data))
                            $listbox .= "<option value=\"{$k}\" selected=\"selected\">{$v}</option>";
                        else
                            $listbox .= "<option value=\"{$k}\">{$v}</option>";
                    }
                    $listbox .= "</select>";
                    if ($description != "") {
                        $listbox .= '<div class="help-block">' . $description . '</div>';
                    }
                    $listbox .= $after;
                    $this->elements[$child] = $listbox;
                } elseif ($multiple and $expanded) { // Render list checkbox
                    $options['attr']['class'] = str_replace("form-control", "", $options['attr']['class']);
                    $listcheckbox = $before;
                    $listcheckbox .= "<div id=\"{$child}\"" . $this->implodeAttr($options['attr']) . ">";
                    foreach ($choices as $k => $v) {
                        if ($inline == true) {
                            $listcheckbox .= '<label class="checkbox-inline">';
                        } else {
                            $listcheckbox .= '<div class="checkbox"><label>';
                        }
                        if (is_array($data) and in_array($k, $data)) {
                            $listcheckbox .= "<input type=\"checkbox\" id=\"{$child}_{$k}\" name=\"{$child}[]\" value=\"{$k}\" checked=\"checked\" /> {$v} ";
                        } else {
                            $listcheckbox .= "<input type=\"checkbox\" id=\"{$child}_{$k}\" name=\"{$child}[]\" value=\"{$k}\" /> {$v} ";
                        }
                        if ($inline == true) {
                            $listcheckbox .= "</label>";
                        } else {
                            $listcheckbox .= "</label></div>";
                        }
                    }
                    $listcheckbox .= "</div>";
                    if ($description != "") {
                        $listcheckbox .= '<div class="help-block">' . $description . '</div>';
                    }
                    $listcheckbox .= $after;
                    $this->elements[$child] = $listcheckbox;
                } elseif (!$multiple and $expanded) { // Render list radio
                    $options['attr']['class'] = str_replace("form-control", "", $options['attr']['class']);
                    $listradio = $before;
                    $listradio .= "<div id=\"{$child}\"" . $this->implodeAttr($options['attr']) . ">";
                    foreach ($choices as $k => $v) {
                        if ($inline == true) {
                            $listradio .= '<label class="radio-inline">';
                        } else {
                            $listradio .= '<div class="radio"><label>';
                        }
                        if ($data == $k) {
                            $listradio .= "<input type=\"radio\" id=\"{$child}_{$k}\" name=\"{$child}\" value=\"{$k}\" checked=\"checked\" /> {$v} ";
                        } else {
                            $listradio .= "<input type=\"radio\" id=\"{$child}_{$k}\" name=\"{$child}\" value=\"{$k}\" /> {$v} ";
                        }
                        if ($inline == true) {
                            $listradio .= "</label>";
                        } else {
                            $listradio .= "</label></div>";
                        }
                    }
                    $listradio .= "</div>";
                    if ($description != "") {
                        $listradio .= '<div class="help-block">' . $description . '</div>';
                    }
                    $listradio .= $after;
                    $this->elements[$child] = $listradio;
                }
            }
        } elseif (in_array($type, array('textarea',))) {
            $input = '<div class="form-group">
                        <label for="' . $child . '" class="col-sm-2 control-label">' . $label . ':</label>
                    <div class="col-sm-10">';
            $input .= "<{$type} id=\"{$child}\" name=\"{$child}\"" . $this->implodeAttr($options['attr']) . ">";
            $input .= $data;
            $input .= "</{$type}>";
            if ($description != "") {
                $input .= '<div class="help-block">' . $description . '</div>';
            }
            $input .= "</div></div>";
            $this->elements[$child] = $input;
        } elseif ($type == 'upload') {
            $input = '<div class="form-group">
                    <label for="' . $child . '" class="col-sm-2 control-label">' . $label . ':</label>
                    <div class="col-sm-5">';
            $input .= '<div class="input-group">';
            $input .= "<input type=\"text\" name=\"{$child}\" id=\"{$child}\"";
            $input .= $this->implodeAttr($options['attr']);
            $input .= " value=\"{$data}\" ";
            $input .= "/>";
            $input .= '<span class="input-group-btn">';
            $input .= '<button class="btn btn-default" type="button"'
                    . $this->implodeAttr($options['btn'])
                    . '>Upload</button>';
            $input .= "</span></div>";
            if ($description != "") {
                $input .= '<div class="help-block">' . $description . '</div>';
            }
            $input .= "</div></div>";
            $this->elements[$child] = $input;
        } elseif ($type == 'hidden') {
            $input = "<input type=\"hidden\" name=\"{$child}\" id=\"{$child}\"";
            $input .= $this->implodeAttr($options['attr']);
            $input .= " value=\"{$data}\" ";
            $input .= "/>";
            $this->elements[$child] = $input;
        } elseif ($type == 'static') {
            $input = '<div class="form-group">
                    <label for="' . $child . '" class="col-sm-2 control-label">' . $label . ':</label>
                    <div class="col-sm-5">';
            $input .= '<p class="form-control-static"';
            $input .= $this->implodeAttr($options['attr']) . ">";
            $input .= $data;
            $input .= "</p>";
            if ($description != "") {
                $input .= '<div class="help-block">' . $description . '</div>';
            }
            $input .= "</div></div>";
            $this->elements[$child] = $input;
        } else {
            $input = '<div class="form-group">
                    <label for="' . $child . '" class="col-sm-2 control-label">' . $label . ':</label>
                    <div class="col-sm-5">';
            if (in_array($type, $this->inputTypes)) {
                $input .= "<input type=\"{$type}\" name=\"{$child}\" id=\"{$child}\"";
            } else {
                $input .= "<input type=\"text\" name=\"{$child}\" id=\"{$child}\"";
            }
            $input .= $this->implodeAttr($options['attr']);
            $input .= " value=\"{$data}\" ";
            $input .= "/>";
            if ($description != "") {
                $input .= '<div class="help-block">' . $description . '</div>';
            }
            $input .= "</div></div>";
            $this->elements[$child] = $input;
        }

        return $this;
    }

    /**
     * Get element in form
     * 
     * @param string $child Field name
     * @return string HTML
     */
    public function get($child) {
        return $this->elements[$child];
    }

    /**
     * Implode attribute array to string
     * 
     * @param array $attr Attributes
     * @return string
     */
    public function implodeAttr($attr = array()) {
        $str = "";
        if (is_array($attr)) {
            foreach ($attr as $key => $value) {
                $value = htmlentities(stripslashes($value));
                $str .= " $key=\"$value\"";
            }
        }
        return $str;
    }

    /**
     * Create form view with HTML format
     * @return string HTML
     */
    public function createView() {
        $view = "<form" . $this->implodeAttr($this->attr) . ">";
        $view .= '<div class="panel panel-primary">';
        $view .= '<div class="panel-heading">' . $this->title . '</div>';
        $view .= '<div class="panel-body">';

        // Each fields
        foreach ($this->children as $key => $value) {
            $view .= $this->elements[$key];
        }

        $view .= '</div>
                <div class="panel-footer">
                    <div class="row">
                        <div class="col-sm-2"></div>
                        <div class="col-sm-5">
                            <input type="submit" class="btn btn-primary" value="Submit" />
                        </div>
                    </div>
                </div>';
        $view .= "</div></form>";
        return $view;
    }

}
