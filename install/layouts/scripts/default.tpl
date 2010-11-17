<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: default.tpl 7396 2010-09-16 00:45:08Z john $
 */
?>
<?php echo $this->doctype()->__toString() ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <base href="<?php echo rtrim('http://' . $_SERVER['HTTP_HOST'] . $this->baseUrl(), '/'). '/' ?>" />

    <?php // TITLE/META ?>
    <?php
      $this->headTitle()
        ->setSeparator(' - ');
      $this->headMeta()
        ->appendHttpEquiv('Content-Type', 'text/html; charset=UTF-8')
        ->appendHttpEquiv('Content-Language', 'en-US');
    ?>
    <?php echo $this->headTitle()->toString()."\n" ?>
    <?php echo $this->headMeta()->toString()."\n" ?>

    <?php // LINK/STYLES ?>
    <?php
      $this->headLink()
        ->prependStylesheet($this->baseUrl() . '/externals/styles/sdk.css')
        ->prependStylesheet($this->baseUrl() . '/externals/styles/styles.css')
        ->prependStylesheet($this->baseUrl() . '/externals/styles/compat.css')
        ;
    ?>
    <?php echo $this->headLink()->toString()."\n" ?>
    <?php echo $this->headStyle()->toString()."\n" ?>

    <?php // SCRIPTS ?>
    <?php
      $appBaseHref = str_replace('install/', '', $this->url(array(), 'default', true));
      $appBaseUrl = rtrim(str_replace('\\', '/', dirname($this->baseUrl())), '/');
      $this->headScript()
        ->prependFile($appBaseUrl . '/externals/smoothbox/smoothbox4.js')
        ->prependFile($appBaseUrl . '/externals/chootools/chootools.js')
        ->prependFile($appBaseUrl . '/externals/mootools/mootools-1.2.4.4-more-' . (APPLICATION_ENV == 'development' ? 'nc' : 'yc') . '.js')
        ->prependFile($appBaseUrl . '/externals/mootools/mootools-1.2.4-core-' . (APPLICATION_ENV == 'development' ? 'nc' : 'yc') . '.js')
        ;
    ?>
    <?php echo $this->headScript()->toString()."\n" ?>
  </head>
  <body>

    <?php if( empty($this->layout()->hideIdentifiers) ): ?>
      <div class='topbar_wrapper'>
        <div class="topbar">
          <div class='topmenu'>
            <p>You are currently signed-in to the package manager, a tool used for
            adding plugins, mods, themes, languages, and other extensions to
            your community.</p>

            <a href="<?php echo $this->url(array(), 'logout') ?>?return=<?php echo urlencode($appBaseHref . 'admin/') ?>" class="buttonlink packages_return">Return to Admin Panel</a>
            
          </div>
          <div class='logo'>
            <img src="externals/images/socialengine_logo_admin.png" alt="" />
          </div>
        </div>
      </div>
      <div class="content tabs_packagemanager">
        <h2>
          Package Manager
        </h2>
        <?php echo $this->render('_managerMenu.tpl') ?>
      </div>
      
    <?php endif; ?>
    
    <div class='content packagemanager'>
      <?php echo $this->layout()->content ?>
    </div>


    <?php if( APPLICATION_ENV == 'development' ): ?>
    <div style="margin-bottom: 40px; text-align: center;">
      <span>
        Peak Memory Usage: <?php echo number_format(memory_get_peak_usage()) ?>
        <br />

        Load time (approx): 
        <?php
          $deltaTime = microtime(true) - _ENGINE_REQUEST_START;
          $hours = floor($deltaTime / 3600);
          $minutes = floor(($deltaTime % 3600) / 60);
          $seconds = floor((($deltaTime % 3600) % 60));
          $milliseconds = floor(($deltaTime - floor($deltaTime)) * 1000);
          if( $hours > 0 ) {
            echo $this->translate(array('%d hour', '%d hours', $hours), $hours);
            echo ", ";
          }
          if( $minutes > 0 ) {
            echo $this->translate(array('%d minute', '%d minutes', $minutes), $minutes);
            echo ", ";
          }
          if( $seconds > 0 ) {
            echo $this->translate(array('%d second', '%d seconds', $seconds), $seconds);
            echo ", ";
          }
          if( $milliseconds > 0 ) {
            echo $this->translate(array('%d millisecond', '%d milliseconds', $milliseconds), $milliseconds);
            echo ", ";
          }
          echo number_format($deltaTime, 3);
          echo ' seconds total';
        ?>
        <br />
      </span>
    </div>
    <?php endif; ?>
  </body>
</html>