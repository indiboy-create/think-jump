<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>跳转提示</title>
    <style>
        * { margin: 0; padding: 0; }
        body { background: #fff; color: #333; font: 16px/1.8 "Microsoft Yahei", Arial, sans-serif; }
        .jump-box { max-width: 640px; margin: 120px auto 0; padding: 0 24px; }
        .jump-box h1 { font-size: 40px; font-weight: normal; }
        .jump-box .msg { font-size: 20px; margin-top: 8px; }
        .jump-box .tip { margin-top: 16px; color: #666; }
        .jump-box a { color: #1677ff; text-decoration: none; }
    </style>
</head>
<body>
<div class="jump-box">
    {if $code == 1}
    <h1>操作成功</h1>
    {else /}
    <h1>操作失败</h1>
    {/if}
    <p class="msg">{$msg}</p>
    <p class="tip">页面将在 <span id="wait">{$wait}</span> 秒后自动跳转，<a id="href" href="{$url}">立即跳转</a></p>
</div>
<script>
(function () {
    var wait = document.getElementById('wait');
    var href = document.getElementById('href').href;
    var timer = setInterval(function () {
        var time = --wait.innerHTML;
        if (time <= 0) {
            clearInterval(timer);
            location.href = href;
        }
    }, 1000);
})();
</script>
</body>
</html>