<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: _formSignupImage.tpl 7371 2010-09-14 03:33:35Z john $
 * @author     Jung
 */
?>
  <?php
    $this->headScript()
      ->appendFile($this->baseUrl().'/externals/moolasso/Lasso.js')
      ->appendFile($this->baseUrl().'/externals/moolasso/Lasso.Crop.js')
  ?>
<div>
  <?php 
    if (isset($_SESSION['TemporaryProfileImg'])){
      echo '<img src="'.$this->baseUrl().'/public/temporary/p_'.$_SESSION['TemporaryProfileImg'].'" alt="" id="lassoImg"/>';
    }
    else
      echo '<img src="application/modules/User/externals/images/nophoto_user_thumb_profile.png" alt="" id="lassoImg" />';
    ?>
</div>
<br/>
  <div id="preview-thumbnail" class="preview-thumbnail">
    <?php 
    if (isset($_SESSION['TemporaryProfileImg'])){
      echo '<img class ="thumb_icon item_photo_user thumb_icon" src="'.$this->baseUrl().'/public/temporary/is_'.$_SESSION['TemporaryProfileImg'].'" alt="Profile Photo" id="previewimage" />  <br />';
    }
    else
      echo
      '<img id="previewimage" class="thumb_icon item_photo_user thumb_icon" src="application/modules/User/externals/images/nophoto_user_thumb_icon.png" alt="Profile Photo" />'
    ?>
  </div>
  <div id="thumbnail-controller" class="thumbnail-controller">
    <?php
      if (isset($_SESSION['TemporaryProfileImg'])){
        echo '<a href="javascript:void(0);" onclick="lassoStart();">'.$this->translate('Edit Thumbnail').'</a>';
      }
    ?>
  </div>
  <script type="text/javascript">
    var loader = new Element('img',{ src:'application/modules/Core/externals/images/loading.gif'});;
    var orginalThumbSrc;
    var originalSize;
    var lassoCrop;

    var lassoSetCoords = function(coords)
    {
      var delta = (coords.w - 48) / coords.w;

      $('coordinates').value =
        coords.x + ':' + coords.y + ':' + coords.w + ':' + coords.h;

      $('previewimage').setStyles({
        top : -( coords.y - (coords.y * delta) ),
        left : -( coords.x - (coords.x * delta) ),
        height : ( originalSize.y - (originalSize.y * delta) ),
        width : ( originalSize.x - (originalSize.x * delta) )
      });
    }
    var myLasso;
    var lassoStart = function()
    {
      if( !orginalThumbSrc ) orginalThumbSrc = $('previewimage').src;
      originalSize = $("lassoImg").getSize();

      //this.style.display = 'none';
      myLasso = new Lasso.Crop('lassoImg',{
  ratio : [1, 1],
  preset : [10,10,58,58],
  min : [48,48],
  handleSize : 8,
  opacity : .6,
  color : '#7389AE',
  border : '<?php echo $this->baseUrl().'/externals/moolasso/crop.gif' ?>',
  onResize : lassoSetCoords
      });


      var sourceImg = $('lassoImg').src;
      $('previewimage').src = $('lassoImg').src;
      $('coordinates').value = 10 + ':' + 10 + ':' + 58+ ':' + 58;

      //$('preview-thumbnail').innerHTML = '<img id="previewimage" alt="cropping test" src="'+sourceImg+'"/>';
      $('thumbnail-controller').innerHTML = '<a href="javascript:void(0);" onclick="lassoEnd();"><?php echo $this->translate('Apply Changes');?></a>';
    }

    var uploadSignupPhoto =function(){
      $('uploadPhoto').value = true;
      $('thumbnail-controller').innerHTML = "<div><img class='loading_icon' src='application/modules/Core/externals/images/loading.gif'/><?php echo $this->translate('Loading...');?></div>";
      $('SignupForm').submit();
      $('Filedata-wrapper').innerHTML = "";
    }
    var lassoEnd = function(){
      $('lassoImg').setStyle('display', 'block');
      $('thumbnail-controller').innerHTML = '<a href="javascript:void(0);" onclick="lassoStart();"><?php echo $this->translate('Edit Thumbnail');?></a>';
      $('lassoMask').destroy();
    }

  </script>
