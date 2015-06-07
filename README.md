# AcuLinkWeather
Intercept data from Acu-Link bridge.

This is a highly WIP bit of code, but it certainly works. Based on [this](http://moderntoil.com/?p=794), but without Perl, libpcap, or any other mess.

- You need to set up a webserver with support for virtual hosts, and add a virtual host with the DNS name www.acu-link.com. At the document root of that site, put this repository.
- Download Predis and put it at the root of the repo.
- Set up Redis.
- Set up a DNS override on your router or gateway to point www.acu-link.com to your webserver. 
- Check the created log files to see if they make sense.
- Look at the added data in Redis, do with it what you want.
