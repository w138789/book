var timerSuper, timerSubordinate; //消息提示变量
/**
 * 异步提交
 * @param url 请求地址
 * @param method get post
 * @param datas 提交数据
 * @param successCallBack
 * @param errorCallback
 */
function ajaxRequest (url, method, datas, successCallBack, errorCallback) {
    var httpRequest = new XMLHttpRequest();//第一步：创建需要的对象
    httpRequest.open(method, url, true); //第二步：打开连接
    if (method.toUpperCase == 'POST') {
        httpRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");//设置请求头 注：post方式必须设置请求头（在建立连接后设置请求头）
        httpRequest.send('name=teswe&ee=ef');//发送请求 将情头体写在send中
    } else {
        httpRequest.send();//第三步：发送请求  将请求参数写在URL中
    }

    /**
     * 获取数据后的处理程序
     */
    httpRequest.onreadystatechange = function () {
        if (httpRequest.readyState == 4 && httpRequest.status == 200) {
            var json = httpRequest.responseText;
            if (json) {
                var data = JSON.parse(json);
                switch (data.error) {
                    case 0:
                        successCallBack(data.data, json);
                        break;
                    case 400:
                        if (!errorCallback) {
                            reportError(data.message);
                        } else {
                            errorCallback(data)
                        }
                        break;
                    default:
                        successCallBack(data.data, json);
                        break;
                }
            } else {
                successCallBack( json);
            }
        }
    }
}

/**
 * 失败错误提示
 * @param message
 */
function reportError (message) {
    showToast(message);
}

/**
 * 消息提示
 * @param msg 提示内容
 * @param duration 时间
 */
function showToast (msg, duration) {
    var showToast = document.getElementById("showToast");
    if (showToast) {
        showToast.remove();
        clearTimeout(timerSuper);
        if (timerSubordinate) {
            clearTimeout(timerSubordinate);
        }
    }
    duration = isNaN(duration) ? 3000 : duration;
    var m = document.createElement('div');
    m.innerHTML = msg;
    m.id = 'showToast';
    m.style.cssText = "padding: 5px 10px; font-size: 14px; background:#000; opacity:0.6; height:auto;min-height: 30px; color:#fff; line-height:30px; text-align:center; border-radius:4px; position:fixed; top:50%; left:50%; z-index:999999;transform: translate(-50%,-50%);";
    document.body.appendChild(m);
    timerSuper = setTimeout(function () {
        var d = 0.5;
        m.style.webkitTransition = '-webkit-transform ' + d + 's ease-in, opacity ' + d + 's ease-in';
        m.style.opacity = '0';
        timerSubordinate = setTimeout(function () {
            document.body.removeChild(m)
        }, d * 1000);
    }, duration);
}

/**
 * 移除loading效果
 */
function removeLoading (time) {
    //移除时间
    if (!time) time = 1.3;
    timerSubordinate = setTimeout(function () {
        var div = document.getElementById("loadingDiv");
        if (div) {
            div.style.webkitTransition = '-webkit-transform ' + time + 's ease-in, opacity ' + time + 's ease-in';
            div.style.opacity = '0';
            div.parentNode.removeChild(div)
        }
    }, time * 1000);
}

/**
 * 展示loading效果
 */
function showLoading () {
    var toast = document.getElementById("showToast");
    if (toast) toast.style.display = 'none';
    var div = document.createElement('div');
    //在页面未加载完毕之前显示的loading Html自定义内容
    div.innerHTML = '<div id="loadingDiv" style=" position: absolute;top: 0;left: 0; width: 100%;height: 100%; background-color: rgba(0, 0, 0, 0.35);opacity:0.5;z-index: 1000;"><div id="over" style="font-size: 16px;background-color:#000000;opacity: 0.6;height: 128px;min-height: 30px;color: rgb(255, 255, 255);line-height: 30px;text-align: center;border-radius: 4px;position: fixed;top: 50%;left: 50%;z-index: 999999;transform: translate(-50%, -50%);"><img src="/Public/Common/images/loading.gif" /></div></div>';
    //呈现loading效果
    document.body.appendChild(div);
}

/**
 * url跳转
 * @param url
 */
function goTo (url) {
    window.location.href = url;
}



