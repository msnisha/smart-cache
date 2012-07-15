SmartCache
==========

Smart cache library for Codeigniter framework.

This Codeigniter library supports partial caching and it is very simple library to learn and use.

<h2>Usage</h2>

```php
// For caching view output call this after loading the view
$this->load->library('smartcache');
$this->smartcache->save_output('filename_here');

// For getting the cached out
$this->load->library('smartcache');
if($this->smartcache->get_to_output('filename_here'))
{
    // Cached data is now in Codeigniter output buffer
    // If nothing else to do simply return here
}
else
{
    // no cache data available
    // so you can load the data from view here
}
```