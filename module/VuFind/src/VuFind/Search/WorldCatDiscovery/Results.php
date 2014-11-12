<?php
/**
 * WorldCatDiscovery Search Results
 *
 * PHP version 5
 *
 * Copyright (C) Villanova University 2011.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @category VuFind2
 * @package  Search_WorldCatDiscovery
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
namespace VuFind\Search\WorldCatDiscovery;

/**
 * WorldCatDiscovery Search Parameters
 *
 * @category VuFind2
 * @package  Search_WorldCatDiscovery
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
class Results extends \VuFind\Search\Base\Results
{
    /**
     * Support method for performAndProcessSearch -- perform a search based on the
     * parameters passed to the object.
     *
     * @return void
     */
    protected function performSearch()
    {
        $query  = $this->getParams()->getQuery();
        $limit  = $this->getParams()->getLimit();
        $offset = $this->getStartRecord() - 1;
        $params = $this->getParams()->getBackendParameters();
        $collection = $this->getSearchService()->search(
            'WorldCatDiscovery', $query, $offset, $limit, $params
        );
		
        $this->responseFacets = $collection->getFacets();
        $this->resultTotal = $collection->getTotal();

        // Construct record drivers for all the items in the response:
        $this->results = $collection->getRecords();
    }

    /**
     * Returns the stored list of facets for the last search
     *
     * @param array $filter Array of field => on-screen description listing
     * all of the desired facet fields; set to null to get all configured values.
     *
     * @return array        Facets data arrays
     */
    public function getFacetList($filter = null)
    {
        $facets = array();
        foreach ($this->responseFacets as $facet){
        	$facetItemList = array();
        	foreach ($facet->getFacetItems() as $facetItem){
        		$facetItemInfo = array();
        		$facetItemInfo['value'] = $facetItem->getName()->getValue();
        		$facetItemInfo['displayText'] = $facetItem->getName()->getValue();
        		$facetItemInfo['count'] = $facetItem->getCount()->getValue();
        		$facetItemInfo['operator'] = 'AND';
        		$facetItemInfo['isApplied'] = false;
        		$facetItemList[] = $facetItemInfo;
        	}
        	$facets[$facet->getFacetIndex()->getValue()] = array(
        		'label' => $facet->getFacetIndex()->getValue(),
        		'list' => $facetItemList
        	);
        }
        return $facets;
    }
}