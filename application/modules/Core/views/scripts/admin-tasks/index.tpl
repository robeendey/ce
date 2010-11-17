<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: index.tpl 7370 2010-09-14 03:28:50Z john $
 * @author     John
 */
?>

<h2><?php echo $this->translate("Task Scheduler") ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<p>
  <?php echo (
    'CORE_VIEWS_SCRIPTS_ADMINTASKS_INDEX_DESCRIPTION' !== ($desc = $this->translate("CORE_VIEWS_SCRIPTS_ADMINTASKS_INDEX_DESCRIPTION")) ?
    $desc : '' ) ?>
</p>

<br />


<div class='admin_search'>
  <?php echo $this->formFilter->render($this) ?>
</div>

<br />


<script type="text/javascript">
  // Auto refresh
  var doAutoRefresh = false;
  var tips;
  window.addEvent('load', function() {
    doAutoRefresh = ( Cookie.read('en4_admin_tasks_autorefresh') == '1' ? true : false );
    (new Element('a', {
      'href' : 'javascript:void(0);',
      'html' : ( doAutoRefresh ? '<?php echo $this->translate('Disable Auto-Refresh') ?>' : '<?php echo $this->translate('Enable Auto-Refresh') ?>' ),
      'events' : {
        'click' : function() {
          if( doAutoRefresh ) {
            this.set('html', '<?php echo $this->translate('Enable Auto-Refresh') ?>');
            doAutoRefresh = false;
          } else {
            this.set('html', '<?php echo $this->translate('Disable Auto-Refresh') ?>');
            doAutoRefresh = true;
          }
          Cookie.write('en4_admin_tasks_autorefresh', (doAutoRefresh ? '1' : '0'));
        }
      }
    })).inject($('autorefresh-select').empty());

    tips = new Tips($$('.tips'), {
      title : function(el) {
        return el.get('html').trim();
      },
      text : function(el) {
        return el.get('title').trim();
      }
    });
  });

  // Order
  var handleSort = function(order) {
    if( $('order').get('value') != order ) {
      $('order').set('value', order);
      $('direction').set('value', 'ASC');
    } else {
      $('direction').set('value', ($('direction').get('value') == 'ASC' ? 'DESC' : 'ASC'));
    }
    $('filter_form').submit();
  }

  // Selected
  var handleSelectedAction = function(action) {
    
    // Check action
    var url;
    switch( action ) {
      case 'run':
        url = '<?php echo $this->url(array('action' => 'run')) ?>';
        break;
      case 'reset':
        url = '<?php echo $this->url(array('action' => 'reset')) ?>';
        break;
      case 'unlock':
        url = '<?php echo $this->url(array('action' => 'unlock')) ?>';
        break;
      default:
        return;
        break;
    }

    // Check selection
    if( action != 'unlock' && $$('#admin-tasks-form input[type="checkbox"][checked]').length <= 0 ) {
      return;
    }
    
    // Submit
    url += '?return=' + encodeURI(window.location.href);
    $('admin-tasks-form').set('action', url);
    $('admin-tasks-form').submit();
  }

  // 
  // Counter
  var now = parseInt('<?php echo sprintf('%d', time()) ?>');
  var lastRun = parseInt('<?php echo sprintf('%d', $this->taskSettings['last']) ?>');
  var interval = parseInt('<?php echo sprintf('%d', $this->taskSettings['interval']) ?>');
  var timeout = parseInt('<?php echo sprintf('%d', $this->taskSettings['timeout']) ?>');
  var counter = 0;
  var refreshing = false;
  var checkInterval = (function(){
    counter++;
    var sortOfNow = now + counter;
    var delta = sortOfNow - lastRun;
    if( delta > interval * 2 ) {
      //$clear(checkInterval);
      if( doAutoRefresh && !refreshing && counter > interval ) {
        refreshing = true;
        window.location.replace( window.location.href );
      }
    } else if( delta > interval ) {
      if( $('task_counter_container') ) {
        $('task_counter_container').set('html', '<?php echo $this->translate('Tasks are ready to be run again.') ?>');
      }
      // Auto refresh?
      if( doAutoRefresh && !refreshing && sortOfNow > 10 ) {
        refreshing = true;
        window.location.replace( window.location.href );
      }
    } else {
      if( $('task_counter') ) {
        $('task_counter').set('html', interval - delta);
      }
    }
  }).periodical(1000);
</script>

<div>
  <?php if( time() - $this->taskSettings['last'] > $this->taskSettings['interval'] * 3 ): ?>
    <ul class="form-errors">
      <li>
        <?php echo $this->translate('Tasks have not executed for more than %1$d seconds. Please check your configuration.', $this->taskSettings['interval'] * 3) ?>
      </li>
    </ul>
  <?php endif; ?>

  <?php echo $this->translate('Tasks are checked every %1$s seconds.', $this->taskSettings['interval']) ?>
  <br />
  
  <?php echo $this->translate('Tasks are considered to have timed out after %1$d seconds.', $this->taskSettings['timeout']) ?>
  <br />

  <span>
    <span id="task_counter_container">
      <?php
        $next = ($this->taskSettings['last'] + $this->taskSettings['interval']) - time();
        if( $next <= 0 ):
      ?>
        <?php echo $this->translate('Tasks are ready to be run again.') ?>
      <?php else: ?>
        <?php echo $this->translate('Tasks can be run again in %1$s seconds.',
            '<span id="task_counter">' . (($this->taskSettings['last'] + $this->taskSettings['interval']) - time()) . '</span>'
        ) ?>
      <?php endif; ?>
    </span>
    <span id="autorefresh-select">
    </span>
  </span>
  <br />

  <?php if( !empty($this->taskSettings['pid']) ): ?>
    <em>
      <?php echo $this->translate('Tasks are currently being executed.') ?>
    </em>
    <br />
  <?php endif; ?>

  <?php echo $this->translate('%1$d tasks are currently running.', $this->currentlyExecutingCount) ?>
  <br />
</div>
<br />


<?php /*
<div>
  <?php echo $this->htmlLink(array('action' => 'settings', 'reset' => false), $this->translate('Change Settings'), array(
    'class' => 'buttonlink',
    'style' => 'background-image: url(' . rtrim($this->baseUrl(), '/') . '/application/modules/Core/externals/images/admin/links_layout.png);'
  )) ?>
</div>
<br />
 *
 */ ?>



<?php if( $this->tasks->count() > 1 ): ?>
  <?php echo $this->paginationControl($this->tasks) ?>
  <br />
<?php endif; ?>


<form id="admin-tasks-form" method="post" action="<?php echo $this->url() ?>">

  <table class="admin_table">
    <thead>
      <tr>
        <th>
          <input type="checkbox" onclick="$$('input[type=checkbox][name]').set('checked', this.get('checked'));" />
        </th>
        <th>
          <a href="javascript:void(0)" onclick="handleSort('task_id')">
            <?php echo $this->translate('ID') ?>
          </a>
        </th>
        <th>
          <a href="javascript:void(0)" onclick="handleSort('title')">
            <?php echo $this->translate('Name') ?>
          </a>
        </th>
        <?php if( empty($this->formFilterValues['category']) ): ?>
        <th>
          <a href="javascript:void(0)" onclick="handleSort('category')">
            <?php echo $this->translate('Category') ?>
          </a>
        </th>
        <?php endif; ?>
        <?php if( empty($this->formFilterValues['type']) ): ?>
        <th>
          <a href="javascript:void(0)" onclick="handleSort('type')">
            <?php echo $this->translate('Type') ?>
          </a>
        </th>
        <?php endif; ?>
        <?php if( empty($this->formFilterValues['state']) ): ?>
        <th>
          <a href="javascript:void(0)" onclick="handleSort('state')">
            <?php echo $this->translate('State') ?>
          </a>
        </th>
        <?php endif; ?>
        <th>
          <a href="javascript:void(0)" onclick="handleSort('timeout')">
            <?php echo $this->translate('Timeout') ?>
          </a>
        </th>
        <th>
          <?php echo $this->translate('Stats') ?>
        </th>
        <?php /*
        <th>
          <?php echo $this->translate('Options') ?>
        </th>
         *
         */ ?>
      </tr>
    </thead>
    <tbody>
      <?php foreach( $this->tasks as $task ):
        $class = 'admin_task_' . $task->type;
        $class .= ' admin_task_' . $task->state;
        if( !$task->enabled ) {
          $class .= ' admin_task_disabled';
        } else {
          $class .= ' admin_task_enabled';
        }
        if( $task->system ) {
          $class .= ' admin_task_system';
        }
        $class = trim($class);
        ?>
        <tr class="<?php echo $class ?>">
          <td>
            <input type="checkbox" name="selection[]" value="<?php echo $task->task_id ?>" />
          </td>
          <td>
            <?php echo $this->locale()->toNumber($task->task_id) ?>
          </td>
          <td>
            <?php if( !empty($task->title) ): ?>
              <?php echo $task->title ?>
            <?php else: ?>
              <?php echo $task->plugin ?>
            <?php endif; ?>

            <?php if( !empty($this->taskProgress[$task->plugin]) ): ?>
              <br />
              <?php // Percent mode ?>
              <?php if( !empty($this->taskProgress[$task->plugin]['progress']) && !empty($this->taskProgress[$task->plugin]['total'])  ): ?>
                <i>
                <?php echo $this->translate(
                  '%1$s' . '%% complete',
                  $this->locale()->toNumber(round((int) @$this->taskProgress[$task->plugin]['progress'] / $this->taskProgress[$task->plugin]['total'] * 100, 1))
                ) ?>
                <br />
                <?php echo $this->translate(
                  '%1$s of %2$s',
                  $this->locale()->toNumber((int) @$this->taskProgress[$task->plugin]['progress']),
                  $this->locale()->toNumber($this->taskProgress[$task->plugin]['total'])
                ) ?>
                </i>
              <?php // Queue mode ?>
              <?php elseif( !empty($this->taskProgress[$task->plugin]['total']) ): ?>
                <i>
                <?php echo $this->translate(
                  '%1$s in queue',
                  $this->locale()->toNumber($this->taskProgress[$task->plugin]['total'])
                ) ?>
                </i>
              <?php // Progress mode ?>
              <?php elseif( !empty($this->taskProgress[$task->plugin]['progress']) ): ?>
                <i>
                <?php echo $this->translate(
                  '%1$s processed',
                  $this->locale()->toNumber($this->taskProgress[$task->plugin]['total'])
                ) ?>
                </i>
              <?php endif; ?>
            <?php endif; ?>
          </td>
          <?php if( empty($this->formFilterValues['category']) ): ?>
            <td>
              <?php echo $this->translate(ucwords(str_replace('_', ' ', $task->category))) ?>
            </td>
          <?php endif; ?>
          <?php if( empty($this->formFilterValues['type']) ): ?>
          <td>
            <?php
              $title = null;
              switch( $task->type ) {
                case 'disabled':        $title = $this->translate("This task is disabled. It will not be run."); break;
                case 'manual':          $title = $this->translate("This task is only run when you select it and click \"Run Selected Now\" below."); break;
                case 'semi-automatic':  $title = $this->translate("This task is only run when you select it and click \"Run Selected Now\" below. It may go to sleep while executing to prevent timeout problems."); break;
                case 'automatic':       $title = $this->translate("This task is run automatically every time it's timeout expires."); break;
              }
            ?>
            <div class="tips admin_tasks_tip_type_<?php echo $task->type ?>" title="<?php echo $title ?>">
              <?php echo $this->translate(ucfirst($task->type)) ?>
            </div>
          </td>
          <?php endif; ?>
          <?php if( empty($this->formFilterValues['state']) ): ?>
          <td>
            <?php
              $title = null;
              switch( $task->state ) {
                case 'dormant':   $title = $this->translate("This task in inactive. If the type is automatic, it is waiting for it's timeout to expire."); break;
                case 'ready':     $title = $this->translate("This task is ready to be run again, and will be executed shortly."); break;
                case 'sleeping':  $title = $this->translate("This task is taking a quick nap to prevent timeout issues."); break;
                case 'active':    $title = $this->translate("This task is currently running."); break;
              }
            ?>
            <div class="tips admin_tasks_tip_state_<?php echo $task->state ?>" title="<?php echo $title ?>">
              <?php echo $this->translate(ucfirst($task->state)) ?>
            </div>
          </td>
          <?php endif; ?>
          <td>
            <?php if( $task->type == 'disabled' || $task->type == 'manual' ): ?>
              -
            <?php else: ?>
              <?php echo $this->translate(array('%1$s second', '%1$s seconds', $task->timeout), $this->locale()->toNumber($task->timeout)) ?>
            <?php endif; ?>
          </td>
          <td>
            Succeeded:
            <?php if( $task->success_count > 0 ): ?>
              <?php echo $this->locale()->toNumber($task->success_count) ?>
              times, last
              <?php echo $this->timestamp($task->success_last) ?>
            <?php else: ?>
              never
            <?php endif; ?>
            <br />

            Failed:
            <?php if( $task->failure_count > 0 ): ?>
              <?php echo $this->locale()->toNumber($task->failure_count) ?>
              times, last
              <?php echo $this->timestamp($task->failure_last) ?>
            <?php else: ?>
              never
            <?php endif; ?>
            <br />

            <?php if( $task->started_count != $task->success_count + $task->failure_count ): ?>
              <?php if( $task->started_count > 0 ): ?>
              Started:
                <?php echo $this->locale()->toNumber($task->started_count) ?>
                times, last
                <?php echo $this->timestamp($task->started_last) ?>
              <?php else: ?>
                never
              <?php endif; ?>
              <br />
            <?php endif; ?>

            <?php if( $task->completed_count != $task->success_count + $task->failure_count ): ?>
              Completed:
              <?php if( $task->completed_count > 0 ): ?>
                <?php echo $this->locale()->toNumber($task->completed_count) ?>
                times, last
                <?php echo $this->timestamp($task->completed_last) ?>
              <?php else: ?>
                never
              <?php endif; ?>
              <br />
            <?php endif; ?>

            <?php if( $task->executing ): ?>
              Running since:
              <?php echo $this->timestamp($task->started_last) ?>
            <?php else: ?>
              Duration:
              <?php if( $task->completed_last == $task->started_last ): ?>
                less than a second
              <?php else: ?>
                <?php echo ($task->completed_last - $task->started_last) . ' seconds' ?>
              <?php endif; ?>
            <?php endif;?>
          </td>
          <?php /*
          <td class="admin_table_options">
            <?php if( !$task->system ): ?>
              <?php if( $task->enabled ): ?>
                <span class="sep">|</span>
                <?php echo $this->htmlLink(array('reset' => false, 'action' => 'disable', 'task_id' => $task->task_id), $this->translate('disable')) ?>
              <?php else: ?>
                <span class="sep">|</span>
                <?php echo $this->htmlLink(array('reset' => false, 'action' => 'enable', 'task_id' => $task->task_id), $this->translate('enable')) ?>
              <?php endif; ?>
            <?php endif; ?>
            <span class="sep">|</span>
            <?php echo $this->htmlLink('javascript:void(0);', $this->translate('run'), array('onclick' => 'runTasks(' . $task->task_id . ', $(this));')) ?>

            <span class="sep">|</span>
            <?php echo $this->htmlLink(array('reset' => false, 'action' => 'edit', 'task_id' => $task->task_id), $this->translate('edit')) ?>
            <span class="sep">|</span>
            <?php echo $this->htmlLink(array('reset' => false, 'action' => 'reset-stats', 'task_id' => $task->task_id), $this->translate('reset stats')) ?>
             
          </td>
           *
           */ ?>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <br />
  
  <div>
    <button onclick="handleSelectedAction('run'); return false;">Run Selected Now</button>
    <button onclick="handleSelectedAction('reset'); return false;">Reset Statistics</button>
    <!--
    <button onclick="handleSelectedAction('unlock'); return false;">Reset Locks</button>
    -->
    
    <!--
    <button>Disable Selected</button>
    <button>Enable Selected</button>
    -->
  </div>

</form>