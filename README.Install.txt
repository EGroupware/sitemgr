Directions for getting sitemgr working on your system.

1) Go to the phpGroupWare setup program (http://yourmachine/phpgw-path/setup/) and install sitemgr and sitemgr-site.  Double check to make sure they are properly installed before continuing.

2) Log in to phpGroupWare as an admin and create an anonymous phpgw user and assign it a password.  The only app (I assume) that they should have access to is sitemgr-site.  sitemgr-site is a dummy application that redirects phpGW users to the generated site.

3) Users who you wish to see sitemgr (aka contributors) or who you want to be able to link to the sitemgr site from phpGW should be given rights to the application.  The easiest way to do this is to go to User groups and give groups permissions to use the applications.

4) Move the sitemgr-site directory somewhere.  This is the directory that serves the dynamic web site.  The directory can be located anywhere.  For example, you could put it in /var/www/html.  You could make the root location of your web server point to it, if you wish (ie, http://yourmachine/ refers to /var/www/html/sitemgr-site).  Make a mental note of the directory where you put it and the url that it is accessed by.

5) Now go to the sitemgr-site directory and edit the config.inc.php file.  You'll need to know the directory that phpGroupWare resides in as well as the above mentioned things.  Edit the values in the top section, as directed.  Make sure you replace the password for the anonymous user with the password that you chose when creating the account.

6) You're almost set to go.  This step includes some additional configuration, some of which duplicates your efforts from step 5.  Log in to phpGroupWare as an administrator.  Make sure you gave yourself the sitemgr and sitemgr-link applications so that you see them on your navbar.  Go to the sitemgr application and select "Setup sitemgr-link".  Fill in the directory and URL information as directed and any other information requested.  Note: at this time you have not created any pages so of course it does not make sense to fill in a default page.

	That's it.  Go to the Category manager, add a category or three and check who can view and edit them, then go to the page manager, add a page or three to each category, set up your site header, site footer, etc., and go view your recently created site by clicking on the sitemgr-link application.  Voila!  
