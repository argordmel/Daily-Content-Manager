<?php
/*
 * Descripcion clase para el manejo de fechas
 *
 *
 * @autor       Ivan Melendez
 * @paquete     Libs
 */
class ExtDate {

    /**
     * Fecha en formato YYYY-MM-DD
     *
     * @var string
     */
    protected static $_date = null;
    /**
     * Dia de la fecha
     *
     * @var int
     */
    protected static $_day = null;
    /**
     * Mes de la fecha
     *
     * @var int
     */
    protected static $_month = null;
    /**
     * Año de la fecha
     *
     * @var int
     */
    protected static $_year = null;
    /**
     * Fecha en formato timestamp
     *
     * @var int
     */
    protected static $_timestamp = null;
    /**
     * Nombre de los dias en español
     *
     * @var array
     */
    protected static $_daySpanish = array('','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo');
     /**
     * Nombre de los meses en español
     *
     * @var array
     */
    protected static $_monthSpanish = array('','enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre', 'diciembre');
    /**
     * Nombre abreviado de los dias en español
     *
     * @var array
     */
    protected static $_dayAbrevSpanish = array('','Lun','Mar','Mie','Jue','Vie','Sab','Dom');
    /**
     * Nombre de los meses en español
     *
     * @var array
     */
    protected static $_monthAbrevSpanish = array('','Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov', 'Dic');   

    /**
     * Metodo para cargar la fecha actual, mas o menos como el contructor <br> Ejemplo: MyDate::loadDate();
     *
     * @param string $date Fecha en formato YYY-MM-DD
     */
    public static function loadDate() {
        self::$_date    =       date("Y-m-d");
        self::$_day     = (int) date("d");
        self::$_month   = (int) date("m");
        self::$_year    = (int) date("Y");
        self::$_timestamp = self::getTimestamp(date("Y-m-d H:i:s"));
    }

    /**
     * Metodo para cargar una fecha diferente a la actual <br> Ejemplo: MyDate::setDate('2010-11-17');
     *
     * @param string $date Fecha en formato YYYY-MM-DD
     */
    public static function setDate($date='') {
        self::$_date    =       ($date != '') ? $date               : date("Y-m-d");
        self::$_day     = (int) ($date != '') ? substr($date, 8, 2) : date("d");
        self::$_month   = (int) ($date != '') ? substr($date, 5, 2) : date("m");
        self::$_year    = (int) ($date != '') ? substr($date, 0, 4) : date("Y");
        self::$_timestamp = self::getTimestamp($date);
    }

    /**
     * Devuelve la fecha actual. <br> Ejemplo: 2010-11-17
     *
     * @return string
     */
    public static function getDate() {        
        return self::$_date;
    }

    /**
     * Devuelve el dia de la fecha. <br> Ejemplo: 17
     *
     * @param string $date Fecha en formato YYYY-MM-DD
     * @return int
     */
    public static function getDay($date='') {
        if($date) {
            if(strlen($date) > 9) { //2010-01-01
                return (int) substr($date, 8, 2);
            } else {
                return (int) $date;
            }
        } else {            
            return self::$_day;
        }
    }

    /**
     * Devuelve el mes de la fecha. <br> Ejemplo: 11
     *
     * @param string $date Fecha en formato YYYY-MM-DD
     * @return int     
     */
    public static function getMonth($date='') {
        if($date) {
            if(strlen($date) > 9) {  //2010-01-01
                return (int) substr($date, 5, 2);
            } else {
                return (int) $date;
            }
        } else {            
            return self::$_month;
        }
    }

    /**
     * Devuelve el año de la fecha. <br> Ejemplo: 2010
     *
     * @param string $date Fecha en formato YYYY-MM-DD
     * @return int
     */
    public static function getYear($date='') {
        if($date) {
            return (int) substr($date, 0, 4);
        } else {           
            return self::$_year;
        }
    }

    /**
     * Devuelve la fecha en formato timestamp. <br> Ejemplo: 1289970000
     *
     * @param string $date Fecha en formato YYYY-MM-DD
     * @return int
     */
    public static function getTimestamp($date='') {
        if($date) {
            $hou = substr($date, 11, 2);
            $min = substr($date, 14, 2);            
            return (int) mktime($hou, $min, 0, self::getMonth($date), self::getDay($date), self::getYear($date));
        } else {
            return self::$_timestamp;
        }        
    }

    /**
     * Devuelve nombre del día de la fecha. <br> Ejemplo: Miércoles
     * 
     * @param string $date Fecha en formato YYYY-MM-DD
     * @return string
     */
    public static function getDayName($date='') {
        if($date!='') {
            return ucfirst(self::$_daySpanish[strftime("%u", self::getTimestamp($date))]);
        } else {
            return ucfirst(self::$_daySpanish[date('w', self::$_timestamp)]);
        }
    }

    /**
     * Devuelve el nombre del mes de la fecha. <br> Ejemplo: Noviembre
     *
     * @param string $date Fecha en formato YYYY-MM-DD
     * @return string
     */
    public static function getMonthName($date='') {
        if($date!='') {            
            return self::$_monthSpanish[self::getMonth($date)];
        } else {
            return self::$_monthSpanish[date('n', self::$_timestamp)];
        }
    }

    /**
     * Devuelve nombre abreviado del día de la fecha. <br> Ejemplo: Mie
     *
     * @param string $date Fecha en formato YYYY-MM-DD
     * @return string
     */
    public static function getAbrevDayName($date='') {
        if($date!='') {
            return ucfirst(self::$_dayAbrevSpanish[strftime("%u", self::getTimestamp($date))]);
        } else {
            return ucfirst(self::$_dayAbrevSpanish[date('w', self::$_timestamp)]);
        }
    }

    /**
     * Devuelve el nombre abreviado del mes de la fecha. <br> Ejemplo: Nov
     *
     * @param string $date Fecha en formato YYYY-MM-DD
     * @return string
     */
    public static function getAbrevMonthName($date='') {
        if($date!='') {
            return ucfirst(self::$_monthAbrevSpanish[self::getMonth($date)]);
        } else {
            return ucfirst(self::$_monthAbrevSpanish[date('n', self::$_timestamp)]);
        }
    }

    /**
     * Suma dias a una fecha. <br> Ejemplo: MyDate::addDays(5);
     *
     * @param int $days Número de dias a sumar
     * @param string $date Fecha opcional a la cual se le sumaran los dias
     * @return string
     */
    public static function addDays($days, $date='') {
        if($date) {
            $fecha = date("Y-m-d", mktime(0, 0, 0, self::getMonth($date), self::getDay($date)+$days, self::getYear($date)));
        } else {
            $fecha = date("Y-m-d", mktime(0, 0, 0, self::$_month, self::$_day+$days, self::$_year));
        }
        return $fecha;
    }

    /**
     * Resta dias a una fecha. <br> Ejemplo: MyDate::diffDays(5);
     *
     * @param int $days Número de dias a restar
     * @param string $date Fecha opcional a la cual se le restaran los dias
     * @return string
     */
    public static function diffDays($days, $date='') {
        if($date) {
            $fecha = date("Y-m-d", mktime(0, 0, 0, self::getMonth($date), self::getDay($date)-$days, self::getYear($date)));
        } else {
            $fecha = date("Y-m-d", mktime(0, 0, 0, self::$_month, self::$_day-$days, self::$_year));
        }
        return $fecha;
    }

    /**
     * Suma meses a una fecha. <br> Ejemplo: MyDate::addMonths(5);
     *
     * @param int $month Número de meses a sumar
     * @param string $date Fecha opcional a la cual se le sumaran los meses
     * @return string
     */
    public static function addMonths ($month, $date='') {
        if($date) {
            $fecha = date("Y-m-d", mktime(0, 0, 0, (self::getMonth($date)+$month), self::getDay($date), self::getYear($date)));
        } else {
            $fecha = date("Y-m-d", mktime(0, 0, 0, (self::$_month+$month), self::$_day, self::$_year));
        }                
        return $fecha;
    }

    /**
     * Resta meses a una fecha. <br> Ejemplo: MyDate::diffMonths(5);
     *
     * @param int $month Número de meses a restar
     * @param string $date Fecha opcional a la cual se le restaran los meses
     * @return string
     */
    public static function diffMonths ($month, $date='') {
        if($date) {
            $fecha = date("Y-m-d", mktime(0, 0, 0, (self::getMonth($date)-$month), self::getDay($date), self::getYear($date)));
        } else {
            $fecha = date("Y-m-d", mktime(0, 0, 0, (self::$_month-$month), self::$_day, self::$_year));
        }
        return $fecha;
    }

    /**
     * Suma años a una fecha. <br> Ejemplo: MyDate::addYear(5);
     *
     * @param int $years Número de años a sumar
     * @param string $date Fecha opcional a la cual se le sumaran los años
     * @return string
     */
    public static function addYears ($years, $date='') {
        if($date) {
            $fecha = date("Y-m-d", mktime(0, 0, 0, self::getMonth($date), self::getDay($date), self::getYear($date)+$years));
        } else {
            $fecha = date("Y-m-d", mktime(0, 0, 0, self::$_month, self::$_day, self::$_year+$years));
        }
        return $fecha;
    }

    /**
     * Resta años a una fecha. <br> Ejemplo: MyDate::diffYears(5);
     *
     * @param int $years Número de años a restar
     * @param string $date Fecha opcional a la cual se le restaran los años
     * @return string
     */
    public static function diffYears ($years, $date='') {
        if($date) {
            $fecha = date("Y-m-d", mktime(0, 0, 0, self::getMonth($date), self::getDay($date), self::getYear($date)-$years));
        } else {
            $fecha = date("Y-m-d", mktime(0, 0, 0, self::$_month, self::$_day, self::$_year-$years));
        }
        return $fecha;
    }

    /**
     * Resta una fecha de otra. <br> Ejemplo: MyDate::diffDate('2010-11-17'); <br> Ejemplo: MyDate::diffDate('2010-01-01','2010-12-31');
     *
     * @param string $first_date Fecha inicial en la que se se empieza a contar los dias transcurridos
     * @param string $last_date Fecha final opcional que se tomará como corte.  <br> Si no se especifica se restará a partir de la fecha cargada con el setDate()
     *
     * @return int
     *
     */
    public static function diffDate($first_date, $last_date=''){
        $timestamp_1 = self::getTimestamp($first_date);
        $timestamp_2 = ($last_date != '') ? self::getTimestamp($last_date) : self::getTimestamp(date("Y-m-d H:i:s"));

        return (int) (($timestamp_2 - $timestamp_1) / 86400);
    }

    /**
     * Devuelve true si la fecha interna es la de hoy
     *
     * @return boolean
     */
    public static function isToday() {
        if (self::$_date == date("Y-m-d")) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Devuelve la fecha en formato especial. <br> Ejemplo: Miércoles, 17 de Noviembre de 2010
     *
     * @param string $date Fecha opcional
     * @return string
     */
    public static function getDateSpecial($date = '') {        
        $fecha = self::getDayName($date).", ".self::getDay($date). " de ".self::getMonthName($date)." de ".self::getYear($date);
        return $fecha;
    }

    /**
     * Devuelve el tiempo que ha pasado entre dos fechas. <br> Ejemplo: Hace 25 min.
     *
     * @param string $fist_date Fecha inicial
     * @param string $last_date Fecha final (opcional)
     * @return string
     */
    public static function getHowLong($first_date, $last_date='') {
        $first = self::getTimestamp($first_date);
        $last = ($last_date) ? self::getTimestamp($last_date) : self::getTimestamp(date("Y-m-d H:i:s"));
        $diff = ($last - $first)/60;
        //$diff = $diff + 50;        
        if($diff < 60) {
            return "Hace $diff min.";
        } else if(($diff/60) < 24) {
            $diff = round(($diff/60),0);
            if($diff > 1) {
                return "Hace ".$diff." hrs.";
            } else {
                return "Hace 1 hr.";
            }
        } else if(($diff/60/24) < 28) {
            $diff = round(($diff/60/24),0);
            if($diff > 1) {
                return "Hace ".$diff." días.";
            } else {
                return "Hace 1 día.";
            }
        } else {
            return date("Y-m-d", strtotime($first_date));
        }
       
    }

    public static function getLastDayOfMonth($date='') {
        return strftime("%d", mktime(0, 0, 0, self::getMonth($date)+1, 0, self::getYear($date)));
    }
//    40 43 77 02 939 Bancolombia

}
?>



