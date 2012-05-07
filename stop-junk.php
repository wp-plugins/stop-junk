<?php

/*

Plugin Name: Stop Junk

Plugin URI: http://stopjunk.megatag.me/

Description: Plugin so user needs to enter result of a simple math problem in a text box before posting a comment. User will only be prompted if not logged in.

Version: 1.0

Author: Matthew Bretag

Author URI: http://stopjunk.megatag.me/

License: GPL2

*/



/*  Copyright 2012  Matthew Bretag  (email : mbretag@gmail.com)



    This program is free software; you can redistribute it and/or modify

    it under the terms of the GNU General Public License, version 2, as 

    published by the Free Software Foundation.



    This program is distributed in the hope that it will be useful,

    but WITHOUT ANY WARRANTY; without even the implied warranty of

    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the

    GNU General Public License for more details.



    You should have received a copy of the GNU General Public License

    along with this program; if not, write to the Free Software

    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/



if ( !class_exists( 'StopJunk' ) || ( defined( 'WP_DEBUG') && WP_DEBUG ) ) {



class StopJunk {

	// Constructor

	function StopJunk() {

		//after form is submitted check math problem was solved

		add_filter( 'preprocess_comment', array( &$this,'check_math' ) );	

		

		//add math problem after comment box	

		add_filter( 'comment_form_field_comment', array( &$this,'add_math' ) );				

		

		//admin settings

		add_action('admin_menu', array( &$this,'stop_junk_add_pages' ) );

	}

	

	//check submitted value is correct

	function check_math( $commentdata ) {

		$user = wp_get_current_user();

		if ( !$user->ID ) 

		{

			$val = $_POST['math_val'];

			$num1 = $_POST['num1'];

			$num2 = $_POST['num2'];

			if ( strlen($val) == 0 || ($num1+$num2 != $val))

			{

				wp_die( __('Error: Please enter the correct result of the math problem.') );

			}		

		}

		return $commentdata;

	}

	

	//add math validation below comments box

	function add_math($default) {

		$text_colour = get_option("stop_junk_math_color"); //check admin setting

		if(strlen($text_colour) > 0)

		{

			$text_colour = " style=\"color:" . $text_colour . ";\"";

		}

		$user = wp_get_current_user();

		if ( !$user->ID ) 

		{

			$num1 = rand(0,9);		

			$num2 = rand(0,10-$num1); //choose second number so total value <= 10

			

			$default .= "		

			<p class=\"stop-junk-math\"><label for=\"math_val\">Validation Code</label><span class=\"required\">*</span>

				<span$text_colour>$num1+$num2=?</span>

				<input style=\"width:100px;\" id=\"math_val\" name=\"math_val\" type=\"text\" size=\"10\" aria-required='true' />

			</p>	

			<input name=\"num1\" value=\"$num1\" type=\"hidden\" />

			<input name=\"num2\" value=\"$num2\" type=\"hidden\" />

			";

							

		}

		return $default;

	}

	

	//ADMIN FUNCTIONS

	

	function stop_junk_add_pages() {

		add_plugins_page(__('Stop Junk Plugin','menu-stop-junk'), __('Stop Junk Plugin','menu-stop-junk'), 'manage_options', 'stopjunkplugin', array( &$this,'stop_junk_plugin_page' ) );

	}

	

	// displays the page content for the Stop Junk settings submenu

	function stop_junk_plugin_page() {	

		//must check that the user has the required capability 

		if (!current_user_can('manage_options'))

		{

		  wp_die( __('You do not have sufficient permissions to access this page.') );

		}

	

		// variables for the field and option names 

		$opt_name = 'stop_junk_math_color';

		$hidden_field_name = 'stop_junk_submit_hidden';

		$data_field_name = 'stop_junk_math_color';

	

		// Read in existing option value from database

		$opt_val = get_option( $opt_name );

	

		// See if the user has posted some information

		// If they did, this hidden field will be set to 'Y'

		if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) 

		{

			// Read their posted value

			$opt_val = $_POST[ $data_field_name ];

	

			// Save the posted value in the database

			update_option( $opt_name, $opt_val );

	

			// Put a settings updated message on the screen

			?>

			<div class="updated"><p><strong><?php _e('settings saved.', 'menu-stop-junk' ); ?></strong></p></div>

			<?php

		}

	

		// display the settings editing screen

		echo '<div class="wrap">';

	

		// header

		echo "<h2>" . __( 'Stop Junk Plugin Settings', 'menu-stop-junk' ) . "</h2>";

	

		// settings form    

		?>

	

		<form name="form1" method="post" action="">

		<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">

		

		<p><?php _e("Math Color:", 'menu-stop-junk' ); ?> 

		<input type="text" name="<?php echo $data_field_name; ?>" value="<?php echo $opt_val; ?>" size="20">

		</p><hr />

		

		<p class="submit">

		<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />

		</p>

		

		</form>

		</div>

	

	<?php

	}

	

} // END CLASS



$wp_stop_junk = new StopJunk();



} // END



?>