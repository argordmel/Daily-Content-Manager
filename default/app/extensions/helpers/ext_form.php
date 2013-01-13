<?php
/**
 * Dailyscript - app | web | media
 *
 * Extension para funciones especiales en los formularios
 *
 * @category    Extensions
 * @author      Iván D. Meléndez
 * @package     Helpers
 * @copyright   Copyright (c) 2010 Dailyscript Team (http://www.dailyscript.com.co)
 * @version     1.0
 */

class ExtForm extends Form {

    protected static $_counter = 0;
    protected static $_form = 0;

    public static function getFieldName($field) {

        $formField = explode('.', $field, 2);

        if(isset($formField[1])) {
            $id = "{$formField[0]}_{$formField[1]}";
            $name = "{$formField[0]}[{$formField[1]}]";
        } else {
            $id = "{$formField[0]}";
            $name = "{$formField[0]}";
        }
        
        return array('id' => $id, 'name' => $name);
    }

    protected static function _getAttrsClass($attrs, $input, $req = false, $ext = false) {

        if($input == 'form') {
            if(isset($attrs['class'])) {
                str_replace('daily',  '', $attrs['class'], $checked);
                $attrs['class'] = (!$checked) ? "daily ".$attrs['class'] : $attrs['class'];
            } else {
                $attrs['class'] = 'daily';
            }
            if(!isset($attrs['id'])) {
                $attrs['id'] = 'form-'.self::$_form;
            }
            if(!isset($attrs['name'])) {
                $attrs['name'] = 'form-'.self::$_form;
            }
            self::$_form++;
        } else {
            if(isset($attrs['class'])) {                
                /* Clases text, select, textarea, checkbox, radio, file */
                //$attrs['class'] = preg_match("/[a-z]*.$input.[a-z]*/i", $attrs['class']) ? $attrs['class'] : $input." ".$attrs['class'];
                $attrs['class'] = preg_match("/\b$input\b/i", $attrs['class']) ? $attrs['class'] : $input." ".$attrs['class'];
                
                /* Clase Field */
                str_replace('field',  '', $attrs['class'], $checked);
                $attrs['class'] = (!$checked) ? "field ".$attrs['class'] : $attrs['class'];                

                /* Clase requerido */
                str_replace('requerido',  '', $attrs['class'], $checked);
                $attrs['class'] = ($req == true && !$checked) ? "requerido ".$attrs['class'] : $attrs['class'];
               
            } else {
                if( ($input != 'checkbox') && ($input != 'radio') ){
                    $attrs['class'] = "field ".$input." full ";
                } else {
                    $attrs['class'] = "field ".$input." ";
                }
                
            }
        }
      
        return $attrs;
        
    }

    public static function _getValidationForm($valid,$extension) {
        static $js_form = true;
        $validation = '';
        if($js_form) {
            $js_form = false;
            if($valid) {
                $validation .= Tag::js('validar')."\n";
                $validation .= '<script type="text/javascript">$(function() {$("form.daily").submit(function(){ ';
                if($extension) {
                    $validation .= 'if(extValidarForm()) {return validarForm(this.name);} else {return false;}';
                } else {
                    $validation .= 'return validarForm(this.name);';
                }
                $validation .= '});});</script>';
            }
        }
        return $validation;
    }

    public static function label($field, $label='', $attrs='', $req = false, $err = false, $choice = false, $textarea = false, $range=70) {
        
        extract(self::getFieldName($field));

        if(is_array($attrs)) {
            $attrs = Tag::getAttrs($attrs);
        }

        $getLabel = '';

        if($label != '') {            
            if($choice) {
                $getLabel .= "<label for=\"$id".self::$_counter."\" class=\"choice\">$label";
                self::$_counter++;
            } else {
                $getLabel .= "<label for=\"$id\" $attrs>$label";
            }
            $getLabel .= ($req) ? "<span class=\"req\">*</span>" : '';
            $getLabel .= ($textarea) ? "<var id=\"rangeMaxMsg$id\">&nbsp;&nbsp;&nbsp; Tamaño máximo: $range</var> caracteres.&nbsp;&nbsp;&nbsp; <em class=\"currently\">Usuados: <var id=\"rangeUsedMsg$id\">0</var> caracteres.</em>" : '';
        }
        if( (!$choice) && ($err) ) {
            $getLabel .= ($label != '') ? "<br /><span class=\"err\" id=\"err_$id\">&nbsp;</span>" : "<label for=\"$id\"><span class=\"err\" id=\"err_$id\">&nbsp;</span>";
        }
        
        $getLabel .= "</label>";
        
        
        return $getLabel;
        
    }

    public static function open($action = null, $method = 'post', $attrs = null, $valid = false, $ext = false) {

        $form = self::_getValidationForm($valid,$ext);
        $attrs = self::_getAttrsClass($attrs, 'form', $valid, $ext );
        $form .= parent::open($action,$method,$attrs);

      return $form."\n";

    }

    public static function openMultipart($action = null, $attrs = null, $valid = false, $ext = false) {

        $form = self::_getValidationForm($valid,$ext);
        $form .= parent::openMultipart($action, $attrs);
        return $form."\n";
    }

    public static function close() {

        return parent::close()."\n";
        
    }

    public static function text($field, $attrs = null, $value = null, $label ='', $req = false, $err = false) {

        $attrs = self::_getAttrsClass($attrs, 'text', $req);
        $input  = "\n".parent::text($field, $attrs, $value)."\n";
        $input .= self::label($field, $label, null, $req, $err);

        return $input."\n";
        
    }

    public static function textUpper($field, $attrs = null, $value = null, $label ='', $req = false, $err = false) {

        if(isset($attrs['class'])) {
            str_replace('email',  '', $attrs['class'], $checked);
            if(!$checked) {
                if(!isset($attrs['onchange'])) {
                    $attrs['onchange'] = 'this.value=this.value.toUpperCase()';
                } else {
                    $attrs['onchange'] .= '; this.value=this.value.toUpperCase()';
                }
            }
        }
        
        $input  = self::text($field, $attrs, $value, $label, $req, $err)."\n";

        return $input;

    }

    public static function select($field, $data, $attrs = null, $value = null, $label = '', $req = false, $err = false) {

        $attrs = self::_getAttrsClass($attrs, 'select', $req);        
        $input  = "\n".parent::select($field, $data, $attrs, $value)."\n";
        $input .= self::label($field, $label, null, $req, $err);

        return $input."\n";
    }

    public static function check($field, $checkValue = null, $attrs = null, $checked=null, $label = '', $req = false, $err = false) {

        $attrs = self::_getAttrsClass($attrs, 'checkbox', false);
        $input  = "\n".parent::check($field, $checkValue, $attrs, $checked)."\n";
        $input .= self::label($field, $label, null, $req, $err, true);

        return $input."\n";

    }

    public static function radio($field, $checkValue = null, $attrs = null, $checked=null, $label = '', $req = false, $err = false) {

        $attrs = self::_getAttrsClass($attrs, 'radio', false);
        $input  = "\n".parent::radio($field, $checkValue, $attrs, $checked)."\n";
        $input .= self::label($field, $label, null, $req, $err, true);

        return $input."\n";

    }

    public static function hidden($field, $attrs = null, $value = null) {

        $input = "\n".parent::hidden($field, $attrs, $value)."\n";

        return $input."\n";

    }

    public static function pass($field, $attrs = null, $value = null, $label = '', $req = false, $err = false) {

        $attrs = self::_getAttrsClass($attrs, 'text', $req);
        $input  = "\n".parent::pass($field, $attrs, $value)."\n";
        $input .= self::label($field, $label, null, $req, $err);

        return $input."\n";
        
    }

    public static function dbSelect($field, $show = NULL, $data = NULL, $blank = 'Seleccione', $attrs = NULL, $value = NULL, $label = '', $req = false, $err = false) {

        $attrs = self::_getAttrsClass($attrs, 'select', $req);
        $input  = "\n".parent::dbSelect($field, $show, $data, $blank, $attrs, $value)."\n";
        $input .= self::label($field, $label, null, $req, $err);

        return $input."\n";

    }

    public static function dbCity($field, $data, $show, $blank = null, $attrs = null, $value = null, $label = '', $req = false, $err = false) {
        
        if(is_array($attrs)) {
            $attrs = Tag::getAttrs($attrs);
        }
        
        extract(parent::_getFieldData($field, $value === null), EXTR_OVERWRITE);

        if(is_null($blank)) {
            $options = '';
        }
        else {
            $options = '<option value="">' . htmlspecialchars($blank, ENT_COMPAT, APP_CHARSET) . '</option>';
        }
        
        foreach($data as $p) {
            $options .= "<option value=\"$p->id\"";
            if($p->id == $value) {
                $options .= ' selected="selected"';
            }
            $options .= '>' . $p->convencion. " | ".htmlspecialchars($p->$show, ENT_COMPAT, APP_CHARSET). '</option>';
        }

        $attrs = self::_getAttrsClass($attrs, 'select', $req);
        $input  =  "\n<select id=\"$id\" name=\"$name\" $attrs>$options</select>\n";
        $input .= self::label($field, $label, null, $req, $err);

        return $input."\n";

    }
    
    public static function textarea($field, $attrs = null,  $value = null, $label = '', $range=70, $req = false, $err = false) {
        
        extract(self::getFieldName($field));
        if($range > 0) {
            if(!isset($attrs['onkeyup'])) {
                $attrs['onkeyup'] = 'validateRange(\''.$id.'\')';
            } else {
                $attrs['onkeyup'] .= ', validateRange(\''.$id.'\')';
            }
        }

        $attrs = self::_getAttrsClass($attrs, 'textarea', $req);
        $input  = "\n".parent::textarea($field, $attrs, $value)."\n";
        $input .= self::label($field, $label, null, $req, $err, false, true, $range);
        
        return $input."\n";

    }

    public static function textareaUpper($field, $attrs = null,  $value = null, $label = '', $range=70, $req = false, $err = false) {

        if(!isset($attrs['onchange'])) {
            $attrs['onchange'] = 'this.value=this.value.toUpperCase()';
        } else {
            $attrs['onchange'] .= '; this.value=this.value.toUpperCase()';
        }
        
        $input = self::textarea($field, $attrs, $value, $label, $range, $req, $err);
        
        return $input;

    }

    public static function file($field, $attrs = null, $label = '', $req = false, $err = false) {

        $attrs = self::_getAttrsClass($attrs, 'file', $req);
        $input  = "\n".parent::file($field, $attrs)."\n";
        $input .= self::label($field, $label, null, $req, $err);

        return $input."\n";
    }
    
    public static function date($field, $attrs = null, $value = null, $label = '', $req = false, $err = false) {

        static $i = false;

        $input   =   '';

        if($i == false){
            $i = true;
            $input .=   "\n".Tag::js('calendario')."\n";
            $input .=  "<script type=\"text/javascript\"> $(function() { $(\".fecha\").datepicker({  }); }); </script>\n";
        }

        $attrs = self::_getAttrsClass($attrs, 'text', $req);
        $input .= "\n".parent::text($field, $attrs, $value)."\n";
        $input .= self::label($field, $label, null, $req, $err);
      
        return $input."\n";
      
  }
    
}

?>