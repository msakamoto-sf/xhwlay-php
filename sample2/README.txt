Xhwlay's sample web applications (more secured, practical).

- controller.php : xhwlay application's common code example.
- login.php, main.php : improved version using controller.php

NOTICE: Before you execute these examples, you must make 2 directories writable
by web server process.

sample/
       datas/ ... readable, and writable
       sess/ ... readable, and writable

This example passes Bookmark Container ID(BCID) by GET/POST parameter, 
and checks request client's ip address and session id is same with given 
bookmark container id's data.
By this checks, application will be more secure and prevents illegal access.

Thank you.
