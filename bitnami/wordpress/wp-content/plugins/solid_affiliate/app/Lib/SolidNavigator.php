<?php

namespace SolidAffiliate\Lib;

/**
 * Solid Navigator
 *
 *  @psalm-type SearchResult = array{
 *      type: self::TYPE_*,
 *      url: string, 
 *      title: string,
 *      description: string,
 *      match_strength: float,
 *      result_index: int
 * }
 * 
 * @psalm-type PageDescription = array{
 *   url: string,
 *   title: string,
 *   description: string,
 *   content: string,
 * }
 */
class SolidNavigator
{
    const TYPE_PAGE = 'Page';

    public static function init(): void
    {
        return;
        // register admin ajax handler
        // add_action('wp_ajax_solid_navigator', [self::class, 'handle_search_ajax']);

        // // TODO enquque alpine.js dependency

        // // If it's an admin page, render the render_solid_navigator_component
        // if (is_admin()) {
        //     add_action('admin_footer', [self::class, 'render_solid_navigator_component']);
        // }

        // // add an action for after the $menu and $submenu globals are set
        // add_action('admin_init', [self::class, 'build_dynamic_pages_db'], 1000);
    }

    public static function handle_search_ajax(): void
    {
        //////////////////////////////////
        // get query from POST
        //////////////////////////////////
        $query = isset($_POST['query']) ? (string)$_POST['query'] : '';
        // clean up the query
        // 1. Removes leading and trailing whitespace. 
        // 2. Replaces multiple spaces with a single space.
        $query = trim($query);
        $query = preg_replace('/\s+/', ' ', $query);

        //////////////////////////////////
        // do the actual searching
        //////////////////////////////////
        $response = self::search_for_pages($query);


        //////////////////////////////////
        // send response
        //////////////////////////////////
        wp_send_json_success([
            'syncedData' => [
                'response' => $response,
                'errors' => ['not implemented'],
                'parsedQuery' => $query,
            ]
        ]);
    }



    /**
     * @return PageDescription[]
     */
    public static function get_pages_db()
    {
        return self::validate_array_of_page_description(get_option('solid_navigator_pages_db', []));
    }


    /**
     * @param mixed $anything
     * @return PageDescription[]
     */
    public static function validate_array_of_page_description($anything)
    {
        try {
            if (!is_array($anything)) {
                throw new \Exception('Expected an array');
            }

            foreach ($anything as $_key => $value) {
                if (!is_array($value)) {
                    throw new \Exception('Expected an array');
                }

                if (!isset($value['url'])) {
                    throw new \Exception('Expected an array with a url key');
                }

                if (!isset($value['title'])) {
                    throw new \Exception('Expected an array with a title key');
                }

                if (!isset($value['description'])) {
                    throw new \Exception('Expected an array with a description key');
                }

                if (!isset($value['content'])) {
                    throw new \Exception('Expected an array with a content key');
                }
            }

            /** @var PageDescription[] */
            return $anything;
        } catch (\Throwable $e) {
            // Optionally, you can log or handle the error here if necessary.
            // For now, we'll just return an empty array.
            return [];
        }
    }



    /**
     * Builds the pages database, and then saves it to the options table.
     * Handles "caching", so this function is safe to call on every admin request.
     * @psalm-suppress
     * @return void
     */
    public static function build_dynamic_pages_db()
    {
        global $menu, $submenu;
        //////////////////////////  
        // Add psalm-types for $menu and $submenu

        /**
         * @var array<int, array{0: string, 1: string, 2: string, 3: string, 4: string, 5: string, 6: string}> $menu
         */
        global $menu;

        /**
         * @var array<string, list<array{0: string, 1: string, 2: string}>> $submenu
         */
        global $submenu;


        $caching_in_seconds = 1 * 60 * 60; // 1 hour

        ///////////////////////////////////////////////////////////////////////
        // Caching
        ///////////////////////////////////////////////////////////////////////
        // check if the option exists, if so, do nothing

        if (empty($menu) || empty($submenu)) {
            return;
        }

        $last_update = (int)get_option('solid_navigator_pages_db_last_update', 0);
        $pages_db = self::validate_array_of_page_description(get_option('solid_navigator_pages_db', []));


        if (!empty($pages_db) && (time() - $last_update < $caching_in_seconds)) {
            return;
        }
        ///////////////////////////////////////////////////////////////////////
        // End Caching
        ///////////////////////////////////////////////////////////////////////

        ///////////////////////////////////////////////////////////////////////
        // Create the pages database from the $menu and $submenu globals
        ///////////////////////////////////////////////////////////////////////
        $pages_db = [];

        /**
         * @psalm-suppress MixedAssignment
         */
        foreach ($submenu as $menu_id => $items) {
            ///////////////////////////////////////////////////////////////////////////////////////
            // use the $menu_id to find the parent $menu item by [2] and then find it's name by [0]
            $menu_name = '';
            foreach ($menu as $menu_item) {
                /**
                 * @psalm-suppress MixedArrayAccess
                 */
                if ($menu_item[2] === $menu_id) {
                    $menu_name = $menu_item[0];
                    break;
                }
            }

            foreach ($items as $item) {
                /** @var string[] $item */
                $item;
                /**
                 * @psalm-suppress MixedArrayAccess
                 */
                $url_slug = $item[2];
                // if it contains '.php', use the url as is
                /**
                 * @psalm-suppress MixedArgument
                 */
                if (strpos($url_slug, '.php') !== false) {
                    $admin_url = admin_url($url_slug);
                } else {
                    // if it doesn't contain '.php', it's a submenu item, so we need to do admin.php?page=<$url_slug>
                    /**
                     * @psalm-suppress MixedOperand
                     */
                    $admin_url = admin_url('admin.php?page=' . $url_slug);
                }

                /**
                 * @psalm-suppress MixedOperand
                 * @psalm-suppress MixedArrayAccess
                 */
                $page_title = $menu_name . ' > ' . $item[0];
                $page_title = preg_replace('@<(\w+)\b.*?>.*?</\1>@si', '', $page_title);
                // // remove any html tags from the title
                // $page_title = strip_tags($page_title);

                $pages_db[] = [
                    'title' => $page_title,
                    'description' => $item[0],

                    'content' => "This is the page for " . $item[0],
                    'url' => $admin_url,
                ];
            }
        }
        ///////////////////////////////////////////////////////////////////////
        // var_dump('MENU');
        // var_dump($menu);
        // var_dump('SUBMENUT MENU');
        // var_dump($submenu);
        // var_dump('PAGES DB');
        // var_dump($pages_db);
        // die();
        ///////////////////////////////////////////////////////////////////////

        // save this to the option so we don't have to do this every time
        update_option('solid_navigator_pages_db', $pages_db);
        update_option('solid_navigator_pages_db_last_update', time());
    }


    /**
     * @psalm-suppress all
     * 
     * @param string $query
     * @return array
     */
    public static function search_for_pages($query)
    {
        $pages_db = self::get_pages_db();

        // Convert the query to an array of words
        $query_words = preg_split('/\s+/', strtolower(trim($query)));

        // Filter entries based on presence of any word from the query
        // $matches = array_filter($pages_db, function ($entry) use ($query_words) {
        //     foreach ($query_words as $word) {
        //         if (stripos($entry['title'], $word) !== false || stripos($entry['description'], $word) !== false || stripos($entry['content'], $word) !== false) {
        //             return true;
        //         }
        //     }
        //     return false;
        // });

        // Filter entries based on presence of all words from the query in the title
        $matches = array_filter($pages_db, function ($entry) use ($query_words) {
            foreach ($query_words as $word) {
                if (stripos($entry['title'], $word) === false) {
                    return false;
                }
            }
            return true;
        });

        $results = array_map(
            /**
             * @param array $entry
             * @param int $index
             */
            function ($entry, $index) use ($query_words) {
                $total_match_strength = 0;
                $matched_words_count = 0;

                // calculate match strength for each word in the query
                foreach ($query_words as $word) {
                    $titleMatchStrength = substr_count(strtolower((string)$entry['title']), $word);
                    $descriptionMatchStrength = substr_count(strtolower((string)$entry['description']), $word);

                    $match_strength = $titleMatchStrength / ((strlen((string)$entry['title'])));

                    $total_match_strength += $match_strength;

                    // if word was found in either field, increment the matched words count
                    if ($titleMatchStrength > 0 || $descriptionMatchStrength > 0) {
                        $matched_words_count++;
                    }
                }

                // Calculate weight based on the fraction of words from the query that were found
                $query_words_present_weight = $matched_words_count / count($query_words);

                // Multiply match strength with the weight
                $final_match_strength = $total_match_strength * $query_words_present_weight;

                return [
                    'type' => self::TYPE_PAGE,
                    'url' => $entry['url'],
                    'title' => $entry['title'],
                    'description' => $entry['description'],
                    'match_strength' => $final_match_strength,
                    'result_index' => $index
                ];
            },
            $matches,
            array_keys($matches)
        );

        /**
         * @psalm-suppress ArgumentTypeCoercion
         */
        $results = self::_highlight_results($results, $query);

        // Recalculate the result_index based on the new order of the results
        $results = array_map(function ($result, $index) {
            $result['result_index'] = $index;
            return $result;
        }, $results, array_keys($results));

        return $results;
    }

    /**
     * Highlights the matching part of the string in a 'mark' tag so it can be highlighted in the UI.
     *
     * @param SearchResult[] $results
     * @param string $query
     * @return SearchResult[]
     */
    public static function _highlight_results($results, $query)
    {
        // Convert the query to an array of words
        $query_words = preg_split('/\s+/', strtolower(trim($query)));

        // map the response to the format we want to return. We need to wrap the matching part of the string in a 'mark' tag so it can be highlighted in the UI.
        $results = array_map(function ($entry) use ($query_words) {
            foreach ($query_words as $word) {
                $entry['title'] = preg_replace('/(' . preg_quote($word, '/') . ')/i', '<mark>$1</mark>', $entry['title']);
                $entry['description'] = preg_replace('/(' . preg_quote($word, '/') . ')/i', '<mark>$1</mark>', $entry['description']);
            }
            return $entry;
        }, $results);

        return $results;
    }


    /**
     * The list page for the Data Export UI.
     *
     * @return void
     */
    public static function render_solid_navigator_component()
    {
        ob_start();
?>

        <!-- <script src="//unpkg.com/alpinejs" defer></script> -->

        <script>
            window.onload = function() {
                if (typeof Alpine === 'undefined') {
                    var script = document.createElement('script');
                    script.src = '//unpkg.com/alpinejs';
                    script.defer = true;
                    document.body.appendChild(script);
                }
            }

            document.addEventListener('alpine:init', () => {
                // if solidNavigator is already defined, don't do anything
                console.log('alpine:init from Solid Navigators')
                if (typeof Alpine.store('solidNavigator') !== 'undefined') {
                    return;
                }

                // Set up Alpine.store to handle the state of our setup/onboarding wizard
                Alpine.store('solidNavigator', {
                    isDisplayed: false,
                    requestCounter: 0, // This solves for race conditions when making multiple requests
                    pendingAjax: false,
                    inputQuery: '',
                    highlightIndex: 0,
                    syncedData: {
                        errors: [],
                        response: {},
                    },
                    total_results: 0,
                    postData() {
                        query = this.inputQuery;
                        console.log(query);
                        // if the query is empty, reset the syncedData
                        if (query === '') {
                            this.syncedData = {
                                errors: [],
                                response: {},
                            };
                            this.total_results = 0;
                            return;
                        }

                        this.pendingAjax = true;
                        this.requestCounter += 1; // Increment the counter each time a request is made
                        const currentRequestCounter = this.requestCounter; // Save the current counter value
                        jQuery.post(ajaxurl, {
                                action: 'solid_navigator',
                                query: query,
                                syncedData: this.syncedData,
                            }, (response) => {
                                // Only update the state if the response corresponds to the most recent request
                                if (currentRequestCounter === this.requestCounter) {
                                    this.syncedData = Object.assign(this.syncedData, response.data.syncedData);
                                    this.total_results = Object.values(this.syncedData.response).flat().length;
                                    // if the response is not empty, show the results
                                }
                            })
                            .fail((error) => {
                                console.log(error);
                            })
                            .always(() => {
                                this.highlightIndex = 0;
                                this.pendingAjax = false;
                            });
                    },

                    handleSelectResultByIndex(highlightIndex) {
                        // find the result by result_index
                        let result = Object.values(this.syncedData.response).flat()[highlightIndex];
                        if (result) {
                            window.location.href = result.url;
                        }
                    },
                });
            });

            jQuery(document).ready(function() {
                // add a keyboard shortcut to open the solid navigator

                document.addEventListener('keydown', function(e) {
                    if (e.keyCode == 74 && (e.metaKey || e.ctrlKey) && !e.altKey && !e.shiftKey) {
                        console.log('ctrl + j pressed');
                        Alpine.store('solidNavigator').isDisplayed = true;

                        setTimeout(() => {
                            let searchInput = document.querySelector('.solid-navigator-input');
                            if (searchInput && !searchInput.matches(':focus')) {
                                e.preventDefault();
                                searchInput.focus();
                                searchInput.scrollIntoView({
                                    behavior: 'smooth'
                                });
                            }
                        }, 25);
                    }

                    // if it's the escape key, and the navigator is open, close it
                    if (e.keyCode == 27 && Alpine.store('solidNavigator').isDisplayed) {
                        Alpine.store('solidNavigator').isDisplayed = false;
                    }
                }, false);
            });
        </script>

        <style>
            [x-cloak] {
                display: none !important;
            }

            .solid-navigator-wrapper {
                position: fixed;
                top: 200px;
                left: 40%;
                padding: 40px;
                border-radius: 20px;
                background: rgb(0 0 0 / 35%);
                z-index: 9999;
                background: rgb(0 0 0 / 85%);
                z-index: 9999;
                background: white;
                border: 10px solid rgb(43 43 43 / 9%);
            }

            .solid-navigator-field {
                display: flex;
                flex-direction: row;
                align-items: center;
                gap: 10px;
            }

            .solid-navigator-field p {
                line-height: 16px;
                font-size: 12px;
                font-weight: 400;
                margin-bottom: 2px;
                margin-top: 0
            }

            .solid-navigator-field input {
                padding-left: 40px;
                background: #fff url("data:image/svg+xml,%3Csvg width='20' height='20' viewBox='0 0 20 20' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M17.5 17.5L12.5 12.5M2.5 8.33333C2.5 9.09938 2.65088 9.85792 2.94404 10.5657C3.23719 11.2734 3.66687 11.9164 4.20854 12.4581C4.75022 12.9998 5.39328 13.4295 6.10101 13.7226C6.80875 14.0158 7.56729 14.1667 8.33333 14.1667C9.09938 14.1667 9.85792 14.0158 10.5657 13.7226C11.2734 13.4295 11.9164 12.9998 12.4581 12.4581C12.9998 11.9164 13.4295 11.2734 13.7226 10.5657C14.0158 9.85792 14.1667 9.09938 14.1667 8.33333C14.1667 7.56729 14.0158 6.80875 13.7226 6.10101C13.4295 5.39328 12.9998 4.75022 12.4581 4.20854C11.9164 3.66687 11.2734 3.23719 10.5657 2.94404C9.85792 2.65088 9.09938 2.5 8.33333 2.5C7.56729 2.5 6.80875 2.65088 6.10101 2.94404C5.39328 3.23719 4.75022 3.66687 4.20854 4.20854C3.66687 4.75022 3.23719 5.39328 2.94404 6.10101C2.65088 6.80875 2.5 7.56729 2.5 8.33333Z' stroke='%238797B8' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E%0A") no-repeat 10px center;
            }

            .solid-navigator-field input:focus-visible {
                background: #fff url("data:image/svg+xml,%3Csvg width='20' height='20' viewBox='0 0 20 20' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M17.5 17.5L12.5 12.5M2.5 8.33333C2.5 9.09938 2.65088 9.85792 2.94404 10.5657C3.23719 11.2734 3.66687 11.9164 4.20854 12.4581C4.75022 12.9998 5.39328 13.4295 6.10101 13.7226C6.80875 14.0158 7.56729 14.1667 8.33333 14.1667C9.09938 14.1667 9.85792 14.0158 10.5657 13.7226C11.2734 13.4295 11.9164 12.9998 12.4581 12.4581C12.9998 11.9164 13.4295 11.2734 13.7226 10.5657C14.0158 9.85792 14.1667 9.09938 14.1667 8.33333C14.1667 7.56729 14.0158 6.80875 13.7226 6.10101C13.4295 5.39328 12.9998 4.75022 12.4581 4.20854C11.9164 3.66687 11.2734 3.23719 10.5657 2.94404C9.85792 2.65088 9.09938 2.5 8.33333 2.5C7.56729 2.5 6.80875 2.65088 6.10101 2.94404C5.39328 3.23719 4.75022 3.66687 4.20854 4.20854C3.66687 4.75022 3.23719 5.39328 2.94404 6.10101C2.65088 6.80875 2.5 7.56729 2.5 8.33333Z' stroke='%2347597C' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E%0A") no-repeat 10px center;
            }

            .solid-navigator-field-hint {
                opacity: .5
            }

            solid-navigator-field-hint:hover {
                opacity: 1;
            }

            .solid-navigator-field p strong {
                font-weight: 600;
            }

            .solid-navigator-input {
                padding: 12px 8px;
                font-size: 13px;
                width: 400px;
                border-radius: 5px;
                border: 1px solid #ccc;
            }

            .solid-navigator-modifiers {
                font-size: 11px;
                opacity: .8;
                width: 100%;
                text-align: right;
            }

            .solid-navigator-result {
                display: flex;
                flex-direction: row;
                gap: 4px;
                padding: 4px;
                border-radius: 8px;
                cursor: pointer;
                height: 40px;
                position: relative;
            }

            .solid-navigator-quick-link.highlighted {
                border: 2px solid #FFEBE2;
            }

            .solid-navigator-result.highlighted {
                background: #FFEBE2;
            }

            .solid-navigator-result::after {
                content: url("data:image/svg+xml,%3Csvg width='20' height='20' viewBox='0 0 20 20' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M4.16669 9.99999H15.8334M15.8334 9.99999L12.5 13.3333M15.8334 9.99999L12.5 6.66666' stroke='%23505062' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E%0A");
                position: absolute;
                background: rgba(255, 255, 255, .4);
                height: 20px;
                width: 20px;
                border-radius: 20px;
                padding: 2px;
                right: 10px;
                top: 15px;
                opacity: 0;
            }

            .solid-navigator-result:hover {
                background-color: #f0f0f0;
            }

            .solid-navigator-result:hover::after {
                opacity: 1;
            }


            .solid-navigator-info {
                padding: 20px;
                border: 1px solid #ccc;
                border-radius: 5px;
                margin-bottom: 1rem;
            }

            .solid-navigator-info pre {
                display: inline-block;
            }

            .solid-navigator-info li {
                margin: 0;
                padding: 0;
                height: 20px;
            }

            .solid-navigator-quick-link {
                font-size: 12px;
                padding: 4px 6px;
                background: #CFDDFF;
                display: inline-block;
                margin: 5px;
                font-weight: 400;
                cursor: pointer;
                border-radius: 4px;
            }

            .solid-navigator-quick-link:hover {
                background: #B8C9FF;
            }

            .solid-navigator-box {
                width: 400px;
                position: relative;
            }

            .solid-navigator-input {
                width: 100%;
            }

            .solid-navigator-results-no-results {
                padding: 40px;
                text-align: center;
                font-size: 16px;
                color: #8586ad;
            }

            .solid-navigator-results {
                width: calc(100% - 33px);
                display: block;
                position: absolute;
                z-index: 99999;
                background: #ffff;
                max-height: 500px;
                border: 1px solid var(--sld-border);
                border-radius: 4px;
                overflow-y: scroll;
                margin-top: 4px;
                box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;
            }

            .solid-navigator-result-type-title {
                padding: 10px;
                line-height: 13px;
                font-weight: 600;
                font-size: 12px;
                color: #8586ad;
            }

            .solid-navigator-result-type {
                padding: 4px;
            }

            .solid-navigator-result-text {
                display: flex;
                flex-direction: column;
                justify-content: center;
                width: calc(100% - 50px);
            }

            .solid-navigator-result-icon {
                width: 40px;
                border-radius: 4px;
                display: flex;
                justify-content: center;
                align-items: center;
            }

            .solid-navigator-result-icon-background {
                width: 30px;
                display: flex;
                height: 30px;
                align-items: center;
                justify-content: center;
                border-radius: 4px;
            }

            .solid-navigator-result-icon-background:after {
                line-height: 1;
            }

            .solid-navigator-result-icon-background.Page {
                background: #e79c84;
            }

            .solid-navigator-result-icon-background.Setting {
                background: #3fabc2;
            }

            .solid-navigator-result-icon-background.Affiliate {
                background: #436dff;
            }

            .solid-navigator-result-icon-background.Documentation {
                background: #be2a8c;
            }

            .solid-navigator-result-icon-background.Quicklink {
                background: #ff0707;
            }

            .solid-navigator-result-icon .Page:after {
                content: url("data:image/svg+xml,%3Csvg width='20' height='20' viewBox='0 0 20 20' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M5 12.5V11.6667C5 11.2246 5.17559 10.8007 5.48816 10.4882C5.80072 10.1756 6.22464 10 6.66667 10H13.3333C13.7754 10 14.1993 10.1756 14.5118 10.4882C14.8244 10.8007 15 11.2246 15 11.6667V12.5M10 7.5V10M2.5 14.1667C2.5 13.7246 2.67559 13.3007 2.98816 12.9882C3.30072 12.6756 3.72464 12.5 4.16667 12.5H5.83333C6.27536 12.5 6.69928 12.6756 7.01184 12.9882C7.3244 13.3007 7.5 13.7246 7.5 14.1667V15.8333C7.5 16.2754 7.3244 16.6993 7.01184 17.0118C6.69928 17.3244 6.27536 17.5 5.83333 17.5H4.16667C3.72464 17.5 3.30072 17.3244 2.98816 17.0118C2.67559 16.6993 2.5 16.2754 2.5 15.8333V14.1667ZM12.5 14.1667C12.5 13.7246 12.6756 13.3007 12.9882 12.9882C13.3007 12.6756 13.7246 12.5 14.1667 12.5H15.8333C16.2754 12.5 16.6993 12.6756 17.0118 12.9882C17.3244 13.3007 17.5 13.7246 17.5 14.1667V15.8333C17.5 16.2754 17.3244 16.6993 17.0118 17.0118C16.6993 17.3244 16.2754 17.5 15.8333 17.5H14.1667C13.7246 17.5 13.3007 17.3244 12.9882 17.0118C12.6756 16.6993 12.5 16.2754 12.5 15.8333V14.1667ZM7.5 4.16667C7.5 3.72464 7.67559 3.30072 7.98816 2.98816C8.30072 2.67559 8.72464 2.5 9.16667 2.5H10.8333C11.2754 2.5 11.6993 2.67559 12.0118 2.98816C12.3244 3.30072 12.5 3.72464 12.5 4.16667V5.83333C12.5 6.27536 12.3244 6.69928 12.0118 7.01184C11.6993 7.3244 11.2754 7.5 10.8333 7.5H9.16667C8.72464 7.5 8.30072 7.3244 7.98816 7.01184C7.67559 6.69928 7.5 6.27536 7.5 5.83333V4.16667Z' stroke='white' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E%0A");
            }


            .solid-navigator-result-icon .Setting:after {
                content: url("data:image/svg+xml,%3Csvg width='20' height='20' viewBox='0 0 20 20' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M4.99998 10C4.55795 10 4.13403 9.82441 3.82147 9.51185C3.50891 9.19929 3.33331 8.77537 3.33331 8.33334C3.33331 7.89132 3.50891 7.46739 3.82147 7.15483C4.13403 6.84227 4.55795 6.66668 4.99998 6.66668M4.99998 10C5.44201 10 5.86593 9.82441 6.17849 9.51185C6.49105 9.19929 6.66665 8.77537 6.66665 8.33334C6.66665 7.89132 6.49105 7.46739 6.17849 7.15483C5.86593 6.84227 5.44201 6.66668 4.99998 6.66668M4.99998 10V16.6667M4.99998 6.66668V3.33334M9.99998 15C9.55795 15 9.13403 14.8244 8.82147 14.5119C8.50891 14.1993 8.33331 13.7754 8.33331 13.3333C8.33331 12.8913 8.50891 12.4674 8.82147 12.1548C9.13403 11.8423 9.55795 11.6667 9.99998 11.6667M9.99998 15C10.442 15 10.8659 14.8244 11.1785 14.5119C11.4911 14.1993 11.6666 13.7754 11.6666 13.3333C11.6666 12.8913 11.4911 12.4674 11.1785 12.1548C10.8659 11.8423 10.442 11.6667 9.99998 11.6667M9.99998 15V16.6667M9.99998 11.6667V3.33334M15 7.50001C14.558 7.50001 14.134 7.32442 13.8215 7.01185C13.5089 6.69929 13.3333 6.27537 13.3333 5.83334C13.3333 5.39132 13.5089 4.96739 13.8215 4.65483C14.134 4.34227 14.558 4.16668 15 4.16668M15 7.50001C15.442 7.50001 15.8659 7.32442 16.1785 7.01185C16.4911 6.69929 16.6666 6.27537 16.6666 5.83334C16.6666 5.39132 16.4911 4.96739 16.1785 4.65483C15.8659 4.34227 15.442 4.16668 15 4.16668M15 7.50001V16.6667M15 4.16668V3.33334' stroke='white' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E%0A");
            }


            .solid-navigator-result-icon .Affiliate:after {
                content: url("data:image/svg+xml,%3Csvg width='20' height='20' viewBox='0 0 20 20' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M5 17.5V15.8333C5 14.9493 5.35119 14.1014 5.97631 13.4763C6.60143 12.8512 7.44928 12.5 8.33333 12.5H11.6667C12.5507 12.5 13.3986 12.8512 14.0237 13.4763C14.6488 14.1014 15 14.9493 15 15.8333V17.5M6.66667 5.83333C6.66667 6.71739 7.01786 7.56523 7.64298 8.19036C8.2681 8.81548 9.11594 9.16667 10 9.16667C10.8841 9.16667 11.7319 8.81548 12.357 8.19036C12.9821 7.56523 13.3333 6.71739 13.3333 5.83333C13.3333 4.94928 12.9821 4.10143 12.357 3.47631C11.7319 2.85119 10.8841 2.5 10 2.5C9.11594 2.5 8.2681 2.85119 7.64298 3.47631C7.01786 4.10143 6.66667 4.94928 6.66667 5.83333Z' stroke='white' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E%0A");
            }


            .solid-navigator-result-icon .Documentation:after {
                content: url("data:image/svg+xml,%3Csvg width='20' height='20' viewBox='0 0 20 20' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M10 15.8333C8.85986 15.1751 7.56652 14.8285 6.25 14.8285C4.93347 14.8285 3.64014 15.1751 2.5 15.8333V4.99999C3.64014 4.34173 4.93347 3.99518 6.25 3.99518C7.56652 3.99518 8.85986 4.34173 10 4.99999M10 15.8333C11.1401 15.1751 12.4335 14.8285 13.75 14.8285C15.0665 14.8285 16.3599 15.1751 17.5 15.8333V4.99999C16.3599 4.34173 15.0665 3.99518 13.75 3.99518C12.4335 3.99518 11.1401 4.34173 10 4.99999M10 15.8333V4.99999' stroke='white' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E%0A");
            }


            .solid-navigator-result-icon .Quicklink:after {
                content: url("data:image/svg+xml,%3Csvg width='20' height='20' viewBox='0 0 20 20' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M10.8334 2.5V8.33333H15.8334L9.16669 17.5V11.6667H4.16669L10.8334 2.5Z' stroke='white' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E%0A");
            }


            .solid-navigator-result-type:not(:last-child) {
                border-bottom: 1px solid var(--sld-border);
            }

            .solid-navigator-result-heading {
                font-size: 12px;
                font-weight: 500;
                line-height: 16px;
                white-space: nowrap;
                overflow: hidden;
                width: 100%;
                text-overflow: ellipsis;
            }

            .solid-navigator-result-desc {
                font-size: 11px;
                font-weight: 400;
                white-space: nowrap;
                overflow: hidden;
                width: 100%;
                text-overflow: ellipsis;
                opacity: .8;
            }

            .solid-navigator-result:hover .solid-navigator-result-desc,
            .solid-navigator-result-heading {
                width: calc(100% - 40px);
            }

            .quick-links {
                display: flex;
                flex-direction: row;
            }

            .solid-navigator-result-desc br {
                display: none;
            }

            .solid-navigator-input-shortcut {
                position: relative;
            }

            .solid-navigator-input-shortcut::after {
                content: '⌘J';
                position: absolute;
                right: 40px;
                top: 50%;
                transform: translateY(-50%);
                color: #888;
                pointer-events: none;
                font-size: 11px;
            }

            .solid-navigator-close {
                display: block;
                color: #8585ac;
                text-align: right;
                margin-top: -30px;
                margin-bottom: 14px;
                font-size: 10px;
                font-family: monospace;
            }

            #solid-navigator-modal-backdrop {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: black;
                opacity: 0.5;
                z-index: 999;
                background-image: url(https://www.transparenttextures.com/patterns/stardust.png)
            }
        </style>


        <div x-data id="solid-navigator-modal-backdrop" x-cloak x-show="$store.solidNavigator.isDisplayed"></div>
        <div x-data class='solid-navigator-wrapper' x-cloak x-show="$store.solidNavigator.isDisplayed">
            <div class="solid-navigator-close">esc to close</div>
            <div class="solid-navigator-box" x-data="{ showResults: false}" @keydown.escape="showResults = false" @keydown.arrow-up="$store.solidNavigator.highlightIndex = Math.max($store.solidNavigator.highlightIndex - 1, -1)" @keydown.arrow-down="$store.solidNavigator.highlightIndex = Math.min($store.solidNavigator.highlightIndex + 1, $store.solidNavigator.total_results - 1)" @keydown.enter="if ($store.solidNavigator.highlightIndex >= 0) { $store.solidNavigator.handleSelectResultByIndex($store.solidNavigator.highlightIndex) };">
                <!-- Search Input -->
                <div class="solid-navigator-field solid-navigator-input-shortcut">
                    <input class="solid-navigator-input" placeholder="Search anything.." x-model="$store.solidNavigator.inputQuery" x-ref="inputQuery" @input.debounce="$store.solidNavigator.postData()" @focus="showResults = true" @blur="showResults = false" @click="showResults = true">
                </div>
                <!-- end - Search Input -->

                <!-- RESULTS -->
                <div class="solid-navigator-results-no-results" x-show="Object.values($store.solidNavigator.syncedData.response).length == 0">
                    <div x-show="$store.solidNavigator.pendingAjax">searching...</div>

                    <div x-show="!$store.solidNavigator.pendingAjax">
                        <div x-show="!$store.solidNavigator.inputQuery">Which page do you want to go to?</div>
                        <div x-show="$store.solidNavigator.inputQuery">No Results</div>
                    </div>
                </div>
                <div class="solid-navigator-results" x-cloak x-show="$store.solidNavigator.syncedData.response && Object.keys($store.solidNavigator.syncedData.response).length > 0 && showResults">
                    <template x-for="result in $store.solidNavigator.syncedData.response">
                        <div>
                            <!-- All other types -->
                            <div class="solid-navigator-result" :class="{ 'highlighted': $store.solidNavigator.highlightIndex === result.result_index }" @mousedown="window.open(result.url, '_blank')">
                                <div class="solid-navigator-result-icon">
                                    <div class="solid-navigator-result-icon-background Page"></div>
                                </div>
                                <div class="solid-navigator-result-text">
                                    <div class="solid-navigator-result-heading" x-html="result.title"></div>
                                    <!-- <div class="solid-navigator-result-desc" x-html="result.description"></div> -->
                                    <!-- <div class="solid-navigator-result-desc" x-html="result.url"></div> -->
                                    <!-- // TODO remove the url prefix the domain.com/wp-admin/ -->
                                    <div class="solid-navigator-result-desc" x-html="result.url.split('wp-admin/').pop();"></div>

                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>


<?php
        echo ob_get_clean();
    }
}
