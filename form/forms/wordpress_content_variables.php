numtables || 2
addenable || Yes
editenable || Yes
directenable || Yes
addcookie || 
addcookie_value || 
editcookie || 
editcookie_value || 
tables wp_posts wp_term_relationships
wp_posts_primarykey || ID
wp_term_relationships_primarykey || object_id
wp_posts_fields post_author post_date post_date_gmt post_content post_title post_excerpt post_status comment_status ping_status post_name guid post_type
wp_term_relationships_fields term_taxonomy_id
wp_posts_post_author || element=>hidden || desc1=> || desc2=> || errorcheck=> || english=> || event=> || size=> || cols=> || maxlen=> || default=>1 || relID=> || selected=> || populatestr=> || populatevariables=> || align=> || filedir=>
wp_posts_post_date || element=>textbox || desc1=>Date || desc2=> || errorcheck=>required=>1;date=> || english=> || event=> || size=>20 || cols=> || maxlen=> || default=>=date('Y-m-d H:i:s', strtotime("now")); || relID=> || selected=> || populatestr=> || populatevariables=> || align=> || filedir=>
wp_posts_post_content || element=>textarea_FCKedit || desc1=>Post Content || desc2=> || errorcheck=>required=>1;maxchars=>2000 || english=>Post Content || event=> || size=>350 || cols=>600 || maxlen=>2000 || default=> || relID=> || selected=> || populatestr=> || populatevariables=> || align=> || filedir=>
wp_posts_post_title || element=>textbox || desc1=>Title || desc2=> || errorcheck=>required=>1 || english=>Title || event=> || size=>75 || cols=> || maxlen=> || default=> || relID=> || selected=> || populatestr=> || populatevariables=> || align=> || filedir=>
wp_posts_post_status || element=>selectbox || desc1=>Post Status || desc2=> || errorcheck=> || english=>Post Status || event=> || size=>1 || cols=> || maxlen=> || default=> || relID=> || selected=>draft || populatestr=>draft=>draft,publish=>publish,private=>private || populatevariables=> || align=> || filedir=>
wp_posts_comment_status || element=>radio || desc1=>Allow Comments? || desc2=> || errorcheck=> || english=>Allow Comments? || event=> || size=> || cols=> || maxlen=> || default=> || relID=> || selected=>open || populatestr=>open=>open,closed=>closed,registered_only=>registered_only || populatevariables=> || align=>horizontal || filedir=>
wp_posts_ping_status || element=>radio || desc1=>Allow Pings? || desc2=> || errorcheck=> || english=>Allow Pings? || event=> || size=> || cols=> || maxlen=> || default=> || relID=> || selected=>closed || populatestr=>open=>open,closed=>closed,registered_only=>registered_only || populatevariables=> || align=>horizontal || filedir=>
wp_posts_post_name || element=>hidden || desc1=> || desc2=> || errorcheck=> || english=> || event=> || size=> || cols=> || maxlen=> || default=>=strtolower(str_replace(" ","-",$wp_posts_post_title)); || relID=> || selected=> || populatestr=> || populatevariables=> || align=> || filedir=>
wp_posts_guid || element=>hidden || desc1=> || desc2=> || errorcheck=> || english=> || event=> || size=> || cols=> || maxlen=> || default=>http://www.phpaddedit.com/addedit/wordpress.php?ID=$next_increment || relID=> || selected=> || populatestr=> || populatevariables=> || align=> || filedir=>
wp_posts_index_fields ID post_date post_content post_title post_status
wp_posts_post_date_gmt || element=>hidden || desc1=> || desc2=> || errorcheck=> || english=> || event=> || size=> || cols=> || maxlen=> || default=>=date('Y-m-d H:i:s', strtotime("now")-18000); || relID=> || selected=> || populatestr=> || populatevariables=> || align=> || filedir=>
wp_posts_post_excerpt || element=>textarea || desc1=>Excerpt || desc2=> || errorcheck=> || english=>Excerpt || event=> || size=>4 || cols=>70 || maxlen=>500 || default=> || relID=> || selected=> || populatestr=> || populatevariables=> || align=> || filedir=>
wp_posts_post_type || element=>hidden || desc1=> || desc2=> || errorcheck=> || english=> || event=> || size=> || cols=> || maxlen=> || default=>post || relID=> || selected=> || populatestr=> || populatevariables=> || align=> || filedir=>
wp_term_relationships_term_taxonomy_id || element=>selectbox_multirow_other || desc1=>Category  (<a href="javascript:openWindow('../forms/add_category.php','540','600')">Add Category</a>) || desc2=>If you need to add a new category that has a parent you should use the link above and then refresh this page but if you want to add a new category that has no parent, just use the text field and add button below || errorcheck=> || english=>Category || event=> || size=>6 || cols=> || maxlen=> || default=> || relID=>object_id || selected=> || populatestr=>select * from wp_terms order by name || populatevariables=>term_id=>name || align=> || filedir=>
desc1_location || fieldset
desc2_location || top
numsections || 3
section1numrows || 4
section1numcols || 1
section1title || Add/Edit a WordPress Post
section2numrows || 2
section2numcols || 2
section2title || 
section3numrows || 5
section3numcols || 1
section3title || 
section1row1 || wp_posts_post_title
section1row2 || wp_posts_post_content
section1row3 || wp_term_relationships_term_taxonomy_id
section1row4 || wp_posts_post_excerpt
section2row1 || wp_posts_comment_status || wp_posts_ping_status
section2row2 || wp_posts_post_date || wp_posts_post_status  
section3row1 || wp_posts_post_author
section3row2 || wp_posts_post_date_gmt 
section3row3 || wp_posts_post_type
section3row4 || wp_posts_guid
section3row5 || wp_posts_post_name
css || 
form_title || 
form_name || addedit
form_action || $_SERVER['PHP_SELF']
form_method || POST
form_enctype || multipart/form-data
form_success_redirect || 
form_failure_redirect || 
form_submit_text || Submit
email_format || html
email_engine || mail
smtp_host || 
smtp_auth || 
smtp_user || 
smtp_pass || 
email_from || mailer@phpaddedit.com
email_from_name || phpAddEdit Mailer
email_reply || mailer@phpaddedit.com
email_bounce || bounce@phpaddedit.com
send_email1 || Yes
email1_to || demo@phpaddedit.com
email1_cc || 
email1_subject || phpAddEdit Demo Form Submitted
email1_body_default || Yes
send_email2 || No
email2_to || 
email2_cc || 
email2_subject || 
email2_body_default || 
attachment1 || 
attachment1_name || 
attachment2 || 
attachment2_name || 
create_RSS || Yes
rss_file || ../../rss/feed.xml
rss_title || phpAddEdit
rss_description || phpAddEdit Demo Additions RSS Feed
rss_link || http://www.phpaddedit.com
rss_title_field || wp_posts_post_title
rss_description_field || wp_posts_post_content
rss_description_chars || 
rss_item_link || http://www.phpaddedit.com/addedit/wordpress.php?ID=$ID
form_width || 650px
pwhelp || passwords must be a minimum of 6 characters and cannot include any special characters
encoding || UTF-8
displayfile || Y
fckedit_toolbar || 
humanverify || Y
humanverify_question || What is 4+7?
humanverify_answer || 11
onsubmit_action || 
form_insert_text || 
form_edit_text || 
