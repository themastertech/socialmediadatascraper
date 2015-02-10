<?PHP
class mailer{
	
	public $from="";
	public $to="";
	public $subject="";
	public $body="";
	public $headers="";
	function __construct(){
		$num_args=func_num_args();
		$this->to=trim(func_get_arg(0));
		$this->subject=func_get_arg(1);
		$this->body=func_get_arg(2);
		if($num_args==4){
		$this->sender=func_get_arg(3);
			//$headers=$func_get_arg(3);
		}
			$headers   = array();
			$headers[] = "MIME-Version: 1.0";
			$headers[] = "Content-type: text/plain; charset=iso-8859-1";
			$headers[] = "From: Kaldit<Kaldit.com>";
			$headers[] = "Subject: {$this->subject}";
			$headers[] = "X-Mailer: PHP/".phpversion();

		mail($this->to, $this->subject, $this->body, implode("\r\n", $headers));
	}
}
?>