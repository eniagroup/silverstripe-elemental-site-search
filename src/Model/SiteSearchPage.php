<?php

namespace EniaGroup\ElementalSiteSearch\Model;

use Page;
use EniaGroup\ElementalSiteSearch\Controller\SiteSearchPageController;

class SiteSearchPage extends Page
{
    /**
     * {@inheritDoc}
     */
    private static $table_name = 'SiteSearchPage';

     /**
     * {@inheritDoc}
     */ 
	private static $singular_name = 'Site Search Page';

     /**
     * {@inheritDoc}
     */ 
    private static $plural_name = 'Site Search Pages';

    /**
     * {@inheritDoc}
     */
    private static $description = 'Enables users to search the website';
    
    /**
     * Set the controller for this model
     * 
     * @return string
     */
    public function getControllerName()
    {
        return SiteSearchPageController::class;
    }

}
