<?php
/**
 * 邮件服务
 *
 * 提供邮件发送服务
 *
 * @author daniel <desheng.young@gmail.com>
 */
class MailService{
	private $option = array();


	public function __construct() {
		$this->init();
	}

	/**
	 * 加载phpmailer, 初始化默认参数
	 */
	public function init() {
		import ( 'ORG.phpmailer.phpmailer' );
		import ( 'ORG.phpmailer.smtp' );
		import ( 'ORG.phpmailer.pop3' );

		$emailset = C('email');
		$this->option = array(
			'email_sendtype'		=> $emailset['email_sendtype'],
			'email_host'			=> $emailset['email_host'],
			'email_port'			=> $emailset['email_port'],
			'email_ssl'				=> $emailset['email_ssl'],
			'email_account'			=> $emailset['email_account'],
			'email_password'		=> $emailset['email_password'],
			'email_sender_name'		=> $emailset['email_sender_name'],
			'email_sender_email'	=> $emailset['email_sender_email'],
			'email_reply_account'	=> $emailset['email_sender_email']
		);
	}

	/**
	 * 发送邮件
	 *
	 * @param string $sendto_email 收信人的Email
	 * @param string $subject      主题
	 * @param string $body         正文
	 * @param array  $senderInfo   发件人信息 array('email_sender_name'=>'发件人姓名', 'email_account'=>'发件人Email地址')
	 * @return boolean
	 */
	public function send_email( $sendto_email, $subject, $body, $senderInfo = '' ) {
		$mail             = new PHPMailer();
		
		$body             = $body;
		$body             = eregi_replace("[\]",'',$body);
		
		$mail->IsSMTP();
		$mail->SMTPAuth   = true;                  // enable SMTP authentication
		$mail->SMTPSecure = "";                 // sets the prefix to the servier
		$mail->Host       = $this->option['email_host'];      // sets GMAIL as the SMTP server
		$mail->Port       = $this->option['email_port'];                   // set the SMTP port for the GMAIL server
		$mail->CharSet    = "UTF-8";
		$mail->Username   = $this->option['email_account'];  // GMAIL username
		$mail->Password   = $this->option['email_password'];            // GMAIL password
		$mail->AddReplyTo($this->option['email_sender_email'],L('site_name'));
		$mail->From       = $this->option['email_sender_email'];
		$mail->FromName   = L('site_name');
		$mail->Subject    = $subject;

		//$mail->Body       = "Hi,<br>This is the HTML BODY<br>";                      //HTML Body
		//$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
		$mail->WordWrap   = 50; // set word wrap
		
		$mail->MsgHTML($body);
		
		$mail->AddAddress($sendto_email);
		
		//$mail->AddAttachment("images/phpmailer.gif");             // attachment
		
		$mail->IsHTML(true); // send as HTML
		return $mail->Send();
	}
}
?>