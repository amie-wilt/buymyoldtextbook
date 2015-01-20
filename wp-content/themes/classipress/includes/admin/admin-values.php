<?php
/**
 *
 * Here is where all the admin field data is stored
 * All the data is stored in arrays and then looped though
 * @author AppThemes
 * @version 3.0
 *
 * Array param definitions are as follows:
 * name    = field name
 * desc    = field description
 * tip     = question mark tooltip text
 * id      = database column name or the WP meta field name
 * css     = any on-the-fly styles you want to add to that field
 * type    = type of html field
 * req     = if the field is required or not (1=required)
 * min     = minimum number of characters allowed before saving data
 * std     = default value. not being used
 * js      = allows you to pass in javascript for onchange type events
 * vis     = if field should be visible or not. used for dropdown values field
 * visid   = this is the row css id that must correspond with the dropdown value that controls this field
 * options = array of drop-down option value/name combo
 * altclass = adds a new css class to the input field (since v3.1)
 *
 *
 */



$options_new_form = array (

    array( 'type' => 'notab'),

	array(	'name' => __( 'New Form', APP_TD ),
                'type' => 'title'),

		array(  'name' => __( 'Form Name', APP_TD ),
                        'desc' => '',
                        'tip' => __( 'Create a form name that best describes what category or categories this form will be used for. (i.e. Auto Form, Clothes Form, General Form, etc). It will not be visible on your site.', APP_TD ),
                        'id' => 'form_label',
                        'css' => 'min-width:400px;',
                        'type' => 'text',
                        'vis' => '',
                        'req' => '1',
                        'min' => '5',
                        'std' => ''),

                array(  'name' => __( 'Form Description', APP_TD ),
                        'desc' => '',
                        'tip' => __( 'Enter a description of your new form layout. It will not be visible on your site.', APP_TD ),
                        'id' => 'form_desc',
                        'css' => 'width:400px;height:100px;',
                        'type' => 'textarea',
                        'vis' => '',
                        'req' => '1',
                        'min' => '5',
                        'std' => ''),

                array(  'name' => __( 'Available Categories', APP_TD ),
                        'desc' => '',
                        'tip' => __( 'You can assign a form layout to multiple categories. A category can only have one form layout assigned to it. Categories not listed are being used on a different form layout. Any unselected categories will use the default ad form.', APP_TD ),
                        'id' => 'post_category[]',
                        'css' => '',
                        'type' => 'cat_checklist',
                        'vis' => '',
                        'req' => '1',
                        'std' => ''),

                array(  'name' => __( 'Status', APP_TD ),
                        'desc' => '',
                        'tip' => __( 'If you do not want this new form live on your site yet, select inactive.', APP_TD ),
                        'id' => 'form_status',
                        'css' => 'min-width:100px;',
                        'std' => '',
                        'js' => '',
                        'vis' => '',
                        'req' => '1',
                        'type' => 'select',
                        'options' => array( 'active'   => __( 'Active', APP_TD ),
                                            'inactive' => __( 'Inactive', APP_TD ) )),

    array( 'type' => 'notabend'),

);


$options_new_field = array (

    array( 'type' => 'notab'),

	array(	'name' => __( 'Field Information', APP_TD ),
                'type' => 'title'),

		array(  'name' => __( 'Field Name', APP_TD ),
                        'desc' => '',
                        'tip' => __( 'Create a field name that best describes what this field will be used for. (i.e. Color, Size, etc). It will be visible on your site.', APP_TD ),
                        'id' => 'field_label',
                        'css' => 'min-width:400px;',
                        'type' => 'text',
                        'req' => '1',
                        'vis' => '',
                        'min' => '2',
                        'std' => ''),

                array(  'name' => __( 'Meta Name', APP_TD ),
                        'desc' => '',
                        'tip' => __( 'This field is used by WordPress so you cannot modify it. Doing so could cause problems displaying the field on your ads.', APP_TD ),
                        'id' => 'field_name',
                        'css' => 'min-width:400px;',
                        'type' => 'text',
                        'req' => '1',
                        'vis' => '',
                        'min' => '5',
                        'std' => '',
                        'dis' => '1'),

                array(  'name' => __( 'Field Description', APP_TD ),
                        'desc' => '',
                        'tip' => __( 'Enter a description of your new form layout. It will not be visible on your site.', APP_TD ),
                        'id' => 'field_desc',
                        'css' => 'width:400px;height:100px;',
                        'type' => 'textarea',
                        'req' => '1',
                        'vis' => '',
                        'min' => '5',
                        'std' => ''),
				
                array(  'name' => __( 'Field Tooltip', APP_TD ),
                        'desc' => '',
                        'tip' => __( 'This will create a ? tooltip icon next to this field on the submit ad page.', APP_TD ),
                        'id' => 'field_tooltip',
                        'css' => 'width:400px;height:100px;',
                        'type' => 'textarea',
                        'req' => '0',
                        'vis' => '',
                        'min' => '5',
                        'std' => ''),
				
               array(   'name' => __( 'Field Type', APP_TD ),
                        'desc' => '',
                        'tip' => __( 'This is the type of field you want to create.', APP_TD ),
                        'id' => 'field_type',
                        'css' => 'min-width:100px;',
                        'std' => '',
                        'js' => 'onchange="show(this)"',
                        'req' => '1',
                        'vis' => '',
                        'min' => '',
                        'type' => 'select',
                        'options' => array( 'text box'  => __( 'text box', APP_TD ),
                                            'drop-down' => __( 'drop-down', APP_TD ),
                                            'text area' => __( 'text area', APP_TD ),
                                            'radio'     => __( 'radio buttons', APP_TD ),
                                            'checkbox'  => __( 'checkboxes', APP_TD ),
                                          ),
                   ),
				   
				   array(  'name' => __( 'Minimum Length', APP_TD ),
                        'desc' => '',
                        'tip' => __( 'Defines the minimum number of characters required for this field. Enter a number like 2 or enter 0 to make the field optional.', APP_TD ),
                        'id' => 'field_min_length',
                        'css' => 'min-width:400px;',
                        'type' => 'text',
                        'req' => '0',
                        'vis' => '0',
                        'min' => '',
                        'std' => ''),

                array(  'name' => __( 'Field Values', APP_TD ),
                        'desc' => '',
                        'tip' => __( 'Enter a comma separated list of values you want to appear in this drop-down box. (i.e. XXL,XL,L,M,S,XS). Do not separate values with the return key.', APP_TD ),
                        'id' => 'field_values',
                        'css' => 'width:400px;height:200px;',
                        'type' => 'textarea',
                        'req' => '',
                        'min' => '1',
                        'std' => '',
                        'vis' => '0',
                    ),

//               array(  'name' => __( 'Add to Search Widget', APP_TD ),
//                        'desc' => '',
//                        'tip' => __( 'Checking this will include this field on the search box on your website. It is perfect for things like regional search. (Note: It should only be used for text or drop-down fields.)', APP_TD ),
//                        'id' => 'field_search',
//                        'css' => '',
//                        'type' => 'checkbox',
//                        'req' => '1',
//                        'min' => '5',
//                        'std' => '',
//                   ),

    array( 'type' => 'notabend'),

);

$options_new_ad_pack = array (


    array( 'type' => 'notab'),

	array(	'name' => __( 'Ad Pack Details', APP_TD ),
                'type' => 'title',
                'desc' => '',
             ),

		array(  'name' => __( 'Name', APP_TD ),
                        'desc' => '',
                        'tip' => __( 'Create a name that best describes this ad package. (i.e. 30 days for only $5) This will be visible on your new ad listing submission page.', APP_TD ),
                        'id' => 'pack_name',
                        'css' => 'min-width:400px;',
                        'type' => 'text',
                        'vis' => '',
                        'req' => '1',
                        'min' => '5',
                        'std' => ''),

               array(  'name' => __( 'Description', APP_TD ),
                        'desc' => '',
                        'tip' => __( 'Enter a description of your new ad package. It will not be visible on your site.', APP_TD ),
                        'id' => 'pack_desc',
                        'css' => 'width:400px;height:100px;',
                        'type' => 'textarea',
                        'req' => '1',
                        'min' => '5',
                        'std' => ''),

                array(  'name' => __( 'Price Per Listing', APP_TD ),
                        'desc' => '',
                        'tip' => __( 'Enter a numeric value for this package (do not enter a currency symbol or commas). For ad packs, this will be the price to post an ad.', APP_TD ),
                        'id' => 'pack_price',
                        'css' => 'width:75px;',
                        'type' => 'text',
                        'vis' => '',
                        'req' => '1',
                        'min' => '1',
                        'std' => ''),

                array(  'name' => __( 'Ad Duration', APP_TD ),
                        'desc' => '',
                        'tip' => __( 'Enter a numeric value to specify the number of days for this ad package (i.e. 30, 60, 90, 120).', APP_TD ),
                        'id' => 'pack_duration',
                        'css' => 'width:75px;',
                        'type' => 'text',
                        'vis' => '',
                        'req' => '1',
                        'min' => '1',
                        'std' => ''),

                array(  'name' => __( 'Package Status', APP_TD ),
                        'desc' => '',
                        'tip' => __( 'If you do not want this ad package live on your site, select inactive.', APP_TD ),
                        'id' => 'pack_status',
                        'css' => 'min-width:100px;',
                        'std' => '',
                        'js' => '',
                        'req' => '1',
                        'type' => 'select',
                        'options' => array( 'active'   => __( 'Active', APP_TD ),
											'inactive' => __( 'Inactive', APP_TD ) )),

     array( 'type' => 'notabend'),


);


$options_new_membership_pack = array (


    array( 'type' => 'notab'),

	array(	'name' => __( 'Membership Pack Details', APP_TD ),
                'type' => 'title',
                'desc' => '',
             ),

		array(  'name' => __( 'Name', APP_TD ),
                        'desc' => '',
                        'tip' => __( 'Create a name that best describes this membership package. (i.e. 30 days unlimited posting for only $25) This will be visible on your ad listing submission page.', APP_TD ),
                        'id' => 'pack_name',
                        'css' => 'min-width:400px;',
                        'type' => 'text',
                        'vis' => '',
                        'req' => '1',
                        'min' => '5',
                        'std' => ''),

               array(  'name' => __( 'Description', APP_TD ),
                        'desc' => '',
                        'tip' => __( 'Enter a description of your new membership package. It will not be visible on your site.', APP_TD ),
                        'id' => 'pack_desc',
                        'css' => 'width:400px;height:100px;',
                        'type' => 'textarea',
                        'req' => '1',
                        'min' => '5',
                        'std' => ''),
                        
                array(  'name' => __( 'Package Type', APP_TD ),
                        'desc' => '',
                        'tip' => __( 'Select which package type to change how the pack affects the price of the ad during the posting process.', APP_TD ),
                        'id' => 'pack_type',
                        'css' => 'min-width:100px;',
                        'std' => '',
                        'js' => '',
                        'req' => '1',
                        'type' => 'select',
                        'options' => array( 'static'     => __( 'Static Price', APP_TD ),
											'discount'   => __( 'Discounted Price', APP_TD ),
											'percentage' => __( '% Discounted Price', APP_TD ), )),     
											
                array(  'name' => __( 'Membership Price', APP_TD ),
                        'desc' => '',
                        'tip' => __( 'The price this membership will cost your customer to purchase. Enter a numeric value (do not enter a currency symbol or commas).', APP_TD ),
                        'id' => 'pack_membership_price',
                        'css' => 'width:75px;',
                        'type' => 'text',
                        'vis' => '',
                        'req' => '1',
                        'min' => '1',
                        'std' => '1.00'),
        
                array(  'name' => __( 'Membership Duration', APP_TD ),
                        'desc' => __( 'Enter a numeric value for the number of days', APP_TD ),
                        'tip' => __( 'The length of time in days this membership lasts. Enter a numeric value (i.e. 30, 60, 90, 120).', APP_TD ),
                        'id' => 'pack_duration',
                        'css' => 'width:75px;',
                        'type' => 'text',
                        'vis' => '',
                        'req' => '1',
                        'min' => '1',
                        'std' => ''),

                array(  'name' => __( 'Price Modifier <br /> (How a membership <br /> affects the price of an ad)', APP_TD ),
                        'desc' => __( 'Enter #.## for currency (i.e. 2.25 for $2.25), ### for percentage (i.e. 50 for 50%).', APP_TD ),
                        'tip' => __( 'Enter a numeric value (do not enter a currency symbol or commas). This will modify the checkout price based on the selected package type.', APP_TD ),
                        'id' => 'pack_price',
                        'css' => 'width:75px;',
                        'type' => 'text',
                        'vis' => '',
                        'req' => '1',
                        'min' => '1',
                        'std' => ''),

				
                array(  'name' => __( 'Satisfies Membership Requirement', APP_TD ),
                        'desc' => sprintf( __( "If the &quot;<a href='%s'>Are Membership Packs Required to Purchase Ads</a>&quot; option under the Membership tab is set to yes, you should select yes.", APP_TD ), 'admin.php?page=app-pricing&tab=membership' ),
                        'tip' => __( 'Selecting no means that this membership does not allow the customer to post to categories requiring membership. You would select no if you wanted to separate memberships that are required to post versus memberships that simply affect the final price.', APP_TD ),
                        'id' => 'pack_satisfies_required',
                        'css' => 'min-width:100px;',
                        'std' => '',
                        'js' => '',
                        'req' => '',
                        'type' => 'select',
                        'options' => array( 'required_'   => __( 'Yes', APP_TD ),
											         ''   => __( 'No', APP_TD ), )),


                array(  'name' => __( 'Package Status', APP_TD ),
                        'desc' => '',
                        'tip' => __( 'Allows you to temporarily turn off this package instead of deleting it. Please note that existing active memberships will still be able to list ads at their discounted price unless memberships are turned off globally from the Pricing => Membership tab.', APP_TD ),
                        'id' => 'pack_status',
                        'css' => 'min-width:100px;',
                        'std' => '',
                        'js' => '',
                        'req' => '1',
                        'type' => 'select',
                        'options' => array( 'active_membership'   => __( 'Active', APP_TD ),
                                            'inactive_membership' => __( 'Inactive', APP_TD ), )),

     array( 'type' => 'notabend'),


);



