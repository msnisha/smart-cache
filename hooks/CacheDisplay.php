<?php

/* * *
 *  Overwrite the Session variable in the views.
 *  This is needed for using with SmartCache library. 
 *  If you need to display some information from session dynamically while all other content is cached
 *  just place them in the view with special tag in the following way.
 *       <!--session:SessionVariableName-->
 *  So when the content is displayed from cache the above session will be replaced.
 */

Class CacheDisplay {

    function replaceSessionTag() {
        $CI = & get_instance();
        $output = $CI->output->get_output();
        if (preg_match("/<!\-\-session:[a-zA-Z0-9]+\-\->/", $output, $match)) {
            foreach ($match as $session) {
                $sessionVar = str_replace('<!--session:', '', $session);
                $sessionVar = str_replace('-->', '', $sessionVar);
                $sessionValue = $CI->session->userdata($sessionVar);
                $output = str_replace($session, $sessionValue, $output);
            }
        }
        $CI->output->set_output($output);
    }
}

?>
