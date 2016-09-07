numtables || 2
addenable || Yes
editenable || Yes
directenable || Yes
addcookie || 
addcookie_value || 
editcookie || 
editcookie_value || 
tables wp_terms wp_term_taxonomy
wp_terms_primarykey || term_id
wp_term_taxonomy_primarykey || term_taxonomy_id
wp_terms_fields name slug
wp_terms_index_fields term_id name slug
wp_term_taxonomy_fields taxonomy description parent
wp_terms_name || element=>textbox || desc1=>Category Name || desc2=>The name is used to identify the category almost everywhere, for example under the post or in the category widget. || errorcheck=>required=>1;unique=>1 || english=>Category Name || event=> || size=>65 || cols=> || maxlen=>255 || default=> || relID=> || selected=> || populatestr=> || populatevariables=> || align=> || filedir=>
wp_terms_slug || element=>hidden || desc1=> || desc2=> || errorcheck=> || english=> || event=> || size=> || cols=> || maxlen=> || default=>=slug($wp_terms_name); || relID=> || selected=> || populatestr=> || populatevariables=> || align=> || filedir=>
wp_term_taxonomy_taxonomy || element=>hidden || desc1=> || desc2=> || errorcheck=> || english=> || event=> || size=> || cols=> || maxlen=> || default=>category || relID=> || selected=> || populatestr=> || populatevariables=> || align=> || filedir=>
wp_term_taxonomy_description || element=>textarea || desc1=>Description || desc2=>The description is not prominent by default, however some themes may show it. || errorcheck=> || english=> || event=> || size=>3 || cols=>60 || maxlen=>500 || default=> || relID=> || selected=> || populatestr=> || populatevariables=> || align=> || filedir=>
wp_term_taxonomy_parent || element=>selectbox || desc1=>Category Parent || desc2=>Categories, unlike tags, can have a hierarchy. You might have a Jazz category, and under that have children categories for Bebop and Big Band. Totally optional. || errorcheck=> || english=> || event=> || size=>1 || cols=> || maxlen=> || default=> || relID=>term_id || selected=> || populatestr=>select * from wp_terms order by name || populatevariables=>term_id=>name || align=> || filedir=>
desc1_location || fieldset
desc2_location || bottom
pwhelp || passwords must be a minimum of 6 characters and cannot include any special characters
form_width || 450px
numsections || 1
section1numrows || 5
section1numcols || 1
section1title || Add/Edit Category
section2numrows || 
section2numcols || 
section2title || 
section3numrows || 
section3numcols || 
section3title || 
section4numrows || 
section4numcols || 
section4title || 
encoding || UTF-8
displayfile || Y
fckedit_toolbar || 
humanverify || N
humanverify || N
humanverify || N
css || 
section1row1 || wp_terms_name
section1row2 || wp_term_taxonomy_parent
section1row3 || wp_term_taxonomy_description
section1row4 || wp_terms_slug
section1row5 || wp_term_taxonomy_taxonomy
form_title || 
form_name || addedit
form_action || 
form_method || POST
form_enctype || multipart/form-data
onsubmit_action || 
form_success_redirect || 
form_failure_redirect || 
form_submit_text || Submit
form_insert_text || Category Successfully Added
form_edit_text || Category Has Been Updated
email_format || html
email_engine || mail
smtp_host || 
smtp_auth || 
smtp_user || 
smtp_pass || 
email_from || 
email_from_name || 
email_reply || 
email_bounce || 
send_email1 || No
email1_to || 
email1_cc || 
email1_subject || 
email1_body_default || 
send_email2 || No
email2_to || 
email2_cc || 
email2_subject || 
email2_body_default || 
attachment1 || 
attachment1_name || 
attachment2 || 
attachment2_name || 
humanverify_question || What is 2+3?
humanverify_answer || 5
