SIMM MANAGEMENT SYSTEM
======================

Current Version
---------------
2.6.10-pre

Latest Changes
--------------
* Fixed bug on news page where selecting a category would narrow down news but the category listed next to each news item wouldn't be accurate
* Fixed bug in menu class where setting a general menu item to require login wouldn't allow anyone to see the link, logged in or not
* Fixed bug where mission posts wouldn't be deleted when the delete action was triggered from the manage posts page
* Fixed bug where post tags couldn't be updated from the Edit Mission Post page
* Updated the database to set postAuthors as a text field
* Added ability to change the number of authors on a post
* Added loading graphic to the inbox to avoid blank pages while loading large inboxes
* Changed install process so that the variables file doesn't have a closing PHP tag to prevent the output already sent errors
* Updated the post count page to combine user records together by email address for compiling total count report
* Added a hidden field to the contact page to help prevent spam bots from sending spam messages through the contact form
* Cleaned up some stray PHP short open tags in the install script
* Added a hidden field to the join page to help prevent spam bots from sending spam messages through the join form
* Added a hidden field to the docking request page to help prevent spam bots from sending spam messages through the docking request form

Changed Files
-------------
* update.php
* framework/functionsGlobal.php
* framework/classMenu.php
* install/install.php
* install/resource_data.php
* install/resource_structure.php
* admin/manage/posts.php
* admin/reports/count.php
* admin/user/inbox.php
* pages/contact.php
* pages/dockingrequest.php
* pages/join.php
* pages/news.php
* update/269.php