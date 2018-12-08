SmartCache
==========

Smart cache library for Codeigniter framework.

This Codeigniter library supports partial caching and it is very simple library to learn and use. I have created
this library originally for using in the codeigniter websites generated using http://thephpcode.com 
website generation wizard. After seeing lot of questions and discussions I thought it is worth to share this
library to the community.

<h2>Usage</h2>
Use the below code to cache the output
```PHP
$this->load->library('smartcache');
$this->smartcache->save_output('filename_here');
```

Use the below code to get the cached output after loading the view using $this->load->view('viewFileName');
```PHP
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

For full documentation please refer www.thephpcode.com/help/library_smartcache.html

