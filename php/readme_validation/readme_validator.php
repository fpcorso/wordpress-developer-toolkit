<?php
// This is the path to markdown.php
if ( !defined('WORDPRESS_README_MARKDOWN') ) {
  define('WORDPRESS_README_MARKDOWN', 'markdown.php');
}

if ( !class_exists( 'WPDT_Readme_Parser' ) ) {
  include( 'class-wpdt-readme-parser.php' );
}


function wpdt_validate_readme($readme_contents) {
  $readme = new WPDT_Readme_Parser;
	$r = $readme->parse_readme_contents($readme_contents);
	$warnings = array();
	$fatal_errors = array();
	$notes = array();
	// fatal errors
	if ( !$r['name'] )
		$fatal_errors[] = 'No plugin name detected.  Plugin names look like: <code>=== Plugin Name ===</code>';
	// warnings
	if ( !$r['requires_at_least'] )
		$warnings[] = '<code>Requires at least</code> is missing';
	if ( !$r['tested_up_to'] )
		$warnings[] = '<code>Tested up to</code> is missing';
	if ( !$r['stable_tag'] )
		$warnings[] = '<code>Stable tag</code> is missing.  Hint: If you treat <code>/trunk/</code> as stable, put <code>Stable tag: trunk</code>';
	if ( !count($r['contributors']) )
		$warnings[] = 'No <code>Contributors</code> listed';
	if ( !count($r['tags']) )
		$warnings[] = 'No <code>Tags</code> specified';
	if ( $r['is_excerpt'] )
		$warnings[] = 'No <code>== Description ==</code> section was found... your short description section will be used instead';
	if ( $r['is_truncated'] )
		$warnings[] = 'Your short description exceeds the 150 character limit';
	// notes
	if ( !isset( $r['sections']['installation'] ) || !$r['sections']['installation'] )
		$notes[] = 'No <code>== Installation ==</code> section was found';
	if ( !isset( $r['sections']['frequently_asked_questions'] ) || !$r['sections']['frequently_asked_questions'] )
		$notes[] = 'No <code>== Frequently Asked Questions ==</code> section was found';
	if ( !isset( $r['sections']['changelog'] ) || !$r['sections']['changelog'] )
		$notes[] = 'No <code>== Changelog ==</code> section was found';
	if ( !isset( $r['upgrade_notice'] ) || !$r['upgrade_notice'] )
		$notes[] = 'No <code>== Upgrade Notice ==</code> section was found';
	if ( !isset( $r['sections']['screenshots'] ) || !$r['sections']['screenshots'] )
		$notes[] = 'No <code>== Screenshots ==</code> section was found';
	if ( !isset( $r['donate_link'] ) || !$r['donate_link'] )
		$notes[] = 'No donate link was found';

  ob_start();
  // print those errors, warnings, and notes
	if ( $fatal_errors ) {
		echo "<div class='fatal error'><p>Fatal Error:</p>\n<ul class='fatal error'>\n";
		foreach ( $fatal_errors as $e )
			echo "<li>$e</li>\n";
		echo "</ul>\n</div>";
		return; // no point staying
	}
	if ( $warnings ) {
		echo "<div class='warning error'><p>Warnings:</p>\n<ul class='warning error'>\n";
		foreach ( $warnings as $e )
			echo "<li>$e</li>\n";
		echo "</ul>\n</div>";
	}
	if ( $notes ) {
		echo "<div class='note error'><p>Notes:</p>\n<ul class='note error'>\n";
		foreach ( $notes as $e )
			echo "<li>$e</li>\n";
		echo "</ul>\n</div>";
	}
	if ( !$notes && !$warnings && !$fatal_errors )
		echo "<div class='success'><p>Your <code>readme.txt</code> rocks.  Seriously.  Flying colors.</p></div>\n";
	else
		echo "<a href='#re-edit'>Re-Edit your Readme File</a>\n";
	// Show the data, as interpreted
	?>
	<hr />

	<h1><?php echo $r['name']; ?></h1>

	<p><em><?php echo $r['short_description']; ?></em></p>

	<hr />

	<p>
	<strong>Contributors:</strong> <?php echo implode(', ', $r['contributors']); ?><br />
	<strong>Donate link:</strong> <?php echo $r['donate_link']; ?><br />
	<strong>Tags:</strong> <?php echo implode(', ', $r['tags']);?><br />
	<strong>Requires at least:</strong> <?php echo $r['requires_at_least']; ?><br />
	<strong>Tested up to:</strong> <?php echo $r['tested_up_to']; ?><br />
	<strong>Stable tag:</strong> <?php echo $r['stable_tag']; ?>
	</p>

	<hr />

	<?php foreach ( $r['sections'] as $title => $section ) : ?>
	<h3><?php echo ucwords(str_replace('_', ' ', $title)); ?></h3>
	<?php echo function_exists( 'apply_filters' ) ? apply_filters( 'validator_section', $section ) : $section; ?>
	<hr />
	<?php endforeach; ?>

	<h3>Upgrade Notice</h3>
	<dl>
  <?php
  if ( isset( $r['upgrade_notice'] ) ) {
    foreach ( $r['upgrade_notice'] as $version => $notice ) : ?>
  		<dt><?php echo $version; ?></dt>
  		<dd><?php echo $notice; ?></dd>
      <?php
    endforeach;
  }
  ?>
	</dl>

	<?php echo $r['remaining_content'];
  return ob_get_clean();
}
?>
