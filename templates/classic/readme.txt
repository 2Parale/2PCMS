------------------------------------------------------------------------------
------------------------------------------------------------------------------
CLASSIC TEMPLATE
author: Daniel Buca
date: February 2012
------------------------------------------------------------------------------
------------------------------------------------------------------------------

Template description:

CLASSIC is a simple template, used to demonstrate the functionality of the 2PCMS script.



------------------------------------------------------------------------------
1. About the template engine
2. Folders
3. File descriptions



1. About the template engine
The template engine used in 2PCMS has the sole role of separating the code from the html.
The scripting language used in the template files is PHP.

2. Folders
Templates are stored in the /templates folder.
For each template there is a folder. The folder name is the the template name.

There is no required sub-folder.

3. File descriptions
readme.txt - this file
footer.php - contains the html code for the footer section
header.php - contains the html code for the header section, the left sidebar, untill the breadcrumbs and page h1 (including)
index.php - contains the html code for the index page
listing.php - contains the html code for the listing page
listing-pagination.php - contains the pagination display; it is used before and after listing.php and search.php template
product.php - contains the html code for the product page, both the product description and the related products section
search.php - contains the html code for the listing elements used in the search results page
style.css - css file, contains all theme css, there is no default script css

Info about the social icons:
considering this is a script functionality and not a template functionality the social icons can be found in the /images folder and not in the template folder