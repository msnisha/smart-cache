<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * SmartCache Class
 *
 * A Smart Caching library for Codeigniter framework. This is written by Sivapatham Nishanthan 
 *
 * @category	Library
 * @author	Sivapatham Nishanthan
 * @link	http://thephpcode.com/index.php/help/library_smartcache.html
 * @license     MIT
 * @version	1.0
 */
class smartcache {

    function __construct() {
        
    }
 
    /**
     *
     * @param type $fileName : Filename with full path relative to the cache directory in the config file
     * @param type $data : Data to cache
     * @param type $expire : Time in seconds
     * @return type TRUE: cached successfully. FALSE: failed to cache
     */
    public function save_data($fileName, $data, $expire="") {
        $CI = & get_instance();
        $path = $CI->config->load('smartcache');
        $cache_path = $CI->config->item('cache_dir');
        if (preg_match('/^([0-9a-zA-Z]+\\/)+/', $fileName, $matches) > 0) {
            $cache_path .= $matches[0];
            if (!is_dir($cache_path)) {
                mkdir($cache_path, 0700, TRUE);
            }
        }
        $cache_file = $cache_path . md5($fileName);

        if (!$fp = @fopen($cache_file, FOPEN_WRITE_CREATE_DESTRUCTIVE)) {
            log_message('error', "Unable to write cache file: " . $cache_path);
            return FALSE;
        }
        if ($expire != "")
            $expire = time() + ($expire * 60);
        if (flock($fp, LOCK_EX)) {
            fwrite($fp, $expire . "\n" . $data);
            flock($fp, LOCK_UN);
        } else {
            log_message('error', "Unable to secure a file lock for file at: " . $cache_path);
            return FALSE;
        }
        fclose($fp);
        return TRUE;
        //@chmod($cache_path, FILE_WRITE_MODE);
    }

    /**
     * cache the current output with the provided filename
     * @param type $filename The filename with the path to save the cache
     * @param type $expire Expiry time in seconds, if not passed cached for unlimited time
     */
    function save_output($filename, $expire='') {
        $CI = & get_instance();
        $data = $CI->output->get_output();
        $this->save_data($filename, $data, $expire);
    }

    // --------------------------------------------------------------------

    /**
     * Read from the cache
     *
     * @access	public
     * @return	void
     */
    function get_data($fileName) {
        $CI = & get_instance();

        $path = $CI->config->load('smartcache');
        $cache_path = $CI->config->item('cache_dir');

        if (preg_match('/^([0-9a-zA-Z]+\/)+/', $fileName, $matches) > 0) {
            $cache_path .= $matches[0];
        }

        $filepath = $cache_path . md5($fileName);

        if (!@file_exists($filepath)) {
            return FALSE;
        }

        if (!$fp = @fopen($filepath, FOPEN_READ)) {
            return FALSE;
        }

        flock($fp, LOCK_SH);

        $cache = '';
        if (filesize($filepath) > 0) {
            $cache = fread($fp, filesize($filepath));
        }

        flock($fp, LOCK_UN);
        fclose($fp);

        // Strip out the embedded timestamp

        if (!preg_match("/(\d*\n)/", $cache, $match)) {
            return FALSE;
        }
        // Has the file expired? If so we'll delete it.
        if (trim(str_replace('\n', '', $match['1'])) != "" && (time() >= trim(str_replace('\n', '', $match['1'])))) {
            if (is_really_writable($cache_path)) {
                @unlink($filepath);
                return FALSE;
            }
        }
        $cache = str_replace($match['0'], '', $cache);
        return $cache;
    }

    /**
     * Get the cached data to the output buffer, if no cache available returns false
     * @param type $filename 
     */
    function get_to_output($filename) {
        $CI = & get_instance();
        $data = $this->get_data($filename);
        if ($data === FALSE) {
            return FALSE;
        } else {
            $CI->output->set_output($cache);
            $this->save_data($filename, $data, $expire);
            return TRUE;
        }
    }

    /***
     * Deletes the cached data file with the given file name
     */
    function delete_data($fileName) {
        $CI = & get_instance();
        $path = $CI->config->load('smartcache');
        $cache_path = $CI->config->item('cache_dir');
        if (preg_match('/^([0-9a-zA-Z]+\/)+/', $fileName, $matches) > 0) {
            $cache_path .= $matches[0];
        }
        $filepath = $cache_path . md5($fileName);


        if (!@file_exists($filepath)) {
            return FALSE;
        }
        if (is_really_writable($cache_path)) {
            if (!is_dir($filepath))
                @unlink($filepath);
            return TRUE;
        }
        else
            return FALSE;
    }

    /***
     * Delete all the cache file in the given folder
     * 
     * $cache_path : The directory name to delete the cache files
     * $initiate : Send FALSE if shouldn't prepend with the config cache directory
     */
    function delete_all_data($cache_path, $initiate=TRUE) {
        if ($initiate) {
            $CI = & get_instance();
            $path = $CI->config->load('smartcache');
            $cache_path = $CI->config->item('cache_dir') . '/' . $cache_path;
        }
        if (is_really_writable($cache_path)) {
            $files = scandir($cache_path);
            $ignore = array('.', '..', 'index.html', '.htaccess');
            foreach ($files as $filepath) {
                if (in_array($filepath, $ignore))
                    continue;
                if (!is_dir($cache_path . '/' . $filepath)) {
                    @unlink($cache_path . '/' . $filepath);
                } else {
                    $this->delete_all_data($cache_path . '/' . $filepath, FALSE);
                }
            }
            return TRUE;
        }
        else
            return FALSE;
    }

}

// END Cache Class

/*
* 	Usage
* 	-----
*
*	$data = $this->cache->get_data('filename');
*	if($data)
*	{
*	    $this->output->set_output($data);
*	}
*	else
*	{
*	    //do your processing 
*	    $data = $this->output->get_output();
*	    $this->cache->save_data('filename',10);
*	} 
*
*/
/* End of file SimpleCache.php */
/* Location: ./application/libary/SimpleCache.php */
