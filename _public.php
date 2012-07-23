<?php
require dirname(__FILE__).'/_widgets.php';

class publicPlanningWidget
{
	public static function calendarWidget(&$w)
	{
		global $core;
		
		if ($w->homeonly && $core->url->type != 'default') {
			return;
		}
		
		$month_names = Array("", "Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Aôut", "Septembre", "Octobre", "Novembre", "Décembre");
				
		$dates = array(
		'cur_month' => date("n"),
		'cur_year' => date("Y"),
		'cur_month_name' =>  $month_names[intval(date("n"))]
		);
		
		$prev_month = $dates['cur_month'] -1;
		if ($prev_month < 1)
		{
			$prev_month = 12;
			$prev_year = $dates['cur_year']-1;
		}
		else 
		{
			$prev_year = $dates['cur_year'];
		}
		
		$prev_date = $prev_month.'-'.$prev_year;
		
		$titre =
		'<div class="planning">'.($w->title ? '<h2>'.html::escapeHTML($w->title).'</h2>' : '').'</div>';
		
		$cal = "
<div class='calendrier'>
	<form id='form_date' method='post' action='programmation.php' enctype='multipart/form-data'><p>
		<a href='index.php?date=".$prev_date."'><img src='style/img/precedent.gif' alt='Précédent' /></a>
		<select name='date' onchange='document.getElementById(\"form_date\").submit()'>";
		
		$compteur_mois = date("n");
		$compteur_annee = date("Y");
		for ($i = 1; $i <= 12; $i++)
		{
			$nom = $month_names[$compteur_mois];
			$selected = ($compteur_mois == $dates['cur_month']) ? $selected = 'selected="selected"' : '';
			$cal .= "<option value='".$compteur_mois.'-'.$compteur_annee."' $selected>".$nom.' '.$compteur_annee."</option>";
				
			$compteur_mois += 1;
			if ($compteur_mois > 12) 
			{
				$compteur_mois = 1;
				$compteur_annee += 1;
			}
		}
		
		$cal .= "</select>";
		
		$next_month = $dates['cur_month']+1;
		if ($next_month > 12)
		{
			$next_month = 1;
			$next_year = $dates['cur_year']+1;
		}
		else 
		{
			$next_year = $dates['cur_year'];
		}
		$next_date = $next_month.'-'.$next_year;
		$total_mois_prochain = $nb_mois + 12*$nb_annee +1;
		$total_mois_courant = date('n') + (12 * intval(date('Y')));
		if ($total_mois_prochain < $total_mois_courant+12)
		{
			$cal .= "<a href='programmation.php?date=".$next_date."'><img src='style/img/suivant.gif' alt='Suivant' /></a>";
		}
		
		$cal .= "</p></form><table summary='Calendrier'>
        <caption></caption>
        <thead>
        <tr><th scope='col'><abbr title='lundi'>lun</abbr></th><th scope='col'><abbr title='mardi'>mar</abbr></th><th scope='col'><abbr title='mercredi'>mer</abbr></th><th scope='col'><abbr title='jeudi'>jeu</abbr></th><th scope='col'><abbr title='vendredi'>ven</abbr></th><th scope='col'><abbr title='samedi'>sam</abbr></th><th scope='col'><abbr title='dimanche'>dim</abbr></th></tr>
        </thead>
        <tbody>";
        
        $calendrier = "";
        /* Generation du calendrier */
		$nb_jours_mois = date("t",mktime(1, 1, 1, $dates['cur_month'], 1, $dates['cur_year']));
		for ($jour = 1; $jour <= $nb_jours_mois; $jour++)
		{
			if( ($jour == 1) || ($jour == 8) || ($jour == 15) || ($jour == 22) || ($jour == 29) ) //changement de ligne
			{
				if ($jour == 1)
				{
					$calendrier .= "<tr>";
				}
				else
				{
					$calendrier .= "</tr><tr>";
				}
			}

			$jour_aff = $jour;
			if ( ($jour == date("j")) && ($nb_mois == date("n")) ) //changement de style pour le jour courant
			{
				$jour_aff = '<span class="cal_today">'.$jour.'</span>';
			}
			if (isset($jours_soiree[$jour])) //lien vers la partie de la page concernee
			{
				$jour_aff = '<a href="#soiree_'.$jours_soiree[$jour].'">'.$jour.'</a>';
			}
			
			$calendrier .= "<td>$jour_aff</td>";    
        }
        
        $cal .= $calendrier."</tr></tbody>
    </table>
</div>
		";
		
		return $titre.$cal;
	}
	
	public static function listWidget(&$w)
	{
		global $core;
		
		if ($w->homeonly && $core->url->type != 'default') {
			return;
		}
		require_once dirname(__FILE__).'/class.dc.planning.php';
		$planning = new dcPlanning($GLOBALS['core']);
		
		try {
			$dates = $planning->getDates();
		} catch (Exception $e) {
			return false;
		}
		
		$title =
		'<div class="planning">'.($w->title ? '<h2>'.html::escapeHTML($w->title).'</h2>' : '');
		
		$list = "<ul>";
		
		foreach($dates as $date)
		{
			$list .= "<li>".
			"<a href='".$date['url']."'>".
			$date['title']." : ".$date['date'].
			"</a></li>";
		}
		
		$list .= "</ul>";
		return $title.$list.'</div>';
	}
}
?>