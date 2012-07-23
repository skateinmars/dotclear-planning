<?php
$core->addBehavior('initWidgets',
	array('planningWidgets','initWidgets'));
 
 
class planningWidgets
{
	public static function initWidgets(&$w)
	{
		/*
		$w->create('CalendarPlanningWidget',__('Planning : Calendrier'),
			array('publicPlanningWidget','calendar'));
			
		$w->CalendarPlanningWidget->setting('title',__('Title:'),
			__('Calendrier des sessions'),'text');
			
		$w->CalendarPlanningWidget->setting('homeonly',__('Home page only'),0,'check');
		*/
		
		$w->create('ListPlanningWidget',__('Planning : Liste'),
			array('publicPlanningWidget','listWidget'));
			
		$w->ListPlanningWidget->setting('title',__('Title:'),
			__('Sessions à venir'),'text');
			
		$w->ListPlanningWidget->setting('homeonly',__('Home page only'),0,'check');
	}
	
	
}
?>