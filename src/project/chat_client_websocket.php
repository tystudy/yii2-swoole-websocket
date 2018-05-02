<!DOCTYPE html>
<html>
<head>
    <title></title>
    <meta charset="UTF-8">
    <script src="http://apps.bdimg.com/libs/jquery/2.1.4/jquery.js"></script>
    <script src="jquery.json.js"></script>
    <script type="text/javascript">
        var fid = 1; //发送者uid
        var tid = 2; //接收者uid
        var exampleSocket = new WebSocket("ws://127.0.0.1:9999");
        $(function () {
            exampleSocket.onopen = function (event) {
                console.log(event.data);
                initData(); //加载历史记录
            };
            exampleSocket.onmessage = function (event) {
                console.log(event.data);
                loadData($.parseJSON(event.data)); //导入消息记录，加载新的消息
            }


        })
        function sendMsg() {
            var pData = {
                content: document.getElementById('content').value,
                fid: fid,
                tid: tid,
            }
            if(pData.content == ''){
                alert("消息不能为空");
                return;
            }
            exampleSocket.send($.toJSON(pData)); //发送消息
        }
        function initData() {
            var pData = {
                fid: fid,
                tid: tid,
            }
            exampleSocket.send($.toJSON(pData)); //获取消息记录，绑定fd
        }
        function loadData(data) {
            for (var i = 0; i < data.length; i++) {
                var html = '<p>' + data[i].fid + '>' + data[i].tid + ':' + data[i].content + '</p>';
                $("#history").append(html);
            }
        }
    </script>
</head>
<body>
<div id="history" style="border: 1px solid #ccc; width: 100px; height: auto">

</div>
<input type="text" id="content">
<button onclick="sendMsg()">发送</button>
</body>
</html>
