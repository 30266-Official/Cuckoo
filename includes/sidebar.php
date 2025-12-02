<?php

/**
 * .______    __    __       ___        ______
 * |   _  \  |  |  |  |     /   \      /  __  \
 * |  |_)  | |  |__|  |    /  ^  \    |  |  |  |
 * |   _  <  |   __   |   /  /_\  \   |  |  |  |
 * |  |_)  | |  |  |  |  /  _____  \  |  `--'  |
 * |______/  |__|  |__| /__/     \__\  \______/
 *
 * Sidebar
 *
 * @author Bhao
 * @link https://dwd.moe/
 * @date 2023-08-02
 */

if(!defined('__TYPECHO_ROOT_DIR__'))
  exit;
$endtime_display = "<span id='endtime'>:D 获取中...</span>";
$tooltip_attr = '';
$data_has_attr = '';
$endtime_file = dirname(__DIR__) . '/endtime.json';
if (file_exists($endtime_file)) {
  $json = @file_get_contents($endtime_file);
  $data = @json_decode($json, true);
  if (json_last_error() === JSON_ERROR_NONE && $data) {
    if (is_array($data)) {
      if (isset($data['endtime'])) {
        $val = $data['endtime'];
      } elseif (isset($data['date'])) {
        $val = $data['date'];
      } else {
        $first = reset($data);
        $val = is_scalar($first) ? $first : '';
      }
      if (isset($data['updatatime'])) {
        $upval = $data['updatatime'];
      } else {
        $upval = null;
      }
    } else {
      $val = $data;
      $upval = null;
    }
    if ($val !== null && $val !== '') {
      $formatted = '';
      if (is_numeric($val) || (is_string($val) && ctype_digit($val))) {
        $ts = intval($val);
        $formatted = date('y年m月d日', $ts);
      } else {
        $ts = strtotime($val);
        if ($ts !== false) {
          $formatted = date('y年m月d日', $ts);
        } else {
          $formatted = htmlspecialchars($val);
        }
      }
        $formatted_up = '';
        if ($upval !== null && $upval !== '') {
          if (is_numeric($upval) || (is_string($upval) && ctype_digit($upval))) {
            $uts = intval($upval);
            $formatted_up = date('y年m月d日', $uts);
          } else {
            $uts = strtotime($upval);
            if ($uts !== false) {
              $formatted_up = date('y年m月d日', $uts);
            } else {
              $formatted_up = htmlspecialchars($upval);
            }
          }
        }
        $tooltip_attr = '';
        if ($formatted_up !== '') {
          $content = "更新时间：" . $formatted_up;
          $safe_content = str_replace("'", "\\'", $content);
          $tooltip_attr = " mdui-tooltip=\"{content: '" . $safe_content . "', position: 'bottom'}\"";
          $endtime_update = "<div class='endtime-update'>" . htmlspecialchars($content) . "</div>";
          $data_has_attr = " data-has-update=\"1\"";
        } else {
          $tooltip_attr = '';
          $endtime_update = '';
          $data_has_attr = '';
        }
        $endtime_display = "<span id='endtime'>" . $formatted . "</span>";
    }
  }
}
?>
<div class="mdui-col-md-4">
  <div class="mdui-card mdui-hoverable sidebar-info">
    <div class="sidebar-info-img">
      <div class="sidebar-info-bg"
           style="background-image: url('<?php setting("sidebarBg", "images/sidebar.jpg"); ?>')"></div>
      <div class="mdui-img-circle mdui-shadow-3"
           style="background-image: url('<?php setting("logoUrl", "images/logo.jpg"); ?>')"></div>
    </div>
    <div class="sidebar-info-body">
      <div class="sidebar-info-name"><?php echo Helper::options()->title ?></div>
      <div class="sidebar-info-desc"><?php setting("describe", "<span id='hitokoto'>:D 获取中...</span>", 1); ?></div>
      <div class="sidebar-info-desc"<?php echo $tooltip_attr . $data_has_attr; ?>>服务器下次到期时间：<?php echo $endtime_display; ?><?php echo isset($endtime_update) ? $endtime_update : ''; ?></div>
    </div>
  </div>
  <?php if ($this->options->showComments) { ?>
  <div class="mdui-card mdui-hoverable sidebar-module">
    <ul class="mdui-list">
      <div class="sidebar-module-title">最新回复</div>
      <li class="mdui-divider mdui-m-y-0"></li>
      <?php $this -> widget('Widget_Comments_Recent') -> to($comments); ?>
      <?php while($comments -> next()) : ?>
        <a href="<?php $comments -> permalink(); ?>">
          <li class="mdui-list-item mdui-ripple sidebar-module-list">
            <div class="sidebar-reply-text"><?php $comments -> author(false); ?>
              : <?php $comments -> excerpt(); ?></div>
          </li>
          <li class="mdui-divider"></li>
        </a>
      <?php endwhile; ?>
    </ul>
  </div>
  <?php } if ($this->options->tagCloud != "0") { ?>
  <div class="mdui-card mdui-hoverable sidebar-module">
    <ul class="mdui-list">
      <div class="sidebar-module-title">标签云</div>
      <li class="mdui-divider mdui-m-y-0"></li>
      <div class="sidebar-tag">
        <?php $this->widget('Widget_Metas_Tag_Cloud', 'sort=mid&desc=0&limit='.$this->options->tagCloud)->to($tags); ?>
        <?php if ($tags->have()) : ?>
          <?php while ($tags->next()) : ?>
            <a href="<?php $tags->permalink(); ?>">
              <div class="mdui-chip">
                <span class="mdui-chip-title"><?php $tags->name(); ?></span>
              </div>
            </a>
          <?php endwhile; ?>
        <?php else : ?>
          <li><?php _e('没有任何标签'); ?></li>
        <?php endif; ?>
      </div>
    </ul>
  </div>
  <?php } if (array_key_exists("Links", Typecho_Plugin::export()['activated']) && $this->options->linksIndexNum != "0") { ?>
  <div class="mdui-card mdui-hoverable sidebar-module">
    <ul class="mdui-list">
      <div class="sidebar-module-title">友情链接</div>
      <li class="mdui-divider mdui-m-y-0"></li>
      <div class='mdui-row-xs-2'>
        <?php Links(1); ?>
      </div>
    </ul>
  </div>
  <?php } ?>
  <?php if ($this->is('single') && $this->fields->catalog == "true" && !$this->hidden) { ?>
  <div class="mdui-card mdui-hoverable sidebar-module" id="toc">
    <ul class="mdui-list">
      <div class="sidebar-module-title">文章目录</div>
      <li class="mdui-divider"></li>
      <div class="toc"></div>
    </ul>
  </div>
  <?php } ?>
</div>
<script>
(function(){
  var css = '\n.sidebar-info-desc .endtime-update{display:none;font-size:12px;color:rgba(0,0,0,.6);margin-top:4px;}\n@media (max-width:600px){\n  .sidebar-info-desc .endtime-update{display:block;}\n  /* 在窄屏上隐藏 MDUI tooltip（如果仍被创建）*/\n  .mdui-tooltip{display:none !important;}\n}\n' +
    '.mdui-tooltip{background:#fff !important;color:#222 !important;border:1px solid #eee !important;box-shadow:0 2px 8px rgba(0,0,0,.08) !important;}\n' +
    '.mdui-theme-layout-dark .mdui-tooltip, body.mdui-theme-layout-dark .mdui-tooltip{background:#222 !important;color:#eee !important;border:1px solid #444 !important;box-shadow:0 2px 8px rgba(0,0,0,.32) !important;}';
  var style = document.createElement('style');
  style.type = 'text/css';
  style.id = 'tooltip-theme-style';
  if (style.styleSheet) style.styleSheet.cssText = css; else style.appendChild(document.createTextNode(css));
  var head = document.getElementsByTagName('head')[0];
  var old = document.getElementById('tooltip-theme-style');
  if (old) old.parentNode.removeChild(old);
  head.appendChild(style);
  var html = document.documentElement;
  if (window.MutationObserver) {
    var observer = new MutationObserver(function(mutations) {
      var shouldReplace = false;
      mutations.forEach(function(mutation) {
        if (mutation.attributeName === 'class') shouldReplace = true;
      });
      if (shouldReplace) {
        var old = document.getElementById('tooltip-theme-style');
        if (old) old.parentNode.removeChild(old);
        var style2 = document.createElement('style');
        style2.type = 'text/css';
        style2.id = 'tooltip-theme-style';
        if (style2.styleSheet) style2.styleSheet.cssText = css; else style2.appendChild(document.createTextNode(css));
        document.getElementsByTagName('head')[0].appendChild(style2);
      }
    });
    observer.observe(html, { attributes: true });
    try { observer.observe(document.body, { attributes: true }); } catch(e) { /* ignore */ }
  }
})();
(function(){
  if (window && window.innerWidth <= 600) {
    var els = document.querySelectorAll('[mdui-tooltip][data-has-update]');
    for (var i = 0; i < els.length; i++) {
      els[i].removeAttribute('mdui-tooltip');
    }
  }
})();
(function(){
  function pad2(n){ return n < 10 ? '0' + n : String(n); }
  var el = document.getElementById('endtime');
  if (!el) return;
  fetch('/endtime.json', {cache: 'no-cache'}).then(function(res){
    if (!res.ok) throw new Error('network');
    return res.json();
  }).then(function(data){
    var val;
    if (data === null) return;
    if (typeof data === 'object') {
      if (data.endtime !== undefined) val = data.endtime;
      else if (data.date !== undefined) val = data.date;
      else {
        for (var k in data) { if (Object.prototype.hasOwnProperty.call(data, k)) { val = data[k]; break; } }
      }
    } else {
      val = data;
    }
    if (val === undefined || val === null || val === '') return;
    var out = '';
    if (!isNaN(val) && val !== '') {
      var ts = Number(val);
      if (ts < 1e12) ts = ts * 1000;
      var d = new Date(ts);
      out = pad2(d.getFullYear() % 100) + '年' + pad2(d.getMonth() + 1) + '月' + pad2(d.getDate()) + '日';
    } else {
      var parsed = Date.parse(String(val));
      if (!isNaN(parsed)) {
        var d = new Date(parsed);
        out = pad2(d.getFullYear() % 100) + '年' + pad2(d.getMonth() + 1) + '月' + pad2(d.getDate()) + '日';
      } else {
        out = String(val);
      }
    }
    el.textContent = out;
    var upval;
    if (typeof data === 'object') {
      if (data.updatatime !== undefined) upval = data.updatatime;
      else if (data.updatetime !== undefined) upval = data.updatetime;
    }
    if (upval !== undefined && upval !== null && upval !== '') {
      var upout = '';
      if (!isNaN(upval) && upval !== '') {
        var uts = Number(upval);
        if (uts < 1e12) uts = uts * 1000;
        var ud = new Date(uts);
        upout = pad2(ud.getFullYear() % 100) + '年' + pad2(ud.getMonth() + 1) + '月' + pad2(ud.getDate()) + '日';
      } else {
        var parsedUp = Date.parse(String(upval));
        if (!isNaN(parsedUp)) {
          var ud = new Date(parsedUp);
          upout = pad2(ud.getFullYear() % 100) + '年' + pad2(ud.getMonth() + 1) + '月' + pad2(ud.getDate()) + '日';
        } else {
          upout = String(upval);
        }
      }
      var parent = el.parentElement;
      if (parent) {
        var safe = ('更新时间：' + upout).replace(/'/g, "\\'");
        var updateEl = parent.querySelector('.endtime-update');
        if (updateEl) updateEl.textContent = '更新时间：' + upout;
        else {
          var d = document.createElement('div'); d.className = 'endtime-update'; d.textContent = '更新时间：' + upout; parent.appendChild(d);
        }
        parent.setAttribute('data-has-update', '1');
        parent.setAttribute('mdui-tooltip', "{content: '" + safe + "', position: 'bottom'}");
        if (window.innerWidth <= 600) {
          parent.removeAttribute('mdui-tooltip');
        }
        parent.removeAttribute('title');
      }
    }
  }).catch(function(){  });
})();
</script>
<span class="mdui-text-color-theme"></span>