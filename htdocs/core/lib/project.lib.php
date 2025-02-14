<?php
/* Copyright (C) 2006-2015 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2010      Regis Houssin        <regis.houssin@capnetworks.com>
 * Copyright (C) 2011      Juanjo Menent        <jmenent@2byte.es>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 * or see http://www.gnu.org/
 */

/**
 *	    \file       htdocs/core/lib/project.lib.php
 *		\brief      Functions used by project module
 *      \ingroup    project
 */
require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';


/**
 * Prepare array with list of tabs
 *
 * @param   Object	$object		Object related to tabs
 * @return  array				Array of tabs to show
 */
function project_prepare_head($object)
{
	global $langs, $conf, $user;
	$h = 0;
	$head = array();

	$head[$h][0] = DOL_URL_ROOT.'/projet/card.php?id='.$object->id;
	$head[$h][1] = $langs->trans("Project");
	$head[$h][2] = 'project';
	$h++;

	$head[$h][0] = DOL_URL_ROOT.'/projet/contact.php?id='.$object->id;
	$head[$h][1] = $langs->trans("ProjectContact");
	$head[$h][2] = 'contact';
	$h++;

	if (! empty($conf->fournisseur->enabled) || ! empty($conf->propal->enabled) || ! empty($conf->commande->enabled)
	|| ! empty($conf->facture->enabled) || ! empty($conf->contrat->enabled)
	|| ! empty($conf->ficheinter->enabled) || ! empty($conf->agenda->enabled) || ! empty($conf->deplacement->enabled))
	{
		$head[$h][0] = DOL_URL_ROOT.'/projet/element.php?id='.$object->id;
		$head[$h][1] = $langs->trans("Overview");
		$head[$h][2] = 'element';
		$h++;
	}

	// Show more tabs from modules
	// Entries must be declared in modules descriptor with line
	// $this->tabs = array('entity:+tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to add new tab
	// $this->tabs = array('entity:-tabname);   												to remove a tab
	complete_head_from_modules($conf,$langs,$object,$head,$h,'project');

	if (empty($conf->global->MAIN_DISABLE_NOTES_TAB))
    {
    	$nbNote = 0;
        if(!empty($object->note_private)) $nbNote++;
		if(!empty($object->note_public)) $nbNote++;
		$head[$h][0] = DOL_URL_ROOT.'/projet/note.php?id='.$object->id;
		$head[$h][1] = $langs->trans('Notes');
		if ($nbNote > 0) $head[$h][1].= ' <span class="badge">'.$nbNote.'</span>';
		$head[$h][2] = 'notes';
		$h++;
    }

	require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
	$upload_dir = $conf->projet->dir_output . "/" . dol_sanitizeFileName($object->ref);
	$nbFiles = count(dol_dir_list($upload_dir,'files',0,'','(\.meta|_preview\.png)$'));
	$head[$h][0] = DOL_URL_ROOT.'/projet/document.php?id='.$object->id;
	$head[$h][1] = $langs->trans('Documents');
	if($nbFiles > 0) $head[$h][1].= ' <span class="badge">'.$nbFiles.'</span>';
	$head[$h][2] = 'document';
	$h++;

	if (empty($conf->global->PROJECT_HIDE_TASKS))
	{
		// Then tab for sub level of projet, i mean tasks
		$head[$h][0] = DOL_URL_ROOT.'/projet/tasks.php?id='.$object->id;
		$head[$h][1] = $langs->trans("Tasks");
		$head[$h][2] = 'tasks';
		$h++;

		$head[$h][0] = DOL_URL_ROOT.'/projet/ganttview.php?id='.$object->id;
		$head[$h][1] = $langs->trans("Gantt");
		$head[$h][2] = 'gantt';
		$h++;
	}

	complete_head_from_modules($conf,$langs,$object,$head,$h,'project','remove');

	return $head;
}


/**
 * Prepare array with list of tabs
 *
 * @param   Object	$object		Object related to tabs
 * @return  array				Array of tabs to show
 */
function task_prepare_head($object)
{
	global $langs, $conf, $user;
	$h = 0;
	$head = array();

	$head[$h][0] = DOL_URL_ROOT.'/projet/tasks/task.php?id='.$object->id.(GETPOST('withproject')?'&withproject=1':'');
	$head[$h][1] = $langs->trans("Card");
	$head[$h][2] = 'task_task';
	$h++;

	$head[$h][0] = DOL_URL_ROOT.'/projet/tasks/contact.php?id='.$object->id.(GETPOST('withproject')?'&withproject=1':'');
	$head[$h][1] = $langs->trans("TaskRessourceLinks");
	$head[$h][2] = 'task_contact';
	$h++;

	$head[$h][0] = DOL_URL_ROOT.'/projet/tasks/time.php?id='.$object->id.(GETPOST('withproject')?'&withproject=1':'');
	$head[$h][1] = $langs->trans("TimeSpent");
	$head[$h][2] = 'task_time';
	$h++;

	// Show more tabs from modules
	// Entries must be declared in modules descriptor with line
	// $this->tabs = array('entity:+tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to add new tab
	// $this->tabs = array('entity:-tabname);   												to remove a tab
	complete_head_from_modules($conf,$langs,$object,$head,$h,'task');

	if (empty($conf->global->MAIN_DISABLE_NOTES_TAB))
    {
    	$nbNote = 0;
        if(!empty($object->note_private)) $nbNote++;
		if(!empty($object->note_public)) $nbNote++;
		$head[$h][0] = DOL_URL_ROOT.'/projet/tasks/note.php?id='.$object->id.(GETPOST('withproject')?'&withproject=1':'');
		$head[$h][1] = $langs->trans('Notes');
		if ($nbNote > 0) $head[$h][1].= ' <span class="badge">'.$nbNote.'</span>';
		$head[$h][2] = 'task_notes';
		$h++;
    }

	$head[$h][0] = DOL_URL_ROOT.'/projet/tasks/document.php?id='.$object->id.(GETPOST('withproject')?'&withproject=1':'');
	$filesdir = $conf->projet->dir_output . "/" . dol_sanitizeFileName($object->project->ref) . '/' .dol_sanitizeFileName($object->ref);
	include_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
	$listoffiles=dol_dir_list($filesdir,'files',1,'','thumbs');
	$head[$h][1] = (count($listoffiles)?$langs->trans('DocumentsNb',count($listoffiles)):$langs->trans('Documents'));
	$head[$h][2] = 'task_document';
	$h++;

	complete_head_from_modules($conf,$langs,$object,$head,$h,'task','remove');

	return $head;
}

/**
 * Prepare array with list of tabs
 *
 * @param	string	$mode		Mode
 * @return  array				Array of tabs to show
 */
function project_timesheet_prepare_head($mode)
{
	global $langs, $conf, $user;
	$h = 0;
	$head = array();

	$h = 0;

	if (empty($conf->global->PROJECT_DISABLE_TIMESHEET_PERWEEK))
	{
		$head[$h][0] = DOL_URL_ROOT."/projet/activity/perweek.php".($mode?'?mode='.$mode:'');
		$head[$h][1] = $langs->trans("InputPerWeek");
		$head[$h][2] = 'inputperweek';
		$h++;
	}

	if (empty($conf->global->PROJECT_DISABLE_TIMESHEET_PERTIME))
	{
		$head[$h][0] = DOL_URL_ROOT."/projet/activity/perday.php".($mode?'?mode='.$mode:'');
		$head[$h][1] = $langs->trans("InputPerDay");
		$head[$h][2] = 'inputperday';
		$h++;
	}

	/*if (empty($conf->global->PROJECT_DISABLE_TIMESHEET_PERACTION))
	{
		$head[$h][0] = DOL_URL_ROOT."/projet/activity/peraction.php".($mode?'?mode='.$mode:'');
		$head[$h][1] = $langs->trans("InputPerAction");
		$head[$h][2] = 'inputperaction';
		$h++;
	}*/

	complete_head_from_modules($conf,$langs,null,$head,$h,'project_timesheet');

	complete_head_from_modules($conf,$langs,null,$head,$h,'project_timesheet','remove');

	return $head;
}


/**
 * Prepare array with list of tabs
 *
 * @return  array				Array of tabs to show
 */
function project_admin_prepare_head()
{
	global $langs, $conf, $user;
	$h = 0;
	$head = array();

	$h = 0;

	$head[$h][0] = DOL_URL_ROOT."/projet/admin/project.php";
	$head[$h][1] = $langs->trans("Projects");
	$head[$h][2] = 'project';
	$h++;

	complete_head_from_modules($conf,$langs,null,$head,$h,'project_admin');

	$head[$h][0] = DOL_URL_ROOT."/projet/admin/project_extrafields.php";
	$head[$h][1] = $langs->trans("ExtraFieldsProject");
	$head[$h][2] = 'attributes';
	$h++;

	$head[$h][0] = DOL_URL_ROOT.'/projet/admin/project_task_extrafields.php';
	$head[$h][1] = $langs->trans("ExtraFieldsProjectTask");
	$head[$h][2] = 'attributes_task';
	$h++;

	complete_head_from_modules($conf,$langs,null,$head,$h,'project_admin','remove');

	return $head;
}


/**
 * Show task lines with a particular parent
 *
 * @param	string	   	$inc				Line number (start to 0, then increased by recursive call)
 * @param   string		$parent				Id of parent project to show (0 to show all)
 * @param   Task[]		$lines				Array of lines
 * @param   int			$level				Level (start to 0, then increased/decrease by recursive call)
 * @param 	string		$var				Color
 * @param 	int			$showproject		Show project columns
 * @param	int			$taskrole			Array of roles of user for each tasks
 * @param	int			$projectsListId		List of id of project allowed to user (string separated with comma)
 * @param	int			$addordertick		Add a tick to move task
 * @return	void
 */
function projectLinesa(&$inc, $parent, &$lines, &$level, $var, $showproject, &$taskrole, $projectsListId='', $addordertick=0)
{
	global $user, $bc, $langs;
	global $projectstatic, $taskstatic;

	$lastprojectid=0;

	$projectsArrayId=explode(',',$projectsListId);

	$numlines=count($lines);

	// We declare counter as global because we want to edit them into recursive call
	global $total_projectlinesa_spent,$total_projectlinesa_planned,$total_projectlinesa_spent_if_planned;
	if ($level == 0)
	{
		$total_projectlinesa_spent=0;
		$total_projectlinesa_planned=0;
		$total_projectlinesa_spent_if_planned=0;
	}

	for ($i = 0 ; $i < $numlines ; $i++)
	{
		if ($parent == 0) $level = 0;

		// Process line
		// print "i:".$i."-".$lines[$i]->fk_project.'<br>';

		if ($lines[$i]->fk_parent == $parent)
		{
			// Show task line.
			$showline=1;
			$showlineingray=0;

			// If there is filters to use
			if (is_array($taskrole))
			{
				// If task not legitimate to show, search if a legitimate task exists later in tree
				if (! isset($taskrole[$lines[$i]->id]) && $lines[$i]->id != $lines[$i]->fk_parent)
				{
					// So search if task has a subtask legitimate to show
					$foundtaskforuserdeeper=0;
					searchTaskInChild($foundtaskforuserdeeper,$lines[$i]->id,$lines,$taskrole);
					//print '$foundtaskforuserpeeper='.$foundtaskforuserdeeper.'<br>';
					if ($foundtaskforuserdeeper > 0)
					{
						$showlineingray=1;		// We will show line but in gray
					}
					else
					{
						$showline=0;			// No reason to show line
					}
				}
			}
			else
			{
				// Caller did not ask to filter on tasks of a specific user (this probably means he want also tasks of all users, into public project
				// or into all other projects if user has permission to).
				if (empty($user->rights->projet->all->lire))
				{
					// User is not allowed on this project and project is not public, so we hide line
					if (! in_array($lines[$i]->fk_project, $projectsArrayId))
					{
						// Note that having a user assigned to a task into a project user has no permission on, should not be possible
						// because assignement on task can be done only on contact of project.
						// If assignement was done and after, was removed from contact of project, then we can hide the line.
						$showline=0;
					}
				}
			}

			if ($showline)
			{
				// Break on a new project
				if ($parent == 0 && $lines[$i]->fk_project != $lastprojectid)
				{
					$var = !$var;
					$lastprojectid=$lines[$i]->fk_project;
				}

				print '<tr '.$bc[$var].' id="row-'.$lines[$i]->id.'">'."\n";

				if ($showproject)
				{
					// Project ref
					print "<td>";
					if ($showlineingray) print '<i>';
					$projectstatic->id=$lines[$i]->fk_project;
					$projectstatic->ref=$lines[$i]->projectref;
					$projectstatic->public=$lines[$i]->public;
					if ($lines[$i]->public || in_array($lines[$i]->fk_project,$projectsArrayId)) print $projectstatic->getNomUrl(1);
					else print $projectstatic->getNomUrl(1,'nolink');
					if ($showlineingray) print '</i>';
					print "</td>";

					// Project status
					print '<td>';
					$projectstatic->statut=$lines[$i]->projectstatus;
					print $projectstatic->getLibStatut(2);
					print "</td>";
				}

				// Ref of task
				print '<td>';
				if ($showlineingray)
				{
					print '<i>'.img_object('','projecttask').' '.$lines[$i]->ref.'</i>';
				}
				else
				{
					$taskstatic->id=$lines[$i]->id;
					$taskstatic->ref=$lines[$i]->ref;
					$taskstatic->label=($taskrole[$lines[$i]->id]?$langs->trans("YourRole").': '.$taskrole[$lines[$i]->id]:'');
					print $taskstatic->getNomUrl(1,'withproject');
				}
				print '</td>';

				// Title of task
				print "<td>";
				if ($showlineingray) print '<i>';
				else print '<a href="'.DOL_URL_ROOT.'/projet/tasks/task.php?id='.$lines[$i]->id.'&withproject=1">';
				for ($k = 0 ; $k < $level ; $k++)
				{
					print "&nbsp; &nbsp; &nbsp;";
				}
				print $lines[$i]->label;
				if ($showlineingray) print '</i>';
				else print '</a>';
				print "</td>\n";

				// Date start
				print '<td align="center">';
				print dol_print_date($lines[$i]->date_start,'dayhour');
				print '</td>';

				// Date end
				print '<td align="center">';
				print dol_print_date($lines[$i]->date_end,'dayhour');
				print '</td>';

				$plannedworkloadoutputformat='allhourmin';
				$timespentoutputformat='allhourmin';
				if (! empty($conf->global->PROJECT_PLANNED_WORKLOAD_FORMAT)) $plannedworkloadoutputformat=$conf->global->PROJECT_PLANNED_WORKLOAD_FORMAT;
				if (! empty($conf->global->PROJECT_TIMES_SPENT_FORMAT)) $timespentoutputformat=$conf->global->PROJECT_TIME_SPENT_FORMAT;

				// Planned Workload (in working hours)
				print '<td align="right">';
				$fullhour=convertSecondToTime($lines[$i]->planned_workload,$plannedworkloadoutputformat);
				$workingdelay=convertSecondToTime($lines[$i]->planned_workload,'all',86400,7);	// TODO Replace 86400 and 7 to take account working hours per day and working day per weeks
				if ($lines[$i]->planned_workload != '')
				{
					print $fullhour;
					// TODO Add delay taking account of working hours per day and working day per week
					//if ($workingdelay != $fullhour) print '<br>('.$workingdelay.')';
				}
				//else print '--:--';
				print '</td>';

				// Progress declared
				print '<td align="right">';
				if ($lines[$i]->progress != '')
				{
					print $lines[$i]->progress.' %';
				}
				print '</td>';

				// Time spent
				print '<td align="right">';
				if ($showlineingray) print '<i>';
				else print '<a href="'.DOL_URL_ROOT.'/projet/tasks/time.php?id='.$lines[$i]->id.($showproject?'':'&withproject=1').'">';
				if ($lines[$i]->duration) print convertSecondToTime($lines[$i]->duration,$timespentoutputformat);
				else print '--:--';
				if ($showlineingray) print '</i>';
				else print '</a>';
				print '</td>';

				// Progress calculated (Note: ->duration is time spent)
				print '<td align="right">';
				if ($lines[$i]->planned_workload || $lines[$i]->duration)
				{
					if ($lines[$i]->planned_workload) print round(100 * $lines[$i]->duration / $lines[$i]->planned_workload,2).' %';
					else print $langs->trans('WorkloadNotDefined');
				}
				print '</td>';

				// Tick to drag and drop
				if ($addordertick)
				{
					print '<td align="center" class="tdlineupdown hideonsmartphone">&nbsp;</td>';
				}

				print "</tr>\n";

				if (! $showlineingray) $inc++;

				$level++;
				if ($lines[$i]->id) projectLinesa($inc, $lines[$i]->id, $lines, $level, $var, $showproject, $taskrole, $projectsListId, $addordertick);
				$level--;
				$total_projectlinesa_spent += $lines[$i]->duration;
				$total_projectlinesa_planned += $lines[$i]->planned_workload;
				if ($lines[$i]->planned_workload) $total_projectlinesa_spent_if_planned += $lines[$i]->duration;
			}
		}
		else
		{
			//$level--;
		}
	}

	if (($total_projectlinesa_planned > 0 || $total_projectlinesa_spent > 0) && $level==0)
	{
		print '<tr class="liste_total nodrag nodrop">';
		print '<td class="liste_total">'.$langs->trans("Total").'</td>';
		if ($showproject) print '<td></td><td></td>';
		print '<td></td>';
		print '<td></td>';
		print '<td></td>';
		print '<td align="right" class="nowrap liste_total">';
		print convertSecondToTime($total_projectlinesa_planned, 'allhourmin');
		print '</td>';
		print '<td></td>';
		print '<td align="right" class="nowrap liste_total">';
		print convertSecondToTime($total_projectlinesa_spent, 'allhourmin');
		print '</td>';
		print '<td align="right" class="nowrap liste_total">';
		if ($total_projectlinesa_planned) print round(100 * $total_projectlinesa_spent / $total_projectlinesa_planned,2).' %';
		print '</td>';
		if ($addordertick) print '<td class="hideonsmartphone"></td>';
		print '</tr>';
	}

	return $inc;
}


/**
 * Output a task line into a pertime intput mode
 *
 * @param	string	   	$inc					Line number (start to 0, then increased by recursive call)
 * @param   string		$parent					Id of parent project to show (0 to show all)
 * @param   Task[]		$lines					Array of lines
 * @param   int			$level					Level (start to 0, then increased/decrease by recursive call)
 * @param   string		$projectsrole			Array of roles user has on project
 * @param   string		$tasksrole				Array of roles user has on task
 * @param	string		$mine					Show only task lines I am assigned to
 * @param   int			$restricteditformytask	0=No restriction, 1=Enable add time only if task is a task i am affected to
 * @param	int			$preselectedday			Preselected day
 * @return  $inc
 */
function projectLinesPerDay(&$inc, $parent, $lines, &$level, &$projectsrole, &$tasksrole, $mine, $restricteditformytask=1, $preselectedday='')
{
	global $db, $user, $bc, $langs;
	global $form, $formother, $projectstatic, $taskstatic;

	$lastprojectid=0;

	$var=true;

	$numlines=count($lines);
	for ($i = 0 ; $i < $numlines ; $i++)
	{
		if ($parent == 0) $level = 0;

		if ($lines[$i]->fk_parent == $parent)
		{
			// Break on a new project
			if ($parent == 0 && $lines[$i]->fk_project != $lastprojectid)
			{
				$var = !$var;
				$lastprojectid=$lines[$i]->fk_project;

				if ($preselectedday)
				{
					$projectstatic->id = $lines[$i]->fk_project;
					$projectstatic->loadTimeSpent($preselectedday, 0, $fuser->id);	// Load time spent into this->weekWorkLoad and this->weekWorkLoadPerTaks for all day of a week
				}
			}

			// If we want all or we have a role on task, we show it
			if (empty($mine) || ! empty($tasksrole[$lines[$i]->id]))
			{
				$projectstatic->id=$lines[$i]->fk_project;
				$projectstatic->ref=$lines[$i]->projectref;
				$projectstatic->title=$lines[$i]->projectlabel;
				$projectstatic->public=$lines[$i]->public;

				$taskstatic->id=$lines[$i]->id;

				print "<tr ".$bc[$var].">\n";

				// Project
				print "<td>";
				print $projectstatic->getNomUrl(1,'',0,$langs->transnoentitiesnoconv("YourRole").': '.$projectsrole[$lines[$i]->fk_project]);
				print "</td>";

				// Ref
				print '<td>';
				$taskstatic->ref=($lines[$i]->ref?$lines[$i]->ref:$lines[$i]->id);
				print $taskstatic->getNomUrl(1,'withproject');
				print '</td>';

				// Label task
				print "<td>";
				for ($k = 0 ; $k < $level ; $k++) print "&nbsp;&nbsp;&nbsp;";
				$taskstatic->id=$lines[$i]->id;
				$taskstatic->ref=$lines[$i]->label;
				$taskstatic->date_start=$lines[$i]->date_start;
				$taskstatic->date_end=$lines[$i]->date_end;
				print $taskstatic->getNomUrl(0,'withproject');
				//print "<br>";
				//for ($k = 0 ; $k < $level ; $k++) print "&nbsp;&nbsp;&nbsp;";
				//print get_date_range($lines[$i]->date_start,$lines[$i]->date_end,'',$langs,0);
				print "</td>\n";

				// Planned Workload
				print '<td align="right">';
				if ($lines[$i]->planned_workload) print convertSecondToTime($lines[$i]->planned_workload,'allhourmin');
				else print '--:--';
				print '</td>';

				// Progress declared %
				print '<td align="right">';
				print $formother->select_percent($lines[$i]->progress, $lines[$i]->id . 'progress');
				print '</td>';

				// Time spent by everybody
				print '<td align="right">';
				// $lines[$i]->duration is a denormalised field = summ of time spent by everybody for task. What we need is time consummed by user
				if ($lines[$i]->duration)
				{
					print '<a href="'.DOL_URL_ROOT.'/projet/tasks/time.php?id='.$lines[$i]->id.'">';
					print convertSecondToTime($lines[$i]->duration,'allhourmin');
					print '</a>';
				}
				else print '--:--';
				print "</td>\n";

				// Time spent by user
				print '<td align="right">';
				$tmptimespent=$taskstatic->getSummaryOfTimeSpent();
				if ($tmptimespent['total_duration']) print convertSecondToTime($tmptimespent['total_duration'],'allhourmin');
				else print '--:--';
				print "</td>\n";

				$disabledproject=1;$disabledtask=1;
				//print "x".$lines[$i]->fk_project;
				//var_dump($lines[$i]);
				//var_dump($projectsrole[$lines[$i]->fk_project]);
				// If at least one role for project
				if ($lines[$i]->public || ! empty($projectsrole[$lines[$i]->fk_project]) || $user->rights->projet->all->creer)
				{
					$disabledproject=0;
					$disabledtask=0;
				}
				// If $restricteditformytask is on and I have no role on task, i disable edit
				if ($restricteditformytask && empty($tasksrole[$lines[$i]->id]))
				{
					$disabledtask=1;
				}

				// Form to add new time
				print '<td class="nowrap" align="center">';
				$tableCell=$form->select_date($preselectedday,$lines[$i]->id,1,1,2,"addtime",0,0,1,$disabledtask);
				print $tableCell;
				print '</td><td align="right">';

				$dayWorkLoad = $projectstatic->weekWorkLoadPerTask[$preselectedday][$lines[$i]->id];
		        $alreadyspent='';
		        if ($dayWorkLoad > 0) $alreadyspent=convertSecondToTime($dayWorkLoad,'allhourmin');

				$tableCell='';
				$tableCell.='<span class="timesheetalreadyrecorded"><input type="text" class="center" size="2" disabled id="timespent['.$inc.']['.$idw.']" name="task['.$lines[$i]->id.']['.$idw.']" value="'.$alreadyspent.'"></span>';
                $tableCell.=' + ';
				//$tableCell.='&nbsp;&nbsp;&nbsp;';
				$tableCell.=$form->select_duration($lines[$i]->id.'duration','',$disabledtask,'text',0,1);
				//$tableCell.='&nbsp;<input type="submit" class="button"'.($disabledtask?' disabled':'').' value="'.$langs->trans("Add").'">';
				print $tableCell;
				print '</td>';

				print '<td align="right">';
				if ((! $lines[$i]->public) && $disabledproject) print $form->textwithpicto('',$langs->trans("YouAreNotContactOfProject"));
				else if ($disabledtask) print $form->textwithpicto('',$langs->trans("TaskIsNotAffectedToYou"));
				print '</td>';

				print "</tr>\n";
			}

			$inc++;
			$level++;
			if ($lines[$i]->id) projectLinesPerDay($inc, $lines[$i]->id, $lines, $level, $projectsrole, $tasksrole, $mine, $restricteditformytask, $preselectedday);
			$level--;
		}
		else
		{
			//$level--;
		}
	}

	return $inc;
}



/**
 * Output a task line into a perday intput mode
 *
 * @param	string	   	$inc					Line number (start to 0, then increased by recursive call)
 * @param	int			$firstdaytoshow			First day to show
 * @param	User|null	$fuser					Restrict list to user if defined
 * @param   string		$parent					Id of parent project to show (0 to show all)
 * @param   Task[]		$lines					Array of lines
 * @param   int			$level					Level (start to 0, then increased/decrease by recursive call)
 * @param   string		$projectsrole			Array of roles user has on project
 * @param   string		$tasksrole				Array of roles user has on task
 * @param	string		$mine					Show only task lines I am assigned to
 * @param   int			$restricteditformytask	0=No restriction, 1=Enable add time only if task is a task i am affected to
 * @return  $inc
 */
function projectLinesPerWeek(&$inc, $firstdaytoshow, $fuser, $parent, $lines, &$level, &$projectsrole, &$tasksrole, $mine, $restricteditformytask=1)
{
	global $db, $user, $bc, $langs;
	global $form, $formother, $projectstatic, $taskstatic;

	$lastprojectid=0;

	$var=true;

	$numlines=count($lines);
	for ($i = 0 ; $i < $numlines ; $i++)
	{
		if ($parent == 0) $level = 0;

		if ($lines[$i]->fk_parent == $parent)
		{
			// Break on a new project
			if ($parent == 0 && $lines[$i]->fk_project != $lastprojectid)
			{
				$var = !$var;
				$lastprojectid=$lines[$i]->fk_project;

				$projectstatic->id = $lines[$i]->fk_project;
				$projectstatic->loadTimeSpent($firstdaytoshow, 0, $fuser->id);	// Load time spent into this->weekWorkLoad and this->weekWorkLoadPerTaks for all day of a week
			}

			// If we want all or we have a role on task, we show it
			if (empty($mine) || ! empty($tasksrole[$lines[$i]->id]))
			{
				print "<tr ".$bc[$var].">\n";

				// Project
				print '<td class="nowrap">';
				$projectstatic->id=$lines[$i]->fk_project;
				$projectstatic->ref=$lines[$i]->projectref;
				$projectstatic->title=$lines[$i]->projectlabel;
				$projectstatic->public=$lines[$i]->public;
				print $projectstatic->getNomUrl(1,'',0,$langs->transnoentitiesnoconv("YourRole").': '.$projectsrole[$lines[$i]->fk_project]);
				print "</td>";

				// Ref
				print '<td class="nowrap">';
				$taskstatic->id=$lines[$i]->id;
				$taskstatic->ref=($lines[$i]->ref?$lines[$i]->ref:$lines[$i]->id);
				print $taskstatic->getNomUrl(1, 'withproject', 'time');
				print '</td>';

				// Label task
				print "<td>";
				print '<!-- Task id = '.$lines[$i]->id.' -->';
				for ($k = 0 ; $k < $level ; $k++) print "&nbsp;&nbsp;&nbsp;";
				$taskstatic->id=$lines[$i]->id;
				$taskstatic->ref=$lines[$i]->label;
				$taskstatic->date_start=$lines[$i]->date_start;
				$taskstatic->date_end=$lines[$i]->date_end;
				print $taskstatic->getNomUrl(0, 'withproject', 'time');
				//print "<br>";
				//for ($k = 0 ; $k < $level ; $k++) print "&nbsp;&nbsp;&nbsp;";
				//print get_date_range($lines[$i]->date_start,$lines[$i]->date_end,'',$langs,0);
				print "</td>\n";

				// Planned Workload
				print '<td align="right">';
				if ($lines[$i]->planned_workload) print convertSecondToTime($lines[$i]->planned_workload,'allhourmin');
				else print '--:--';
				print '</td>';

				// Progress declared %
				print '<td align="right">';
				print $formother->select_percent($lines[$i]->progress, $lines[$i]->id . 'progress');
				print '</td>';

				// Time spent by everybody
				print '<td align="right">';
				// $lines[$i]->duration is a denormalised field = summ of time spent by everybody for task. What we need is time consummed by user
				if ($lines[$i]->duration)
				{
					print '<a href="'.DOL_URL_ROOT.'/projet/tasks/time.php?id='.$lines[$i]->id.'">';
					print convertSecondToTime($lines[$i]->duration,'allhourmin');
					print '</a>';
				}
				else print '--:--';
				print "</td>\n";

				// Time spent by user
				print '<td align="right">';
				$tmptimespent=$taskstatic->getSummaryOfTimeSpent();
				if ($tmptimespent['total_duration']) print convertSecondToTime($tmptimespent['total_duration'],'allhourmin');
				else print '--:--';
				print "</td>\n";

				$disabledproject=1;$disabledtask=1;
				//print "x".$lines[$i]->fk_project;
				//var_dump($lines[$i]);
				//var_dump($projectsrole[$lines[$i]->fk_project]);
				// If at least one role for project
				if ($lines[$i]->public || ! empty($projectsrole[$lines[$i]->fk_project]) || $user->rights->projet->all->creer)
				{
					$disabledproject=0;
					$disabledtask=0;
				}
				// If $restricteditformytask is on and I have no role on task, i disable edit
				if ($restricteditformytask && empty($tasksrole[$lines[$i]->id]))
				{
					$disabledtask=1;
				}

				//var_dump($projectstatic->weekWorkLoadPerTask);

				// Fields to show current time
				$tableCell=''; $modeinput='hours';
				for ($idw = 0; $idw < 7; $idw++)
		        {
					$tmpday=dol_time_plus_duree($firstdaytoshow, $idw, 'd');
					$tmparray=dol_getdate($tmpday);
		        	$dayWorkLoad = $projectstatic->weekWorkLoadPerTask[$tmpday][$lines[$i]->id];
		        	$alreadyspent='';
		        	if ($dayWorkLoad > 0) $alreadyspent=convertSecondToTime($dayWorkLoad,'allhourmin');
                    $tableCell ='<td align="center">';
                    $tableCell.='<span class="timesheetalreadyrecorded"><input type="text" class="center" size="2" disabled id="timespent['.$inc.']['.$idw.']" name="task['.$lines[$i]->id.']['.$idw.']" value="'.$alreadyspent.'"></span>';
                    //$placeholder=' placeholder="00:00"';
                    $placeholder='';
                    //if (! $disabledtask)
                    //{
                    	$tableCell.='+';
                    	$tableCell.='<input type="text" alt="'.$langs->trans("AddHereTimeSpentForDay",$tmparray['day'],$tmparray['mon']).'" title="'.$langs->trans("AddHereTimeSpentForDay",$tmparray['day'],$tmparray['mon']).'" '.($disabledtask?'disabled':$placeholder).' class="center" size="2" id="timeadded['.$inc.']['.$idw.']" name="task['.$lines[$i]->id.']['.$idw.']" value="" cols="2"  maxlength="5"';
		        		$tableCell.=' onkeypress="return regexEvent(this,event,\'timeChar\')"';
                    	$tableCell.= 'onblur="regexEvent(this,event,\''.$modeinput.'\'); updateTotal('.$idw.',\''.$modeinput.'\')" />';
                    //}
                    $tableCell.='</td>';
                    print $tableCell;
		        }

				print '<td align="right">';
				if ((! $lines[$i]->public) && $disabledproject) print $form->textwithpicto('',$langs->trans("YouAreNotContactOfProject"));
				else if ($disabledtask) print $form->textwithpicto('',$langs->trans("TaskIsNotAffectedToYou"));
				print '</td>';

		        print "</tr>\n";
			}

			$inc++;
			$level++;
			if ($lines[$i]->id) projectLinesPerWeek($inc, $firstdaytoshow, $fuser, $lines[$i]->id, $lines, $level, $projectsrole, $tasksrole, $mine, $restricteditformytask);
			$level--;
		}
		else
		{
			//$level--;
		}
	}

	return $inc;
}


/**
 * Search in task lines with a particular parent if there is a task for a particular user (in taskrole)
 *
 * @param 	string	$inc				Counter that count number of lines legitimate to show (for return)
 * @param 	int		$parent				Id of parent task to start
 * @param 	array	$lines				Array of all tasks
 * @param	string	$taskrole			Array of task filtered on a particular user
 * @return	int							1 if there is
 */
function searchTaskInChild(&$inc, $parent, &$lines, &$taskrole)
{
	//print 'Search in line with parent id = '.$parent.'<br>';
	$numlines=count($lines);
	for ($i = 0 ; $i < $numlines ; $i++)
	{
		// Process line $lines[$i]
		if ($lines[$i]->fk_parent == $parent && $lines[$i]->id != $lines[$i]->fk_parent)
		{
			// If task is legitimate to show, no more need to search deeper
			if (isset($taskrole[$lines[$i]->id]))
			{
				//print 'Found a legitimate task id='.$lines[$i]->id.'<br>';
				$inc++;
				return $inc;
			}

			searchTaskInChild($inc, $lines[$i]->id, $lines, $taskrole);
			//print 'Found inc='.$inc.'<br>';

			if ($inc > 0) return $inc;
		}
	}

	return $inc;
}

/**
 * Return HTML table with list of projects and number of opened tasks
 *
 * @param	DoliDB	$db					Database handler
 * @param	Form	$form				Object form
 * @param   int		$socid				Id thirdparty
 * @param   int		$projectsListId     Id of project i have permission on
 * @param   int		$mytasks            Limited to task i am contact to
 * @param	int		$statut				-1=No filter on statut, 0 or 1 = Filter on status
 * @param	array	$listofoppstatus	List of opportunity status
 * @return	void
 */
function print_projecttasks_array($db, $form, $socid, $projectsListId, $mytasks=0, $statut=-1, $listofoppstatus=array())
{
	global $langs,$conf,$user,$bc;

	require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';

	$projectstatic=new Project($db);

	$sortfield='';
	$sortorder='';
	$project_year_filter=0;

	$title=$langs->trans("Projects");
	if (strcmp($statut, '') && $statut >= 0) $title=$langs->trans("Projects").' '.$langs->trans($projectstatic->statuts_long[$statut]);

	print '<table class="noborder" width="100%">';

	$sql = "SELECT p.rowid as projectid, p.ref, p.title, p.fk_user_creat, p.public, p.fk_statut as status, p.fk_opp_status as opp_status, p.opp_amount, COUNT(DISTINCT t.rowid) as nb";	// We use DISTINCT here because line can be doubled if task has 2 links to same user
	$sql.= " FROM ".MAIN_DB_PREFIX."projet as p";
	if ($mytasks)
	{
		$sql.= ", ".MAIN_DB_PREFIX."projet_task as t";
		$sql.= ", ".MAIN_DB_PREFIX."element_contact as ec";
		$sql.= ", ".MAIN_DB_PREFIX."c_type_contact as ctc";
	}
	else
	{
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."projet_task as t ON p.rowid = t.fk_projet";
	}
	$sql.= " WHERE p.entity = ".$conf->entity;
	$sql.= " AND p.rowid IN (".$projectsListId.")";
	if ($socid) $sql.= "  AND (p.fk_soc IS NULL OR p.fk_soc = 0 OR p.fk_soc = ".$socid.")";
	if ($mytasks)
	{
		$sql.= " AND p.rowid = t.fk_projet";
		$sql.= " AND ec.element_id = t.rowid";
		$sql.= " AND ctc.rowid = ec.fk_c_type_contact";
		$sql.= " AND ctc.element = 'project_task'";
		$sql.= " AND ec.fk_socpeople = ".$user->id;
	}
	if ($statut >= 0)
	{
		$sql.= " AND p.fk_statut = ".$statut;
	}
	if (!empty($conf->global->PROJECT_LIMIT_YEAR_RANGE))
	{
		$project_year_filter = GETPOST("project_year_filter");
		//Check if empty or invalid year. Wildcard ignores the sql check
		if ($project_year_filter != "*")
		{
			if (empty($project_year_filter) || !ctype_digit($project_year_filter))
			{
				$project_year_filter = date("Y");
			}
			$sql.= " AND (p.dateo IS NULL OR p.dateo <= ".$db->idate(dol_get_last_day($project_year_filter,12,false)).")";
			$sql.= " AND (p.datee IS NULL OR p.datee >= ".$db->idate(dol_get_first_day($project_year_filter,1,false)).")";
		}
	}
	$sql.= " GROUP BY p.rowid, p.ref, p.title, p.fk_user_creat, p.public, p.fk_statut, p.fk_opp_status, p.opp_amount";
	$sql.= " ORDER BY p.title, p.ref";

	$var=true;
	$resql = $db->query($sql);
	if ( $resql )
	{
		$total_task = 0;
		$total_opp_amount = 0;
		$ponderated_opp_amount = 0;

		$num = $db->num_rows($resql);
		$i = 0;

    	print '<tr class="liste_titre">';
    	print_liste_field_titre($title.' <span class="badge">'.$num.'</span>',$_SERVER["PHP_SELF"],"","","","",$sortfield,$sortorder);
    	if (! empty($conf->global->PROJECT_USE_OPPORTUNITIES))
    	{
    		print_liste_field_titre($langs->trans("OpportunityAmount"),"","","","",'align="right"',$sortfield,$sortorder);
    		print_liste_field_titre($langs->trans("OpportunityStatus"),"","","","",'align="right"',$sortfield,$sortorder);
    	}
    	if (empty($conf->global->PROJECT_HIDE_TASKS)) print_liste_field_titre($langs->trans("Tasks"),"","","","",'align="right"',$sortfield,$sortorder);
    	print_liste_field_titre($langs->trans("Status"),"","","","",'align="right"',$sortfield,$sortorder);
    	print "</tr>\n";
		
		while ($i < $num)
		{
			$objp = $db->fetch_object($resql);

			$projectstatic->id = $objp->projectid;
			$projectstatic->user_author_id = $objp->fk_user_creat;
			$projectstatic->public = $objp->public;

			// Check is user has read permission on project
			$userAccess = $projectstatic->restrictedProjectArea($user);
			if ($userAccess >= 0)
			{
				$var=!$var;
				print "<tr ".$bc[$var].">";
				print '<td class="nowrap">';
				$projectstatic->ref=$objp->ref;
				print $projectstatic->getNomUrl(1);
				print ' - '.dol_trunc($objp->title,24).'</td>';
				if (! empty($conf->global->PROJECT_USE_OPPORTUNITIES))
				{
					print '<td align="right">';
        			if ($objp->opp_amount) print price($objp->opp_amount, 0, '', 1, -1, -1, $conf->currency);
					print '</td>';
					print '<td align="right">';
					$code = dol_getIdFromCode($db, $objp->opp_status, 'c_lead_status', 'rowid', 'code');
        			if ($code) print $langs->trans("OppStatus".$code);
					print '</td>';
				}
				$projectstatic->statut = $objp->status;
				if (empty($conf->global->PROJECT_HIDE_TASKS)) print '<td align="right">'.$objp->nb.'</td>';
				print '<td align="right">'.$projectstatic->getLibStatut(3).'</td>';
				print "</tr>\n";

				$total_task = $total_task + $objp->nb;
				$total_opp_amount = $total_opp_amount + $objp->opp_amount;
				$ponderated_opp_amount = $ponderated_opp_amount + price2num($listofoppstatus[$objp->opp_status] * $objp->opp_amount / 100);
			}

			$i++;
		}

		print '<tr><td>'.$langs->trans("Total")."</td>";
		if (! empty($conf->global->PROJECT_USE_OPPORTUNITIES))
		{
			print '<td align="right">'.price($total_opp_amount, 0, '', 1, -1, -1, $conf->currency).'</td>';
			print '<td align="right">'.$form->textwithpicto(price($ponderated_opp_amount, 0, '', 1, -1, -1, $conf->currency), $langs->trans("OpportunityPonderatedAmount"), 1).'</td>';
		}
		if (empty($conf->global->PROJECT_HIDE_TASKS)) print '<td align="right">'.$total_task.'</td>';

		$db->free($resql);
	}
	else
	{
		dol_print_error($db);
	}

	print "</table>";

	if (!empty($conf->global->PROJECT_LIMIT_YEAR_RANGE))
	{
		//Add the year filter input
		print '<form method="get" action="'.$_SERVER["PHP_SELF"].'">';
		print '<table width="100%">';
		print '<tr>';
		print '<td>'.$langs->trans("Year").'</td>';
		print '<td style="text-align:right"><input type="text" size="4" class="flat" name="project_year_filter" value="'.$project_year_filter.'"/>';
		print "</tr>\n";
		print '</table></form>';
	}
}

