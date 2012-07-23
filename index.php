<?php
if (!defined('DC_CONTEXT_ADMIN')) { exit; }

require_once dirname(__FILE__).'/class.dc.planning.php';
$planning = new dcPlanning($core);

$default_tab = '';

$new_date = '';
$post_id = 0;
$title = '';

# Add a session date
if (!empty($_POST['add_date']))
{
	$title = $_POST['title'];
	$post_id = $_POST['post_id'];
	$new_date = $_POST['date'];
	
	try {
		$planning->addDate($new_date, $title, $post_id);
		http::redirect($p_url.'&added=1');
	} catch (Exception $e) {
		$core->error->add($e->getMessage());
		$default_tab = 'add-date';
	}
}

# Delete a session date
if (!empty($_POST['removeaction']) && !empty($_POST['remove'])) {
	foreach ($_POST['remove'] as $v)
	{
		try {
			$planning->delDate($v);
		} catch (Exception $e) {
			$core->error->add($e->getMessage());
			break;
		}
	}
	
	if (!$core->error->flag()) {
		http::redirect($p_url.'&removed=1');
	}
}

# Edit a session
if (!empty($_POST['edit_date']))
{
	$title = $_POST['title'];
	$post_id = $_POST['post_id'];
	$new_date = $_POST['date'];
	
	try {
		$planning->updateDate($new_date, $title, $post_id);
		http::redirect($p_url.'&updated=1');
	} catch (Exception $e) {
		$core->error->add($e->getMessage());
		$default_tab = 'add-date';
	}
}

# Get dates
try {
	$dates = $planning->getDates(true);
} catch (Exception $e) {
	$core->error->add($e->getMessage());
}

# Get posts
$params = array();
try {
	$posts = $core->blog->getPosts($params);
} catch (Exception $e) {
	$core->error->add($e->getMessage());
}
$posts_labels = array();
while( $posts->fetch()) {
	$posts_labels[$posts->post_title] = $posts->post_id;
}

?>
<html>
<head>
  <title>Planning</title>
  <?php echo dcPage::jsToolMan(); 
  echo dcPage::jsDatePicker();
  echo dcPage::jsPageTabs($default_tab); 
  ?>
</head>

<body>

<h2><?php echo html::escapeHTML($core->blog->name); ?> &gt; <?php echo __('Planning configuration'); ?></h2>


<?php
if (!empty($_GET['removed'])) {
		echo '<p class="message">'.__('The session(s) has(have) been successfully removed.').'</p>';
}

if (!empty($_GET['added'])) {
		echo '<p class="message">'.__('Session date has been successfully created.').'</p>';
}

if (!empty($_GET['updated'])) {
		echo '<p class="message">'.__('Session date has been successfully updated.').'</p>';
}
?>

<div class="multi-part" title="<?php echo __('Planning'); ?>">
<form action="plugin.php" method="post" id="dates-form">
<?php echo form::hidden('p','planning'); echo $core->formNonce(); ?>

	<table class="maximal dragable">
	<thead>
	<tr>
	  <th colspan="2"><?php echo __('Title'); ?></th>
	  <th><?php echo __('Date'); ?></th>
	  <th><?php echo __('Post'); ?></th>
	</tr>
	</thead>
	<tbody id="dates-list">
	<?php
	foreach($dates as $date)
	{
		echo "<tr>";
		echo '<td class="minimal">'.form::checkbox(array('remove[]'),$date['date_raw']).'</td>';
		echo "<td>".$date['title']."</td>";
		echo "<td>".$date['date']."</td>";
		echo "<td>".$date['post_title']."</td>";
		echo "</tr>";
	}
	?>
	</tbody>
	</table>

	<div class="two-cols">

	<p class="col right"><input type="submit" name="removeaction"
	value="<?php echo __('Delete selected dates'); ?>"
	onclick="return window.confirm('<?php echo html::escapeJS(
	__('Are you sure you you want to delete selected dates?')); ?>');" /></p>
	</div>

</form>
</div>

<?php
echo
'<div class="multi-part" id="add-date" title="'.__('Add a session date').'">'.
'<form action="plugin.php" method="post" id="add-date-form">'.
'<fieldset class="two-cols"><legend>'.__('Add a new session date').'</legend>'.
'<p class="col"><label class="required" title="'.__('Required field').'">'.__('Title:').' '.
form::field('title',30,255,$title,'',2).
'</label></p>'.

'<p class="col"><label class="required" title="'.__('Required field').'">'.__('Post:').' '.
form::combo('post_id',$posts_labels).
'</label></p>'.

'<p class="col"><label class="required" title="'.__('Required field').'">'.__('Date:').' '.
form::field('date',16,16,$new_date,'',4).
'</label></p>'.

'<p>'.form::hidden(array('p'),'planning').
$core->formNonce().
'<input type="submit" name="add_date" value="'.__('Save').'" tabindex="6" /></p>'.
'</fieldset>'.
'</form>'.
'</div>';
?>

<pre>
<?php
?>
</pre>

<script type="text/javascript">
//<![CDATA[
var post_dtPick = new datePicker($('#date').get(0));
	post_dtPick.img_top = '1.5em';
	post_dtPick.draw();
//]]>
</script>
</body>
</html>