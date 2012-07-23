<?php

class dcPlanning
{
	private $blog;
	private $con;
	private $table;
	private $posts_base_url;
	
	public function __construct(&$core)
	{
		$this->blog =& $core->blog;
		$this->con =& $core->blog->con;
		$this->table = $this->blog->prefix.'planning';
		$this->posts_base_url = $this->blog->url.$core->url->getBase('post').'/';
	}
	
	public function getDates($olddates = false)
	{
		if ($olddates == false) {
			$reqOlds = "WHERE date > CURRENT_TIMESTAMP ";
		}
		else
		{
			$reqOlds = "";
		}
		
		$strReq = 'SELECT post_id, title, date '.
				'FROM '.$this->table.' '. $reqOlds .
				"ORDER BY date";
				
		$rs = $this->con->select($strReq);
		$rs = $rs->toStatic();
		
		$dates = array();
		
		while ($rs->fetch())
		{
			$post = $this->blog->getPosts(array('post_id' => $rs->post_id));
			$post->fetch();
			
			$dates[] = array('date' => date ( 'j/n/Y H:i', strtotime($rs->date) ), 
				'date_raw' => $rs->date,
				'title' => $rs->title, 
				'url' => $this->posts_base_url.$post->post_url,
				'post_title' => $post->post_title
			);
		}
		
		return $dates;
	}
	
	public function getDate($date)
	{
		if (!strtotime($date)) {
			throw new Exception(__('Invalid date.'));
		}
		 
		$strReq = 'SELECT post_id, title, date '.
				'FROM '.$this->table.' '.
				"WHERE date = '".$date."' ";
				"ORDER BY date";
				
		$rs = $this->con->select($strReq);
		$rs = $rs->toStatic();
		
		$post = $this->blog->getPosts(array('post_id' => $rs->post_id));
		$post->fetch();
		
		if ($rs->fetch())
		{
			return array('date' => date ( 'j/n/Y H:i', strtotime($rs->date) ), 
					'date_raw' => $rs->date,
					'title' => $rs->title, 
					'url' => $this->posts_base_url.$post->post_url,
					'post_title' => $post->post_title
			);
		}
		return false;
	}
	
	public function addDate($date, $title, $post_id)
	{
		$cur = $this->con->openCursor($this->table);
		
		$cur->title = (string) $title;
		$cur->date = $date;
		$cur->post_id = (integer) $post_id;
		
		if (!strtotime($cur->date)) {
			throw new Exception(__('Invalid date : '.$cur->date.' .'));
		}
		if ($cur->post_id < 1) {
			throw new Exception(__('You must provide a reference to an article'));
		}
		if ($cur->title == '') {
			throw new Exception(__('You must provide a title'));
		}
		
		$checkDate = $this->getDate($date);
		if (is_array($checkDate))
		{
			throw new Exception(__('Session date already exists !'));
		}
		
		$cur->insert();
		$this->blog->triggerBlog();
	}
	
	public function delDate($date)
	{
		$cur = $this->con->openCursor($this->table);
		$cur->date = $date;
		
		if (!strtotime($cur->date)) {
			throw new Exception(__('Invalid date : '.$cur->date));
		}
		
		$strReq = 'DELETE FROM '.$this->table.' '.
				"WHERE date = '".$cur->date."'";
		
		$this->con->execute($strReq);
		$this->blog->triggerBlog();
	}
	
	public function updateDate($date, $title, $post_id)
	{
		$cur = $this->con->openCursor($this->table);
		
		$cur->date = $date;
		$cur->post_id = (integer) $post_id;
		
		if (!strtotime($cur->date)) {
			throw new Exception(__('Invalid date.'));
		}
		if ($cur->title == '') {
			throw new Exception(__('You must provide a title'));
		}
		if ($cur->post_id < 1) {
			throw new Exception(__('You must provide a reference to an article'));
		}
		
		$cur->update('WHERE date = '.$date);
		$this->blog->triggerBlog();
	}
}	
?>