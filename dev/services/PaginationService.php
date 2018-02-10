<?php

namespace dev\services;

use Craft;

/**
 * Class PaginationGenerator
 */
class PaginationService
{
    /** @var array $defaults */
    public static $defaults = [
        'pad' => 3,
        'currentPage' => 1,
        'perPage' => 10,
        'totalResults' => 0,
        'base' => '/blog',
    ];

    /**
     * Get URI path without pagination segments
     * @param bool $omitLeadingSlash
     * @return string
     */
    public static function getUriPathSansPagination(
        bool $omitLeadingSlash = false
    ) : string {
        $lead = $omitLeadingSlash ? '' : '/';

        $segments = Craft::$app->getRequest()->getSegments();

        $segmentCount = \count($segments);

        if ($segmentCount <= 2) {
            return $lead . implode('/', $segments);
        }

        $lastSegIndex = $segmentCount - 1;
        $secondLastIndex = $lastSegIndex - 1;

        if (isset($segments[$secondLastIndex]) &&
            $segments[$secondLastIndex] === 'page' &&
            isset($segments[$lastSegIndex]) &&
            is_numeric($segments[$lastSegIndex])
        ) {
            unset($segments[$lastSegIndex], $segments[$secondLastIndex]);
        }

        return $lead . implode('/', $segments);
    }

    /**
     * Get pagination
     * @param array $options
     * @return array
     */
    public static function getPagination(array $options = array()) : array
    {
        // Start our default options
        $opt = self::$defaults;

        // Iterate through incoming options and cast them properly
        foreach ($options as $key => $val) {
            // Make sure the option is set
            if (! isset(self::$defaults[$key])) {
                continue;
            }

            // Get the type of the default
            $type = \gettype(self::$defaults[$key]);

            // Check if it's an integer
            if ($type === 'integer') {
                $opt[$key] = (int) $val;
                continue;
            }

            // Set string value
            $opt[$key] = (string) $val;
        }

        // Get our default options and merge with the incoming array
        $opt = (object) array_merge(self::$defaults, $options);

        // Calculate the total pages
        $totalPages = ceil($opt->totalResults / $opt->perPage);

        // If we don't have any pages to paginate, we can be done here
        if ($totalPages < 2) {
            return [];
        }

        // Calculate initial lower and upper range of our pagination variables
        $lowerRange = $opt->currentPage - $opt->pad;
        $upperRange = $opt->currentPage + $opt->pad;

        // Figure out if we're starting from one or ending at total
        if ($opt->currentPage < ($opt->pad + 1)) {
            $lowerRange = 1;
            $upperRange = ($opt->pad * 2) + 1;
        } elseif ($opt->currentPage + $opt->pad >= $totalPages) {
            $lowerRange = $totalPages - ($opt->pad * 2);
            $upperRange = $totalPages;
        }

        // Sanity check lower range
        if ($lowerRange < 1) {
            $lowerRange = 1;
        }

        // Sanity check upper range
        if ($upperRange > $totalPages) {
            $upperRange = $totalPages;
        }

        // Check if we need a query string in our pagination
        $queryString = '';
        $queryArray = Craft::$app->getRequest()->getQueryParams();
        if ($queryArray) {
            $queryString = '?' . http_build_query($queryArray);
        }

        // Let's get a pages array ready with our current page
        $pages = array(
            'currentPage' => $opt->currentPage
        );

        // Check if we need a prevPage variable
        $pages['prevPage'] = null;
        if ($opt->currentPage > 1) {
            $prevPage = $opt->currentPage - 1;
            $pages['prevPage'] = $prevPage > 1 ?
                "{$opt->base}/page/{$prevPage}{$queryString}" :
                "{$opt->base}{$queryString}";
        }

        // Check if we need a nextPage variable
        $pages['nextPage'] = null;
        if ($opt->currentPage < $totalPages) {
            $nextPage = $opt->currentPage + 1;
            $pages['nextPage'] = "{$opt->base}/page/{$nextPage}{$queryString}";
        }

        // Check if we need a firstPage variable
        $pages['firstPage'] = null;
        if ($opt->currentPage > $opt->pad + 1) {
            $pages['firstPage'] = "{$opt->base}{$queryString}";
        }

        // Check if we need a lastPage variable
        $pages['lastPage'] = null;
        if ($opt->currentPage + $opt->pad < $totalPages) {
            $pages['lastPage'] = "{$opt->base}/page/{$totalPages}{$queryString}";
        }

        // Iterate through the page range
        foreach (range($lowerRange, $upperRange) as $pageNum) {
            // Cast page number to integer
            $pageNum = (int) $pageNum;

            // Crate the variables array for this page
            $array = array(
                'label' => $pageNum,
                'target' => "{$opt->base}/page/{$pageNum}{$queryString}",
                'isActive' => false
            );

            // If the page number is 1, we don't want the page/num segments
            if ($pageNum === 1) {
                $array['target'] = "{$opt->base}{$queryString}";
            }

            // Check if the page is active
            if ($pageNum === $opt->currentPage) {
                $array['isActive'] = true;
            }

            // Assign to the pages array
            $pages['pages'][] = $array;
        }

        // Return the pages array
        return $pages;
    }
}
