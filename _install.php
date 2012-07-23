<?php
# On lit la version du plugin
$m_version = $core->plugins->moduleInfo('planning','version');

# On lit la version du plugin dans la table des versions
$i_version = $core->getVersion('planning');
 
# La version dans la table est supérieure ou égale à
# celle du module, on ne fait rien puisque celui-ci
# est installé
if (version_compare($i_version,$m_version,'>=')) {
	return;
}
 
# La procédure d'installation commence vraiment là

#on definit la version
$core->setVersion('planning',$m_version);

$s = new dbStruct($core->con,$core->prefix);
 
$s->planning
	->title('varchar',255,false,"'Nouveau cours'")
	->date('timestamp',0,false)
	->post_id('integer',0,false)
	;
$s->planning->primary('pk_planning','date');
//$s->planning->reference('fk_post','post_id','post','post_id','cascade','cascade');

/* Synchronisation de la table */
$si = new dbStruct($core->con,$core->prefix);
$changes = $si->synchronize($s);

return true;
?>