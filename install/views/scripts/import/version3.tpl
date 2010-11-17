<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: version3.tpl 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */
?>

<?php if( !empty($this->form) ): ?>
  <?php echo $this->form->render($this) ?>
<?php endif; ?>

<?php if( $this->status ): ?>
  <div>
    <?php if( $this->hasError ): ?>
      <div class="error">
        Some errors occurred in the import of your network. Please contact
        technical support.
      </div>
    <?php elseif( $this->hasWarning ): ?>
      <div class="warning">
        Some minor problems occurred in the import of your network. These may
        not present a problem. Please review the log below for warning messages.
      </div>
    <?php else: ?>
      <div class="success">
        Your import is complete.
      </div>
    <?php endif; ?>
    <?php
      //if( APPLICATION_ENV == 'development' ) {
        echo '<br />';
        echo '<br />';
        echo 'Time taken for import: ';
        $hours = floor($this->importDeltaTime / 3600);
        $minutes = floor(($this->importDeltaTime % 3600) / 60);
        $seconds = floor((($this->importDeltaTime % 3600) % 60));
        echo $this->translate(array('%d hour', '%d hours', $hours), $hours);
        echo ", ";
        echo $this->translate(array('%d minute', '%d minutes', $minutes), $minutes);
        echo ", ";
        echo $this->translate(array('%d second', '%d seconds', $seconds), $seconds);
        echo " (";
        echo number_format($this->importDeltaTime);
        echo ' seconds total';
        echo ") ";
      //}
    ?>
  </div>
  <br />
  
  <ul class="import_log_section">
    <?php
    if( !empty($this->messages) ) {

      $currentClass = null;
      foreach( $this->messages as $message ) {
        if( preg_match('/^(Install_Import_.+?)[:]\s*(.+)$/i', $message, $m) ) {
          $class = $m[1];
          $classMessage = $m[2];
          if( $class != $currentClass ) {
            $parts = explode('_', $class);
            $type = array_pop($parts);
            if( $currentClass ) {
              echo '</ul>';
              echo '</li>';
            }
            echo '<li>';
            echo '<h3>' . trim(preg_replace('/[A-Z]/', ' - $0', trim($type)), " \r\t\n-") . '</h3>';
            echo '<ul class="import_log">';
            $currentClass = $class;
          }
          $message = $classMessage;
        }
        $isWarning = ( stripos($message, 'warning') !== false );
        $isError = ( stripos($message, 'error') !== false );
        ?>
          <li class="<?php echo $isError ? 'error' : ( $isWarning ? 'warning' : 'notice' ); ?>">
            <?php echo $message ?>
          </li>
        <?php
      }

    }
    ?>
  </ul>
<?php endif; ?>