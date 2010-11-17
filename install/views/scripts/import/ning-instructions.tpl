
<h2>Ning Import Instructions</h2>

<p>
  This Ning Import tool is designed to migrate content provided
  by Ning's Archive Tool into SocialEngine.  It is intended to be used on a fresh
  install of SocialEngine4.
</p>

<br />
<br />

<?php if( !empty($this->dbHasContent) ): ?>
  <div class="warning">
    Your site already has content. It is not possible to import to a site with existing content.
  </div>
  <br />
  <br />
<?php return; endif; ?>

<ol style="margin-left:20px">
  <li>
    Install SocialEngine. Do not create content or post anything yet.
  </li>
  <li>
    Export your network's data to your computer using the
    <a href="http://help.ning.com/cgi-bin/ning.cfg/php/enduser/std_adp.php?p_faqid=3796">Ning Archive Tool</a>.
    Be sure to download everything it gives an option to download.
  </li>
  <li>
    In the export folder, you should have some of these files and folders:
    <blockquote style="white-space: pre;">
        ning-members.json, ning-members-local.json,
        ning-blogs.json, ning-blogs-local.json,
        ning-discussions.json, ning-discussions-local.json,
        ning-events.json, ning-events-local.json,
        ning-groups.json, ning-groups-local.json,
        ning-music.json, ning-music-local.json,
        ning-notes.json, ning-notes-local.json,
        ning-photos.json, ning-photos-local.json,
        ning-videos.json, ning-videos-local.json,
        Directories: blogs/, discussions/, events/, groups/, members/, music/, notes/, photos/, videos/
    </blockquote>
    At the very least, you need ning-members.json,
    however the import utility will work with whatever is available.
  </li>
  <li>
    Log into your site using FTP and access your root SocialEngine directory (it
    appears to be "<?php echo APPLICATION_PATH ?>").  This is where the application/,
    externals/, public/ and temporary/ folders exist.
    Upload all of the JSON files and the folders of images and site content to
    the root SocialEngine folder.
  </li>
  <li>
    Use our Ning Import tool to import all your existing Ning members, content,
    and data.<br />
    <button type="button" style="margin:10px 0px" id="continue" name="continue"
            onclick="window.location.href='<?php echo $this->url(array('action' => 'ning')) ?>';return false;">
      Start Import
    </button>
  </li>
  <li>
    After the import tool completes, delete all of the JSON files from your
    SocialEngine root directory that you previously uploaded (do NOT delete the
    directories you uploaded).  Your Ning site is now imported, and your
    SocialEngine4 site is now ready to use!
  </li>
</ol>

<br />

