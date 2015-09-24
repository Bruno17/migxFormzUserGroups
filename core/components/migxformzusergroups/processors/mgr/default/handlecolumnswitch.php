<?php

$config = $modx->migx->customconfigs;

$prefix = $modx->getOption('prefix', $config, null);
$packageName = $config['packageName'];

$packagepath = $modx->getOption('core_path') . 'components/' . $packageName . '/';
$modelpath = $packagepath . 'model/';

$modx->addPackage($packageName, $modelpath, $prefix);
$classname = $config['classname'];

$joinclass = 'fmzFormsUsergroup';
$form_id = $modx->getOption('co_id', $scriptProperties, 0);
$object_id = $modx->getOption('object_id', $scriptProperties, 0);
$idx = $modx->getOption('idx', $scriptProperties, 0);

switch ($idx) {
    case '1':

        if ($joinobject = $modx->getObject($joinclass, array('form_id' => $form_id, 'group_id' => $object_id))) {

        } else {
            $joinobject = $modx->newObject($joinclass);
            $joinobject->set('form_id', $form_id);
            $joinobject->set('group_id', $object_id);
        }
        $joinobject->save();
        break;
    case '0':
        if ($joinobject = $modx->getObject($joinclass, array('form_id' => $form_id, 'group_id' => $object_id))) {
            $joinobject->remove();
        }
        break;
    default:
        break;
}


//clear cache for all contexts
$collection = $modx->getCollection('modContext');
foreach ($collection as $context) {
    $contexts[] = $context->get('key');
}
$modx->cacheManager->refresh(array(
    'db' => array(),
    'auto_publish' => array('contexts' => $contexts),
    'context_settings' => array('contexts' => $contexts),
    'resource' => array('contexts' => $contexts),
    ));

return $modx->error->success();