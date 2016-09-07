#=====================================================================
# phpAddEdit Form Generator Script
# Ver. 2.0
# By Jeff M. Blum
# http://www.phpaddedit.com/
# Original Version Released: 01-01-2007
# This Version Released: 02-18-2010
# See ChangeLog.txt file for detailed listing of changes
#
# Copyright Info: This application was written by Jeff Blum.
# Feel free to copy, cite, reference, sample, borrow 
# or plagiarize the contents.  However, if you don't mind,
# please let me know where it goes so that I can watch and take
# part in the development of it. Information wants to be free,
# support public domain freeware.  Donations are appreciated
# and will be spent on further upgrades and other public domain
# scripts.
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# 
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#
# Finally, PLEASE SEND WORKING URL's to contact@phpaddedit.com.
# I would like to keep a list of these scripts in use.
#
#=====================================================================

For information on installing or upgrading, visit: http://www.phpaddedit.com/page/install/

-----------------------
--- INSTALLING
-----------------------
Just copy all the files to a directory on your server and make the directory writeable (chmod 777 will work). Then view the /install/ page and enter the basic information requested - that's it!
You will need 4 pieces of information:
  Database host (sometimes 'localhost')
  Database name (e.g., 'mydb')
  Database user (e.g., 'root')
  Database password

-----------------------
--- UPGRADING
-----------------------
To upgrade, I recommend downloading the changed files collection 
from sourceforge rather than the entire collection of files. This 
collection will never include the user-specific (customization) files. 
If you do download and copy over the entire collection of files, 
first save your user-specific files and then replace the defaults
with those. The files to worry about are: 
  config.php 
  addedit-customize.php
  addedit-error-check-custom.php
  addedit-execute-custom.php
  includes/email_template.php (if you are using the HTML email option)

