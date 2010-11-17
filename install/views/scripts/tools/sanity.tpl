<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: index.tpl 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */
?>

<h3>
  System Requirements Check
</h3>

<div class='sanity_wrapper'>
  <div>
    <?php foreach( $this->tests->getTests() as $battery ): ?>
      <h3>
        <?php echo $battery->getName() ?>
        <?php //echo $this->packageIndex[$battery->getName()]->getKey() ?>
      </h3>
      <ul class='sanity'>
        <?php foreach( $battery->getTests() as $test ): ?>
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
                  <?php echo $message->toString()  ?> <br />
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endforeach; ?>
  </div>
</div>

<br />
<br />


<h3>
  Dependency Check
</h3>

<div class='sanity_wrapper'>
  <div>
    <?php foreach( $this->dependencies as $guid => $dependencies ): ?>
      <h3>
        <?php echo $dependencies->getPackageKey() ?>
        <?php //echo $this->packageIndex[$battery->getName()]->getKey() ?>
      </h3>
      <ul class='sanity'>
        <?php foreach( $dependencies->getDependencies() as $dependency ): ?>
          <li>
            <div>
              <?php echo $dependency->getGuid() ?>
            </div>
            <?php
              $errClass = ( $dependency->getStatus() <= 0 ? 'sanity-ok' : ( $dependency->getRequired() ? 'sanity-error' : 'sanity-notice' ) );
            ?>
            <div class='<?php echo $errClass ?>'>
              <?php echo $dependency->getRequired() ? 'Requires' : 'Recommends' ?> that
              "<?php echo $dependency->getGuid() ?>"
              <?php
                if( $dependency->getMinVersion() && $dependency->getMaxVersion() ) {
                  echo '(between ' . $dependency->getMinVersion() . ' and ' . $dependency->getMaxVersion(). ')';
                } else if( $dependency->getMinVersion() ) {
                  echo '(at least ' . $dependency->getMinVersion() . ')';
                } else if( $dependency->getMaxVersion() ) {
                  echo '(no greater than ' . $dependency->getMaxVersion(). ')';
                }
              ?>
              be installed.
              <span>
                <?php
                  switch( $dependency->getStatus() ) {
                    case 0:
                      //echo 'OK';
                      break;
                    case 1:
                      echo 'Please upgrade.';
                      break;
                    case 2:
                      echo 'Please contact the developer about a compatible version, or disable.';
                      break;
                    case 3:
                      echo 'Please install.';
                      break;
                  }
                ?>
              </span>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endforeach; ?>
  </div>
</div>

<br />
<br />

