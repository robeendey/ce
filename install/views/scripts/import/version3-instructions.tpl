
<h2>SocialEngine 3 Import Instructions</h2>

<p>
  This SocialEngine 3 Import tool is designed to migrate content directly from a
  SocialEngine 3 installation. It is intended to be used on a fresh
  install of SocialEngine 4; it will remove any existing content on the network.
</p>

<br />


<?php if( !empty($this->dbHasContent) ): ?>
  <div class="warning">
    Your site already has content. The content will be removed if you use this
    import tool.
  </div>
  <br />
  <br />
<?php endif; ?>


<ul>
  <li>
    The following types of data
    <b style="font-weight: bold;">will be removed or overwritten from the existing <em>version 4</em> installation</b>:
    <ul style="margin-left: 20px;margin-bottom: 10px;padding-top: 4px;list-style: circle;">
      <li>
        All admin and user accounts.
      </li>
      <li>
        All user content.
      </li>
      <li>
        All announcements.
      </li>
      <li>
        All admin created categoried (i.e. blog categories, video categories, etc)
      </li>
    </ul>
  </li>
  <li>
    The following types of data
    <b style="font-weight: bold;">will not be removed or overwritten from the existing <em>version 4</em> installation</b>:
    <ul style="margin-left: 20px;margin-bottom: 10px;padding-top: 4px;list-style: circle;">
      <li>
        Any installed plugins, themes, widgets, or language packs.
      </li>
      <li>
        User levels.
      </li>
      <li>
        Files uploaded in the admin panel media manager.
      </li>
      <li>
        Custom pages or changes to existing pages made in the layout editor.
      </li>
    </ul>
  </li>
  <li>
    The following types or data
    <b style="font-weight: bold;">may be removed or overwritten from the existing <em>version 4</em> installation</b>:
    <ul style="margin-left: 20px;margin-bottom: 10px;padding-top: 4px;list-style: circle;">
      <li>
        Global settings
      </li>
      <li>
        Level settings
      </li>
    </ul>
  </li>
</ul>

<br />



<button type="button" style="margin:10px 0px" id="continue" name="continue"
        onclick="window.location.href='<?php echo $this->url(array('action' => 'version3')) ?>';return false;">
  Start Import
</button>
