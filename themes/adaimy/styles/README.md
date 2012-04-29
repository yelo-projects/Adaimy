## Overview

A basic framework to quickly prototype websites, with a mobile-first driven approach  
Requires stylus.

## USAGE  ##

Clone the repo, open "./layers/000_variables.styl" and edit the main variables there.  
For a more granular approach, you might also want to edit "020_base" which contains the basic classes that other classes will extend.  
Then take a quick glance at "040_utils" to see what utility classes are available to you (no need to reinvent the wheel)  

Begin laying out your styles by editing "080_structure" to create your grid  
Refine in the "mediaqueries" directory, which contains different files for different screen sizes as well as one for print

Run ./compile to compile your styles or ./watch to watch in interactive mode.

### Other Interesting Files ###

The directory "ui" contains utilities such as buttons, tabs, tooltips, all driven by pure CSS3.  
You might want to jquery on top of them to polyfill for misbehaving browsers.

You can find examples in the "examples" directory.
