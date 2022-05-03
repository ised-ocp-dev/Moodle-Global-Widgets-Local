<?php

// globalwidgets Dashboard
// ---------

require_once("../../config.php");
global $DB;

// Security.
$context = context_system::instance();
require_login();
require_capability('moodle/site:config', $context);

// Page boilerplate stuff.
$url = new moodle_url('/local/globalwidgets/dashboard.php');
$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_pagelayout('admin');
$title = "globalwidgets Dashboard";
$PAGE->set_title($title);
$PAGE->set_heading($title);





echo $OUTPUT->header();

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
			Content Saved!
		</div>
	
	<?php
	
}

if($_GET['action'] == ""){
	?>

		<h1>Global Widgets</h1>
		<hr />
		
		<a class="btn btn-primary" href="<?php echo $CFG->wwwroot; ?>/local/globalwidgets/dashboard.php?action=new">Create New Global Content</a>
		
		<hr />
		
		<?php
		
			$widgets = $DB->get_records_sql('SELECT * FROM {globalwidgets_datacache}', array(1));
			foreach($widgets as $widget){
				?>

					<p><a href="" class="btn btn-primary mr-4">Edit</a><?php echo $widget->title; ?></p>
					<hr />
				
				<?php
			}
		
		?>

	<?php
}

if($_GET['action'] == "new"){
	?>

		<h1>Create New Global Widgets</h1>
		<hr />
		
		<?php

		// FORMS LIBRARY
		require_once("$CFG->libdir/formslib.php");
		
		// NEW FORM
		class globalwidgets_form extends moodleform {
			//Add elements to form
			public function definition() {
				global $CFG;
			   
				$mform = $this->_form; // Don't forget the underscore! 

				// TITLE
				$mform->addElement('text', 'title', 'Title'); // Add elements to your form.
				$mform->setType('text', PARAM_NOTAGS);                   // Set type of element.
				$mform->setDefault('text', 'Content Block Title');        // Default value.
				
				// TEXT EDITOR
				$mform->addElement('editor', 'content', 'Content');
				$mform->setType('content', PARAM_RAW);
				
				
				$buttonarray=array();
				$buttonarray[] = $mform->createElement('submit', 'submitbutton', 'Save');
				//$buttonarray[] = $mform->createElement('reset', 'resetbutton', get_string('revert'));
				$buttonarray[] = $mform->createElement('cancel', 'cancelbutton', 'Cancel');
				
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