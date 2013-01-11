<?php
class DoubleBarLayout implements PageLayout {
   
    public function fetchPagedLinks($parent, $queryVars) {

        $currentPage = $parent->getPageNumber();        
        $totalPage  = $parent->fetchNumberPages();                
        $str = "";

        //write statement that handles the previous and next phases
        //if it is not the first page then write previous to the screen
        if(!$parent->isFirstPage()) {
            $previousPage = $currentPage - 1;
            $str = "<span class=\"paginacion-anterior\">";
            $str.= Html::link($parent->getUrl()."/pag/$previousPage$queryVars/", "« Anterior");
            $str.="</span> ";
        }

        for($i = $currentPage - 3; $i <= $currentPage + 3; $i++) {
            //if i is less than one then continue to next iteration
            if($i < 1) {
                continue;
            }
            if($i > $parent->fetchNumberPages()) {
                break;
            }
            if($i == $currentPage) {
                $str .= "<span class=\"paginacion-numeracion-actual\" title=\"Página $i de $totalPage\">$i</span>";
            }
            else {
                $str .= "<span class=\"paginacion-numeracion\">";
                $str .= Html::link($parent->getUrl()."/pag/$i$queryVars/", $i, array('title'=>"Página $i de $totalPage"));
                $str .= "</span>";
            }
            ($i == $currentPage + 3 || $i == $parent->fetchNumberPages()) ? $str .= " " : $str .= " | ";  // Se determina si se imprime la paginacion
        }

        if(!$parent->isLastPage()) {
            $nextPage = $currentPage + 1;
            $str .= "<span class=\"paginacion-siguiente\">";
            $str .= Html::link($parent->getUrl()."/pag/$nextPage$queryVars/", "Siguiente »");
            $str .= "</span>";
        }
        return $str;
    }   
}
?>
