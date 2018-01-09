<?php if (!defined('THINK_PATH')) exit();?>
<div  id="login" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
    <form class="form-signin" method="post" action="/public/doLogin" >
         <div class="modal-header">
		    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		    <h3 id="myModalLabel">欢迎登录<?php echo (C("SITE_NAME")); ?></h3>
  		</div>
  		<div class="modal-body">
  		<div class="main">
       <div class="main-social">
		  <div class="login-weibo">
		    <a href="/public/auth_login/type/sina">
		    <img src="/static/img/weibo.png" alt="48.png" width="140">
			</a>  
			</div>
		  <div class="login-qq">
		    <a    href="/public/auth_login/type/qq">
		    	<img src="/static/img/qq.png" alt="48.png" width="140">
			</a>  
		  </div>
		</div>
		  <div class="main-wechat">
		    <div id="wechat_login_container" data-redirect-uri="https://passport.36kr.com/users/auth/open_wechat/callback" data-appid="wxb186f15cde5a27dd" data-state="9d0a76417f093cfea4f7828cb817e93fd63be38dd62e3751">
		    	<iframe src="https://open.weixin.qq.com/connect/qrconnect?appid=wxb186f15cde5a27dd&amp;scope=snsapi_login&amp;redirect_uri=https://passport.36kr.com/users/auth/open_wechat/callback&amp;state=9d0a76417f093cfea4f7828cb817e93fd63be38dd62e3751&amp;login_type=jssdk&amp;href=https://passport.36kr.com/assets/wechat_qrconnect-b0db866334a6da201ad44c0860496560.css" frameborder="0" scrolling="no" width="300px" height="200px"></iframe>
		    </div>
		  </div>
      </div>
        </div>

    </form>
</div>