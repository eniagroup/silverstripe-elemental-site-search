<?php

namespace EniaGroup\ElementalSiteSearch\Controller;

use PageController;
use SilverStripe\Core\Convert;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\PaginatedList;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\ORM\Queries\SQLSelect;

class SiteSearchPageController extends PageController 
{
    /**
     * {@inheritDoc}
     */
    private static $allowed_actions = [
        'ajax'
    ];

    /**
     * Get the search term
     * 
     * @return string
     */
    public function SearchTerms() {
        return $this->request->requestVar("search");
    }

    /**
     * AJAX search
     * 
     * @return string
     */
    public function ajax() {
        if ( $this->request->isAjax() ) {
            return $this->renderWith(array('AjaxSearchResults'));
        } else {
            return $this->httpError(404);
        }
    }

    /**
     * Return search results
     * 
     * @return boolean
     */
    public function Results() {
        
        // Cleanup the string
        $keywords = Convert::raw2sql(trim($this->SearchTerms()));

        // Nothing to search
        if(empty($keywords)) {
            return false;
        }
        
        $andProcessor = create_function('$matches','
            return " +" . $matches[2] . " +" . $matches[4] . " ";
        ');
        $notProcessor = create_function('$matches', '
            return " -" . $matches[3];
        ');

        $keywords = preg_replace_callback('/()("[^()"]+")( and )("[^"()]+")()/i', $andProcessor, $keywords);
        $keywords = preg_replace_callback('/(^| )([^() ]+)( and )([^ ()]+)( |$)/i', $andProcessor, $keywords);
        $keywords = preg_replace_callback('/(^| )(not )("[^"()]+")/i', $notProcessor, $keywords);
        $keywords = preg_replace_callback('/(^| )(not )([^() ]+)( |$)/i', $notProcessor, $keywords);
        
        $keywords = $this->addStarsToKeywords($keywords);
        
        $start = isset($_GET['start']) ? (int)$_GET['start'] : 0;
        $pageLength = 10;
        
        // Build query
        $sql = new SQLSelect();
        $sql->setDistinct(true);
        $sql->setFrom('SiteTree');
        $sql->addSelect("(( 2 * (MATCH (`SiteTree`.`Title`) AGAINST ('{$keywords}' IN BOOLEAN MODE))) +( 0.5 * (MATCH (`SiteTree`.`SearchContent`) AGAINST ('{$keywords}' IN BOOLEAN MODE))) +( 1.2 * (MATCH (`SiteTree`.`Keywords`) AGAINST ('{$keywords}' IN BOOLEAN MODE))) +Weight) AS Relevance");
        $sql->setWhere("(MATCH (`SiteTree`.`Title`,`SiteTree`.`SearchContent`,`SiteTree`.`Keywords`) AGAINST ('{$keywords}' IN BOOLEAN MODE))");
        $sql->setOrderBy("Relevance","DESC");
        $sql->setOrderBy(array("Relevance" => "DESC", "Created" => "DESC"));
        $totalCount = $sql->count();
        $sql->setLimit($pageLength, $start);
        $result = $sql->execute();
        $objects = ArrayList::create();
        
        // add based on permission
        foreach($result as $row) {
            $SiteTree = SiteTree::create($row);
            $objects->add($SiteTree);
        }

        $list = new PaginatedList($objects);
        $list->setPageStart($start);
        $list->setPageLength($pageLength);
        $list->setTotalItems($totalCount);
        $list->setLimitItems(false);

        return $list;
    }

    /**
     * Add stars to keywords
     * 
     * @param string $keywords
     * @return string
     */
    protected function addStarsToKeywords($keywords) {
        if(!trim($keywords)) return "";
        // Add * to each keyword
        $splitWords = preg_split("/ +/" , trim($keywords));
        while(list($i,$word) = each($splitWords)) {
            if($word[0] == '"') {
                while(list($i,$subword) = each($splitWords)) {
                    $word .= ' ' . $subword;
                    if(substr($subword,-1) == '"') break;
                }
            } else {
                $word .= '*';
            }
            $newWords[] = $word;
        }
        return implode(" ", $newWords);
    }

}