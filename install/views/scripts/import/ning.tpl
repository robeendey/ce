<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: ning.tpl 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */
?>

<?php if( !empty($this->form) ): ?>
  <?php echo $this->form->render($this) ?>
<?php endif; ?>

<?php if( $this->status ): ?>
  <?php if( !$this->hasError ): ?>
    Your import is complete.
  <?php else: ?>
    Some errors occurred in the import of your network. Please contact
    technical support.
  <?php endif; ?>
  <?php
  if( !empty($this->messages) ) {

    $currentClass = null;
    foreach( $this->messages as $message ) {
      list($class, $pmsg) = explode(':', $message, 2);
      if( $class != $currentClass ) {
        $parts = explode('_', $class);
        $type = array_pop($parts);
        echo '</li>';
        echo '<li>';
        echo '<h3>' . trim($type) . '</h3>';
        $currentClass = $class;
      }
      $isError = ( stripos($message, 'error') !== false );
      // Strip class from message
      //preg_replace('//',

      ?>
  <li class="<?php echo $isError ? 'error' : 'notice'; ?>">
    <?php echo $message ?>
  </li>
      <?php
    }

  }
  ?>
<?php endif; ?>