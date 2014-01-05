<div id="conferencer_overview" class="wrap">
	<h2>Conferencer</h2>

	<p>Welcome to the Conferencer Wordpress Plugin.  This plugin will give you the tools you will need to create, manage, and display your conference information.  This diagram shows the different types of information that will define your conference and how they are related to each other.</p>
	<p>Each <strong>session</strong> in your conference is assigned a <strong>room</strong>, a <strong>time slot</strong>, <strong>tracks</strong>, <strong>speakers</strong>, and <strong>sponsors</strong>.  Sponsors are assigned to different <strong>sponsor levels</strong>.  Notice that the diagram shows these "one-to-many" relationships.  Each room will have any number of sessions.  Each sponsor level will have any number of sponsors.  Notice that some "to-many" relationships go both ways.  A session can have any number of speakers, and a speaker can be assigned to any number of sessions.</p>

	<img src="<?php echo CONFERENCER_URL; ?>/images/relationship-diagram.gif" alt="relationship diaram" />

	<p>Once you've got your conference information entered into the system, the site will display it to your attendees in a variety of ways.  Each content type will have it's own bookmarkable page to display a description and related information.  For example, each speaker page will display a description of the speaker and a list of sessions at which they are speaking.</p>

	<p>Each type of information will have an archive page.  For example, you're site will have a page listing all the rooms for your conference.</p>

	<p>The plugin also creates widgets for use in your site's sidebar.  For example, you'll find a sponsor slideshow widget on <a href="<?php echo admin_url('widgets.php'); ?>">widgets page</a>, ready to drop into your site.</p>

	<h3>Where do I start?</h3>
	<p>Likely you have a lot of data input ahead of you.  You can start anywhere you like, by creating an entry for one of the conference data types.  How about creating a <a href="<?php echo admin_url('post-new.php?post_type=session'); ?>">session</a> now?</p>

	<?php
        // TODO: explain "non-session"  vs. 'no-sessions'
        // TODO: remind designer of template possibilities (like use of featured image)
	?>
</div>