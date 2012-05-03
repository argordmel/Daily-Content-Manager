<?php
/**
 * The intention of the Paginated class is to manage the iteration of records
 * based on a specified page number usually addressed by a get parameter in the query string
 * and to use a layout interface to produce number pages based on the amount of elements
 */

require_once "PageLayout.php";
require_once "DoubleBarLayout.php";

class Paginated {

    private $rs;            //result set
    private $pageSize;      //number of records to display
    private $pageNumber;    //the page to be displayed
    private $rowNumber;     //the current row of data which must be less than the pageSize in keeping with the specified size
    private $offSet;
    private $layout;
    private $url;           //Url para mostrar las páginas

    public function __construct($obj, $displayRows = 10, $pageNum = 1) {
        $this->setRs($obj);
        $this->setPageSize($displayRows);
        $this->assignPageNumber($pageNum);
        $this->setRowNumber(0);
        $this->setOffSet(($this->getPageNumber() - 1) * ($this->getPageSize()));
        $this->setUrl(Router::get('route'));
    }

    public function setUrl($route){
        $url = explode('pag', $route);
        $rs = $url[0];
        $this->url = trim($rs,'/');
    }

    public function getUrl() {
        return $this->url;
    }

    //implement getters and setters
    public function setOffSet($offSet) {
        $this->offSet = $offSet;
    }

    public function getOffSet() {
        return $this->offSet;
    }

    public function getRs() {
        return $this->rs;
    }

    public function setRs($obj) {
        $this->rs = $obj;
    }

    public function getPageSize() {
        return $this->pageSize;
    }

    public function setPageSize($pages) {
        $this->pageSize = $pages;
    }

    //accessor and mutator for page numbers
    public function getPageNumber() {
        return $this->pageNumber;
    }

    public function setPageNumber($number) {
        $this->pageNumber = $number;
    }

    //fetches the row number
    public function getRowNumber() {
        return $this->rowNumber;
    }

    public function setRowNumber($number) {
        $this->rowNumber = $number;
    }

    public function fetchNumberPages() {
        if (!$this->getRs()) {
            return false;
        }
        $pages = ceil(count($this->getRs()) / (float)$this->getPageSize());
        return $pages;
    }

    //sets the current page being viewed to the value of the parameter
    public function assignPageNumber($page) {
        if(($page <= 0) || ($page > $this->fetchNumberPages()) || ($page == "")) {
            $this->setPageNumber(1);
        }
        else {
            $this->setPageNumber($page);
        }
    }

    public function fetchPagedRow() {
        if((!$this->getRs()) || ($this->getRowNumber() >= $this->getPageSize())) {
            return false;
        }

        $this->setRowNumber($this->getRowNumber() + 1);
        $index = $this->getOffSet();
        $this->setOffSet($this->getOffSet() + 1);

        /* Para evitar un offset */
        if(isset($this->rs[$index]) ) {
            return $this->rs[$index];
        }
        return false;
    }

    public function isFirstPage() {
        return ($this->getPageNumber() <= 1);
    }

    public function isLastPage() {
        return ($this->getPageNumber() >= $this->fetchNumberPages());
    }

    public function getLayout() {
        return $this->layout;
    }

    public function setLayout(PageLayout $layout) {
        $this->layout = $layout;
    }

    public function fetchPagedNavigation($queryVars = "") {
        return $this->getLayout()->fetchPagedLinks($this, $queryVars);
    }
}
?>