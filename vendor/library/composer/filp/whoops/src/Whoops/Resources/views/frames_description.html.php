<div class="frames-description <?php echo $has_frames_tabs ? 'frames-description-application' : '' ?>">
  <?php if ($has_frames_tabs): ?>
    <?php if ($active_frames_tab == 'application'): ?>
      <a href="#" id="application-frames-tab" class="frames-tab frames-tab-active">
          应用程序框架 (<?php echo $frames->countIsApplication() ?>)
      </a>
    <?php else: ?>
      <span href="#" id="application-frames-tab" class="frames-tab">
        应用程序框架 (<?php echo $frames->countIsApplication() ?>)
      </span>
    <?php endif; ?>
    <a href="#" id="all-frames-tab" class="frames-tab <?php echo $active_frames_tab == 'all' ? 'frames-tab-active' : '' ?>">
      全部 frames (<?php echo count($frames) ?>)
    </a>
  <?php else: ?>
    <span>
        Stack frames (<?php echo count($frames) ?>)
    </span>
  <?php endif; ?>
</div>