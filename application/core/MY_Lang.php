<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
/**
* Language Identifier
* 
* Adds a language identifier prefix to all site_url links
* 
* @copyright     Copyright (c) 2011 Wiredesignz
* @version         0.25
* 
* Permission is hereby granted, free of charge, to any person obtaining a copy
* of this software and associated documentation files (the "Software"), to deal
* in the Software without restriction, including without limitation the rights
* to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
* copies of the Software, and to permit persons to whom the Software is
* furnished to do so, subject to the following conditions:
* 
* The above copyright notice and this permission notice shall be included in
* all copies or substantial portions of the Software.
* 
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
* IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
* FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
* AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
* LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
* OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
* THE SOFTWARE.
*/
class MY_Lang extends CI_Lang
{
    function __construct() {
        
        global $URI, $CFG;
        
        $index_page    = $CFG->item('index_page');
        $default_abbr  = $CFG->item('language_abbr');
        $lang_uri_abbr = $CFG->item('lang_uri_abbr');
        
        /* ignore the default abbreviation */
        $lang_ignore = $CFG->item('lang_ignore') AND $lang_ignore = $default_abbr;
        
        /* get the lang_abbreviation from uri and check validity */
        if ($lang_abbr = $URI->segment(1) AND isset($lang_uri_abbr[$lang_abbr])) {

           /* reset uri segments and uri string */
           $URI->_reindex_segments(array_shift($URI->segments));
           $URI->uri_string = preg_replace("|^\/?$lang_abbr\/?|", '', $URI->uri_string);
            
           /* set config language values to match the user language */
           $CFG->set_item('language', $lang_uri_abbr[$lang_abbr]);
           $CFG->set_item('language_abbr', $lang_abbr);
            
           /* check for default abbreviation to be ignored */
           if ($lang_abbr != $lang_ignore) {
           
                   /* check and set the user uri identifier */
                   $index_page .= empty($index_page) ? $lang_abbr : "/$lang_abbr";
                
                /* reset the index_page value */
                $CFG->set_item('index_page', $index_page);
           }            
        
        /* if uri segment has content */
        } elseif ($lang_abbr) {
            
            /* check if default abbreviation is not ignored */   
            if ($default_abbr != $lang_ignore) {
                
                   /* check and set the uri identifier to the default value */    
                $index_page .= empty($index_page) ? $default_abbr : "/$default_abbr";
                
                /* clean up and redirect using the default value */
                header('Location: '.$CFG->item('base_url').$index_page.$URI->uri_string);
            }
            
            /* if the uri abbreviation length matches */
            if (strlen($lang_abbr) == 2) {
                
                /* then the uri abbreviation must be invalid */
                header('Location: '.$CFG->item('lang_redirect_url'));
            }
        }
        
        log_message('debug', "MX_Language_Identifier Class Initialized");
    }
}

/* translate helper */
function t($line) {
    global $CI;
    return ($t = $CI->lang->line($line)) ? $t : $line;
}  