<?php

// globalwidgets Dashboard
// ---------

require_once("../../config.php");
global $DB;

// Security.
$context = context_system::instance();
require_login();


// Page boilerplate stuff.
$url = new moodle_url('/local/globalwidgets/dashboard.php');
$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_pagelayout('admin');
$title = "globalwidgets Dashboard";
$PAGE->set_title($title);
$PAGE->set_heading($title);





echo $OUTPUT->header();

// var_dump($_POST);

// INSERTING A NEW GLOBAL WIDGET
if($_POST['submitbutton'] == "Save"){
	if($_POST['title'] !=  ""){
		
		// --------
		// INSERT
		// --------
		$data = new stdClass();
		$data->title = $_POST['title'];
		$data->content = $_POST['content']['text'];
		
		$DB->insert_record('globalwidgets_datacache', $data, false);
		
		?>

			<div class="alert alert-success" role="alert">
				<?php echo get_string('ContentUpdated', 'local_globalwidgets'); ?>
			</div>
		
		<?php
		
	}
}

// UPDATE A FORM
if($_POST['update_form_id'] != "" && $_POST['cancelbutton'] != "Cancel"){
		
	// UPDATE
	//var_dump('UPDATE {globalwidgets_datacache} SET content = "'.html_entity_decode($_POST['content']['text']).'" WHERE id="'.intval($_POST['update_form_id']).'"');
	//$DB->execute('UPDATE {globalwidgets_datacache} SET content = "'.html_entity_decode($_POST['content']['text']).'" WHERE id="'.intval($_POST['update_form_id']).'"');
	
	
	$data = new stdClass();
	$data->id = $_POST['update_form_id'];
	$data->content = $_POST['content']['text'];
	
	$DB->update_record('globalwidgets_datacache', $data, $bulk=false)

	
	// --------
	// INSERT
	// --------
	// $data = new stdClass();
	// $data->title = $_POST['update_update_title'];
	// $data->content = $_POST['content']['text'];
	
	// $DB->insert_record('globalwidgets_datacache', $data, false);
	
	?>

		<div class="alert alert-success" role="alert">
			<?php echo get_string('ContentUpdated', 'local_globalwidgets'); ?>
		</div>
	
	<?php
	
}


		

if($_GET['action'] == ""){
	?>

		<h1><?php echo get_string('GlobalWidgets', 'local_globalwidgets'); ?></h1>
		<hr />
		
		<a class="btn btn-success" href="<?php echo $CFG->wwwroot; ?>/local/globalwidgets/dashboard.php?action=new"><?php echo get_string('CreateNewGlobalContent', 'local_globalwidgets'); ?></a>
		
		<hr />
		
		<?php
		
			$widgets = $DB->get_records_sql('SELECT * FROM {globalwidgets_datacache} ORDER BY title ASC', array(1));
			foreach($widgets as $widget){
				?>

					<p>
						<a href="<?php echo $CFG->wwwroot; ?>/local/globalwidgets/dashboard.php?action=edit&id=<?php echo $widget->id; ?>" class="btn btn-primary mr-4"><?php echo get_string('Edit', 'local_globalwidgets'); ?></a>
						<a class="btn btn-danger" href="<?php echo $CFG->wwwroot; ?>/local/globalwidgets/dashboard.php?action=delete&id=<?php echo $widget->id; ?>"><?php echo get_string('Delete', 'local_globalwidgets'); ?></a>
						<span style="padding-left:20px;"><?php echo format_text($widget->title, FORMAT_HTML, null); ?></span>
					</p>
					
		
					<hr />
				
				<?php
			}
		
		?>

	<?php
}





if($_GET['action'] == "delete"){
	
	$content_data = $DB->get_record_sql("SELECT * FROM {globalwidgets_datacache} WHERE id = '".intval($_GET['id'])."'", array(1));
	
	?>

		<h1><?php echo get_string('ConfirmDeletion', 'local_globalwidgets'); ?>: <?php echo $content_data->title; ?></h1>
		<hr />
		
		<a class="btn btn-danger" href="<?php echo $CFG->wwwroot; ?>/local/globalwidgets/dashboard.php?action=confirm_delete&id=<?php echo $_GET['id']; ?>"><?php echo get_string('Delete', 'local_globalwidgets'); ?></a>
		
	<?php
}

if($_GET['action'] == "confirm_delete"){
	
	// Clean
	$DB->execute("DELETE FROM {globalwidgets_datacache} WHERE id=".intval($_GET['id'])."");
	
	?>
		
		<div class="alert alert-danger" role="alert">
			<?php echo get_string('ContentDeleted', 'local_globalwidgets'); ?>
		</div>
		<a class="btn btn-primary" href="<?php echo $CFG->wwwroot; ?>/local/globalwidgets/dashboard.php"><?php echo get_string('ReturnToGlobalWidgetsEditor', 'local_globalwidgets'); ?></a>
		
	<?php
}







if($_GET['action'] == "new"){
	?>

		<h1><?php echo get_string('CreateNewGlobalWidgets', 'local_globalwidgets'); ?></h1>
		<hr />
		
		<?php

		// FORMS LIBRARY
		require_once("$CFG->libdir/formslib.php");
		
		// NEW FORM
		class globalwidgets_form extends moodleform {
			//Add elements to form
			public function definition() {
				global $CFG, $DB;
			   
				$mform = $this->_form; // Don't forget the underscore! 

				// TITLE
				$mform->addElement('text', 'title', get_string('Title', 'local_globalwidgets')); // Add elements to your form.
				$mform->setType('text', PARAM_NOTAGS);                   // Set type of element.
				$mform->setDefault('text', 'Default Content Block Title');        // Default value.
				
				// TEXT EDITOR
				$mform->addElement('editor', 'content', get_string('Content', 'local_globalwidgets'));
				$mform->setType('content', PARAM_RAW);
				
				
				$buttonarray=array();
				$buttonarray[] = $mform->createElement('submit', 'submitbutton', get_string('Save', 'local_globalwidgets'));
				//$buttonarray[] = $mform->createElement('reset', 'resetbutton', get_string('revert'));
				$buttonarray[] = $mform->createElement('cancel', 'cancelbutton', get_string('Cancel', 'local_globalwidgets'));
				
				$mform->addGroup($buttonarray, 'buttonar', '', ' ', false);
			 
			}
			//Custom validation should be added here
			function validation($data, $files) {
				return array();
			}
		}

		
		//Instantiate simplehtml_form 
		$mform = new globalwidgets_form();

		//Form processing and displaying is done here
		if ($mform->is_cancelled()) {
			//Handle form cancel operation, if cancel button is present on form
		} else if ($fromform = $mform->get_data()) {
		  //In this case you process validated data. $mform->get_data() returns data posted in form.
		} else {
		  // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
		  // or on the first display of the form.

		  //Set default data (if any)
		  $mform->set_data($toform);
		  //displays the form
		  $mform->display();
		}
		

		?>
	
		<hr />


	<?php
}


if($_GET['action'] == "edit"){
	?>

		<h1><?php echo get_string('EditingNewGlobalWidgets', 'local_globalwidgets'); ?></h1>
		<hr />
		
		<?php

		// FORMS LIBRARY
		require_once("$CFG->libdir/formslib.php");
		
		// NEW FORM
		class globalwidgets_form extends moodleform {
			//Add elements to form
			public function definition() {
				global $CFG, $DB;
				
				// GET BLOCK DATA
				// ----------------
				
				$content_data = $DB->get_record_sql("SELECT * FROM {globalwidgets_datacache} WHERE id = '".intval($_GET['id'])."'", array(1));
				//var_dump($content_data);
				
				// BUILD FORM
				// ----------------
			   
				$mform = $this->_form; // Don't forget the underscore! 
				
				$mform->addElement('hidden','update_form_id','form_id',$_GET['id']);
				$mform->setDefault('update_form_id', $_GET['id']);
				
				$mform->addElement('hidden','update_update_title',$content_data->title, $content_data->title );
				$mform->setDefault('update_title', $content_data->title);

				// TITLE
				$mform->addElement('text', 'title', get_string('Title', 'local_globalwidgets')); // Add elements to your form.
				$mform->setType('text', PARAM_NOTAGS);                   // Set type of element.
				$mform->setDefault('title', $content_data->title);
				
				// TEXT EDITOR
				$mform->addElement('editor', 'content', get_string('Content', 'local_globalwidgets'));
				$mform->setType('content', PARAM_HTML);
				
				// DEFAULT SELECTION FOR CONTENT MUST BE ADDED *AFTER* DOM
				$mform->addElement('static', null, '',
                    '<script type="text/javascript">
				//<![CDATA[
					jQuery(document).ready(function() {
						jQuery("#id_title").prop( "disabled", true );
						jQuery("#id_content").html("'.htmlspecialchars($content_data->content).'");
					});
				//]]>
				</script>');


				
				$buttonarray=array();
				$buttonarray[] = $mform->createElement('submit', 'submitbutton', get_string('Save', 'local_globalwidgets'));
				//$buttonarray[] = $mform->createElement('reset', 'resetbutton', get_string('revert'));
				$buttonarray[] = $mform->createElement('cancel', 'cancelbutton', get_string('Cancel', 'local_globalwidgets'));
				
				$mform->addGroup($buttonarray, 'buttonar', '', ' ', false);
			 
			}
			//Custom validation should be added here
			function validation($data, $files) {
				return array();
			}
		}

		
		//Instantiate simplehtml_form 
		$mform = new globalwidgets_form();

		//Form processing and displaying is done here
		if ($mform->is_cancelled()) {
			//Handle form cancel operation, if cancel button is present on form
		} else if ($fromform = $mform->get_data()) {
		  //In this case you process validated data. $mform->get_data() returns data posted in form.
		} else {
		  // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
		  // or on the first display of the form.

		  //Set default data (if any)
		  $mform->set_data($toform);
		  //displays the form
		  $mform->display();
		}
		

		?>
	
		<hr />


	<?php
}


echo $OUTPUT->footer();