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
// 读取根目录的 endtime.json 并生成显示文本（含默认“获取中”提示），同时读取 updatatime 用于鼠标悬浮显示
$endtime_display = "<span id='endtime'>:D 获取中...</span>";
$tooltip_attr = '';
$data_has_attr = '';
$endtime_file = dirname(__DIR__) . '/endtime.json';
if (file_exists($endtime_file)) {
  $json = @file_get_contents($endtime_file);
  $data = @json_decode($json, true);
  if (json_last_error() === JSON_ERROR_NONE && $data) {
    // 取出 endtime
    if (is_array($data)) {
      if (isset($data['endtime'])) {
        $val = $data['endtime'];
      } elseif (isset($data['date'])) {
        $val = $data['date'];
      } else {
        $first = reset($data);
        $val = is_scalar($first) ? $first : '';
      }
      // 取出 updatatime（可能不存在）
      if (isset($data['updatatime'])) {
        $upval = $data['updatatime'];
      } else {
        $upval = null;
      }
    } else {
      $val = $data;
      $upval = null;
    }
    // 格式化 endtime
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
        // 格式化 updatatime（若存在）
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
        // 准备 tooltip 属性（放到整行容器上，触发区域为整行），默认空字符串
        $tooltip_attr = '';
        if ($formatted_up !== '') {
          $content = "更新时间：" . $formatted_up;
          // 转义单引号以便在属性内使用单引号包裹
          $safe_content = str_replace("'", "\\'", $content);
          // 生成类似： mdui-tooltip="{content: '更新时间：yy年mm月dd日', position: 'bottom'}"
          $tooltip_attr = " mdui-tooltip=\"{content: '" . $safe_content . "', position: 'bottom'}\"";
          // 同时准备内联更新时间元素（用于移动端显示）
          $endtime_update = "<div class='endtime-update'>" . htmlspecialchars($content) . "</div>";
          // 标记 data 属性用于 CSS/JS 响应式处理
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
        <div class="sidebar-info-endtime" style="font-size:15px;margin-top:5px;">
        <?php
        $expired_display = '';
        $samplePath = dirname(__DIR__) . '/Sample.php';
        if (file_exists($samplePath)) {
          ob_start();
          try {
            include $samplePath;
            $out = trim(ob_get_clean());
            $pos = strpos($out, '{');
            if ($pos !== false) {
              $jsonStr = substr($out, $pos);
              $data = json_decode($jsonStr, true);
              if ($data !== null) {
                function _find_key($arr, $keyName) {
                  if (!is_array($arr)) return null;
                  if (array_key_exists($keyName, $arr)) return $arr[$keyName];
                  foreach ($arr as $v) {
                    if (is_array($v)) {
                      $r = _find_key($v, $keyName);
                      if ($r !== null) return $r;
                    }
                  }
                  return null;
                }
                $expired = _find_key($data, 'ExpiredTime');
                if ($expired !== null) {
                  $ts = strtotime($expired);
                  if ($ts !== false) {
                    $expired_display = date('y年m月d日', $ts);
                  } elseif (is_numeric($expired)) {
                    if ($expired > 9999999999) $expired = (int)($expired / 1000);
                    $expired_display = date('y年m月d日', (int)$expired);
                  } else {
                    $expired_display = htmlspecialchars($expired);
                  }
                }
              }
            }
          } catch (Throwable $e) {
            ob_end_clean();
          }
        }
        echo $expired_display ? $expired_display : "<span id='endtime'>获取中...</span>";
        ?>
        </div>
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
  var css = '\n.sidebar-info-desc .endtime-update{display:none;font-size:12px;color:rgba(0,0,0,.6);margin-top:4px;}\n@media (max-width:600px){\n  .sidebar-info-desc .endtime-update{display:block;}\n  /* 在窄屏上隐藏 MDUI tooltip（如果仍被创建）*/\n  .mdui-tooltip{display:none !important;}\n}\n';
  var style = document.createElement('style');
  style.type = 'text/css';
  if (style.styleSheet) style.styleSheet.cssText = css; else style.appendChild(document.createTextNode(css));
  document.getElementsByTagName('head')[0].appendChild(style);
})();
// 立即在窄屏上移除可能存在的 mdui-tooltip 属性，防止触摸设备显示悬浮提示
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
    // 数字视为 unix 秒时间戳（若看起来是毫秒则直接使用）
    if (!isNaN(val) && val !== '') {
      var ts = Number(val);
      if (ts < 1e12) ts = ts * 1000; // 转为毫秒
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
    // 处理 updatatime 并设置悬浮提示（格式与 endtime 相同）
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
      // 把 tooltip 放到整行父元素上，使用 MDUI tooltip，位置在底部
      var parent = el.parentElement;
      if (parent) {
        var safe = ('更新时间：' + upout).replace(/'/g, "\\'");
        // 设置内联更新时间元素（用于移动端）
        var updateEl = parent.querySelector('.endtime-update');
        if (updateEl) updateEl.textContent = '更新时间：' + upout;
        else {
          var d = document.createElement('div'); d.className = 'endtime-update'; d.textContent = '更新时间：' + upout; parent.appendChild(d);
        }
        parent.setAttribute('data-has-update', '1');
        // 设置 MDUI tooltip，但在窄屏移除 tooltip，以使用内联显示
        parent.setAttribute('mdui-tooltip', "{content: '" + safe + "', position: 'bottom'}");
        if (window.innerWidth <= 600) {
          parent.removeAttribute('mdui-tooltip');
        }
        // 移除可能存在的原生 title
        parent.removeAttribute('title');
      }
    }
  }).catch(function(){ /* 保持 "获取中..." */ });
})();
</script>
<span class="mdui-text-color-theme"></span>