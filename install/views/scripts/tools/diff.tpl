<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: _installMenu.tpl 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */
?>

<div>
  <?php if( !$this->showEverything ): ?>
    <a href="<?php echo $this->escape($this->url(array()) . '?' . http_build_query(array_merge($this->parts, array('show' => '1')))) ?>">
      Show Entire File
    </a>
  <?php else: ?>
    <a href="<?php echo $this->escape($this->url(array()) . '?' . http_build_query(array_merge($this->parts, array('show' => '0')))) ?>">
      Hide Irrelevant Parts
    </a>
  <?php endif; ?>
  &nbsp;
  <?php if( $this->type == 'inline' ): ?>
    <a href="<?php echo $this->escape($this->url(array()) . '?' . http_build_query(array_merge($this->parts, array('type' => 'sidebyside')))) ?>">
      Side-by-side
    </a>
  <?php else: ?>
    <a href="<?php echo $this->escape($this->url(array()) . '?' . http_build_query(array_merge($this->parts, array('type' => 'inline')))) ?>">
      Inline
    </a>
  <?php endif; ?>
</div>
<br />

<table class="package_text_diff">
  <tr class="package_text_diff_title">
    <td colspan="4">
      <?php echo $this->file ?>
    </td>
  </tr>
<?php

// Pre scan?
$i = 0;
$li = 0;
$ri = 0;
$relevant = array();
foreach( $this->textDiff->getDiff() as $textDiffOp ) {
  $operation = strtolower(get_class($textDiffOp));
  $type = str_replace('text_diff_op_', '', $operation);
  $keys = ( $textDiffOp->orig ? array_keys($textDiffOp->orig) : ($textDiffOp->final ? array_keys($textDiffOp->final) : false) );
  if( !$keys ) continue;

  foreach( $keys as $key ) {
    $i++;
    $originalLine = ( is_array($textDiffOp->orig) && isset($textDiffOp->orig[$key]) ? $textDiffOp->orig[$key] : null );
    $finalLine = ( is_array($textDiffOp->final) && isset($textDiffOp->final[$key]) ? $textDiffOp->final[$key] : null );

    $cli = ( $originalLine !== null ? ++$li : '' );
    $cri = ( $finalLine !== null ? ++$ri : '' );
    $delta = '';

    if( $type != 'copy' ) {
      for( $j = max(0, $i - 3); $j <= $i + 3; $j++ ) {
        $relevant[$j] = true;
      }
    }
  }
}

// Generate
$i = 0;
$li = 0;
$ri = 0;
$isIrrelevant = true;
$inIrrelevant = true;
$firstIrrelevant = true;
foreach( $this->textDiff->getDiff() as $textDiffOp ) {
  $operation = strtolower(get_class($textDiffOp));
  $type = str_replace('text_diff_op_', '', $operation);
  $keys = ( $textDiffOp->orig ? array_keys($textDiffOp->orig) : ($textDiffOp->final ? array_keys($textDiffOp->final) : false) );
  if( !$keys ) continue;

  foreach( $keys as $key ) {
    $i++;

    $originalLine = ( is_array($textDiffOp->orig) && isset($textDiffOp->orig[$key]) ? $textDiffOp->orig[$key] : null );
    $finalLine = ( is_array($textDiffOp->final) && isset($textDiffOp->final[$key]) ? $textDiffOp->final[$key] : null );

    $cli = ( $originalLine !== null ? ++$li : '' );
    $cri = ( $finalLine !== null ? ++$ri : '' );
    $delta = '';

    // Check relevant?
    $showSpacing = false;
    if( !$this->showEverything ) {
      $wasInIrrelevant = false;
      if( !isset($relevant[$i]) ) {
        if( !$inIrrelevant ) {
          $inIrrelevant = true;
        }
        continue;
      } else {
        if( $inIrrelevant ) {
          $wasInIrrelevant = true;
        }
        $inIrrelevant = false;
      }
      $showSpacing = false;
      if( $wasInIrrelevant ) {
        if( !$firstIrrelevant ) {
          $showSpacing = true;
        } else {
          $firstIrrelevant = false;
        }
      }
    }

    if( $this->type == 'inline' ) {
      if( $showSpacing ) {
        ?>
        <tr class="text_diff_op_line text_diff_irrelevant">
          <td class="package_text_diff_line">
            ...
          </td>
          <td class="package_text_diff_line">
            ...
          </td>
          <td class="package_text_diff_left">

          </td>
        </tr>
        <?php
      }
    ?>
      <tr class="text_diff_op_line <?php echo $operation ?>">
        <td class="package_text_diff_line">
          <?php if( in_array($type, array('copy', 'delete', 'change')) ) echo htmlspecialchars($cli); ?>
        </td>
        <td class="package_text_diff_line">
          <?php if( in_array($type, array('copy', 'add')) )  echo htmlspecialchars($cri); ?>
        </td>
        <td class="package_text_diff_left">
          <pre style="margin: 0;"><?php echo htmlspecialchars($originalLine) ?></pre>
        </td>
      </tr>
      <?php if( in_array($type, array('change', 'add')) ): ?>
      <tr class="text_diff_op_line <?php echo $operation ?>">
        <td class="package_text_diff_line">
          <?php if( $type == 'copy' ) echo htmlspecialchars($cli); ?>
        </td>
        <td class="package_text_diff_line">
          <?php if( $type != 'copy' ) echo htmlspecialchars($cri); ?>
        </td>
        <td class="package_text_diff_right">
          <pre style="margin: 0;"><?php echo htmlspecialchars($finalLine) ?></pre>
        </td>
      </tr>
      <?php endif; ?>
    <?php
    } else {
      if( $showSpacing ) {
        ?>
        <tr class="text_diff_op_line text_diff_irrelevant">
          <td class="package_text_diff_line">
            ...
          </td>
          <td class="package_text_diff_left">

          </td>
          <td class="package_text_diff_line">
            ...
          </td>
          <td class="package_text_diff_right">

          </td>
        </tr>
        <?php
      }
    ?>
      <tr class="text_diff_op_line <?php echo $operation ?>">
        <td class="package_text_diff_line">
          <?php echo htmlspecialchars($cli); ?>
        </td>
        <td class="package_text_diff_left">
          <pre style="margin: 0;"><?php echo htmlspecialchars($originalLine) ?></pre>
        </td>
        <td class="package_text_diff_line">
          <?php echo htmlspecialchars($cri); ?>
        </td>
        <td class="package_text_diff_right">
          <pre style="margin: 0;"><?php echo htmlspecialchars($finalLine) ?></pre>
        </td>
      </tr>
    <?php
    }
  }
}

?>
</table>



