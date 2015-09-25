<?php
$form_id = 0;

$packageName = 'migxformzusergroups';
$packagepath = $modx->getOption('core_path') . 'components/' . $packageName . '/';
$modelpath = $packagepath . 'model/';
if (is_dir($modelpath)) {
    $modx->addPackage($packageName, $modelpath);
}

switch ($modx->event->name) {

    case 'OnFormzFormsPrepareQueryBeforeCount':
        /* check for usergroups to restrict forms to permitted usergroups - get all usergroups the user is in */
        $usergroups = $modx->user->getUserGroups();
        $joinclass = 'fmzFormsUsergroup';
        $jalias = 'UserGroups';
        $on = 'fmzForms.id = UserGroups.form_id';

        $c->leftjoin($joinclass, $jalias, $on);

        /* get only forms without usergroup-connection or with usergoups, where the user is in */
        $c->where(array('UserGroups.group_id:IN' => $usergroups, 'OR:UserGroups.group_id:=' => null));
        $c->groupby('fmzForms.id');
        
        break;

    case 'OnFormzFormsBeforeSave':
    case 'OnFormzFormsFieldsBeforeSave':
        if ($mode == modSystemEvent::MODE_UPD || ($mode == modSystemEvent::MODE_NEW && isset($data['form_id']))) {
            $form_id = isset($data['form_id']) ? $data['form_id'] : $id;
        }
        break;

    case 'OnFormzFormsBeforeRemove':
        $form_id = $id;
        break;
    case 'OnFormzFormsFieldsBeforeRemove':
        if ($removeObject = $modx->getObject('fmzFormsFields',$id)) {
            $form_id = $removeObject->get('form_id');
        }

        break;
}

if (!empty($form_id) && $collection = $modx->getCollection('fmzFormsUsergroup', array('form_id' => $form_id))) {
    //form has usergroups
    $usergroups = $modx->user->getUserGroups();
    if ($collection = $modx->getCollection('fmzFormsUsergroup', array('form_id' => $form_id, 'group_id:IN' => $usergroups))) {
        //user is in a assigned usergroup - all good

    } else {
        $modx->event->output('Fehlende Speicherberechtigung');
    }

}

return;