<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<meta name="renderer" content="webkit">
<title>跳转提示</title>
<meta http-equiv="Cache-Control" content="no-siteapp" />
<base target='_self'/>
<style type="text/css">
body{background:rgb(235,247,255)}body,div,p{font-family:microsoft yahei;}.message{position:absolute;top:50px;left:50%;width:350px;height:200px;padding:20px;margin-left:-175px;text-align:center;background:#fff;box-shadow:0 0 10px 3px #F6F0F0;}.message .head{position:absolute;top:0;left:0;width:372px;height:40px;line-height:40px;background:#F86D6D;color:#fff;text-align:left;padding-left:18px;}.message .head span{text-align:left;}.message .content{padding-top:40px;}.jump a{color:#39f;line-height:50px;}
</style>
</head>
<body>
<div class="message">
  <div class="head"><span>提示信息</span></div>
  <div class="content">
    <p class="error">:({{$message}}</p>
    @if ($url != 'javascript:;' && $url != '') 
    <p class="jump"> 等待时间： <b id="wait">{!! $limittime !!}</b> <br />
      <a id="href" href="{!! $url !!}" title="如果你的浏览器没有自动跳转，请点击这里...">如果你的浏览器没有自动跳转，请点击这里...</a> </p>
    @endif
  </div>
</div>
<script type="text/javascript">
(function() {
    @if ($tgobj)
    var tgobj = window.parent.document.getElementById('{!! $tgobj !!}');
    if (tgobj) {
        tgobj.style.display = 'none';
    }
    @endif
	@if ($url != 'javascript:;' && $url != '') 
    var wait = document.getElementById('wait');
    var href = document.getElementById('href').href;
    var interval = setInterval(function() {
        var time = --wait.innerHTML;
        if (time <= 0) {
            location.href = href;
            clearInterval(interval);
        };
    }, 1000);
	@endif
})();
</script>
</body>
</html>