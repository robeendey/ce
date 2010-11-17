<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: db-sanity.tpl 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */
?>

<h1>
  <?php echo $this->translate('Step 3: Setup MySQL Database') ?>
</h1>

<p>
  <?php echo $this->translate('We\'ve successfully connected to the database. Now, let\'s make sure your MySQL server has everything it needs to support SocialEngine.') ?>
</p>

<br />

<div class="sanity_wrapper">
  <div>
    <ul class='sanity'>
      <?php foreach( $this->test->getTests() as $test ): ?>
        <li>
          <div>
            <?php echo $test->getName() ?>
          </div>
          <?php if( !$test->hasMessages() ): ?>
            <div class='sanity-ok'>
              <?php echo $test->getEmptyMessage(); ?>
          </div>
          <?php else: ?>
            <?php
              $errLevel = $test->getMaxErrorLevel();
              $errClass = ( $errLevel & 4 ? 'sanity-error' : ($errLevel & 3 ? 'sanity-notice' : 'sanity-ok' ));
            ?>
            <div class='<?php echo $errClass ?>'>
              <?php foreach( $test->getMessages() as $message ): ?>
                <?php echo $message->toString() ?> <br />
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
</div>

<br />

<div>
  <?php if( $this->maxErrorLevel >= 4 ): ?>

    <button onclick="window.location.replace(window.location.href);">
      <?php echo $this->translate('Try Again') ?>
    </button>

  <?php else: ?>

    <button onclick="window.location.href = '<?php echo $this->url(array('action' => 'db-create'), '', true) ?>';">
      <?php echo $this->translate('Continue...') ?>
    </button>

    <button onclick="window.location.href = '<?php echo $this->url(array('action' => 'db-info'), '', true) ?>?clear=1';">
      <?php echo $this->translate('Re-enter MySQL Info') ?>
    </button>

  <?php endif; ?>
</div>
