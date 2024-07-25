<?php defined('B_PROLOG_INCLUDED') || die;

use \Bitrix\Main\Loader;
use \Bitrix\Crm\StatusTable;
use \Bitrix\Main\Localization\Loc;

global $APPLICATION, $USER;

if (!$USER->IsAdmin()) return;

$module_id = 'devresource.dashboard';
Loader::includeModule($module_id);
Loader::includeModule('crm');

/** Регионы START **/
$regionsCDBResult = \CUserFieldEnum::GetList(['USER_FIELD_ID' => 'ASC']);

while ($region = $regionsCDBResult->fetch()) {
    if ($region['USER_FIELD_ID'] === '3819') {
        $leadRegions['options'][$region['ID']] = $region['VALUE'];
        $leadRegions['default'] .= $region['ID'] . ',';
    }
    if ($region['USER_FIELD_ID'] === '3823') {
        $dealRegions['options'][$region['ID']] = $region['VALUE'];
        $dealRegions['default'] .= $region['ID'] . ',';
    }
}
/** Регионы END **/

/** Статусы и стадии START **/
$statusesCDBResult = StatusTable::getList([
    'group' => ['ENTITY_ID'],
    'cache' => ['ttl' => 3600],
    'order' => ['ID' => 'ASC']
]);
while ($status = $statusesCDBResult->fetch()) {
    if ($status['ENTITY_ID'] === 'STATUS') {
        $leadStatuses[$status['STATUS_ID']] = $status['NAME'];
    }
    if ($status['ENTITY_ID'] === 'DEAL_STAGE_493') {
        $dealStages[$status['STATUS_ID']] = $status['NAME'];
    }
}
/** Статусы и стадии END **/

/** Табы START **/
$tabs = [
    [
        'DIV' => 'lead_settings',
        'TAB' => Loc::getMessage('DASHBOARD_LEAD_TAB_NAME'),
        'TITLE' => Loc::getMessage('DASHBOARD_TAB_TITLE')
    ],
    [
        'DIV' => 'deal_settings',
        'TAB' => Loc::getMessage('DASHBOARD_DEAL_TAB_NAME'),
        'TITLE' => Loc::getMessage('DASHBOARD_TAB_TITLE')
    ],
    [
        'DIV' => 'agent_settings',
        'TAB' => Loc::getMessage('DASHBOARD_AGENT_TAB_NAME'),
        'TITLE' => Loc::getMessage('DASHBOARD_TAB_TITLE')
    ],
];
/** Табы END **/

/** Опции START **/
$optionsLead = [
    'lead' => [
        Loc::getMessage('DASHBOARD_OPTION_CITIZENSHIP_BLOCK_TITLE'),
        ['citizenship_lead_user_field_code', Loc::getMessage('DASHBOARD_OPTION_CITIZENSHIP_BLOCK_TITLE') . ':', 'UF_CRM_1642420792', ['text', 30], 'N', Loc::getMessage('DASHBOARD_OPTION_REQURED_UF_LABEL'), 'N'],
        ['citizenship_lead', Loc::getMessage('DASHBOARD_OPTION_REGION_TITLE') . ':', $leadRegions['default'], ["multiselectbox", $leadRegions['options']]],
        Loc::getMessage('DASHBOARD_OPTION_STATUSES_TITLE'),
        ['note' => Loc::getMessage('DASHBOARD_OPTION_NOTE_LEAD')],
        ['applicationsReceived', Loc::getMessage('DASHBOARD_OPTION_APPLICATIONS_RECEIVED') . ':', '', ["multiselectbox", $leadStatuses]],
    ],
];

$optionsDeal = [
    'deal' => [
        Loc::getMessage('DASHBOARD_OPTION_MAIN_BLOCK_TITLE'),
        ['dateAuthUserFiledCode', Loc::getMessage('DASHBOARD_OPTION_AUTH_DATE_USER_FIELD_CODE') . ':', 'UF_CRM_AUTHORISEDDATE', ['text', 30], 'N', Loc::getMessage('DASHBOARD_OPTION_REQURED_UF_LABEL'), 'N'],
        ['ammountUserFiledCode', Loc::getMessage('DASHBOARD_OPTION_AMMOUNT_USER_FIELD_CODE') . ':', 'UF_CRM_5D7FB6C2266D7', ['text', 30], 'N', Loc::getMessage('DASHBOARD_OPTION_REQURED_UF_LABEL'), 'N'],
        ['loanAgreementAmountUserFiledCode', Loc::getMessage('DASHBOARD_OPTION_LOAN_AGREEMENT_AMMOUNT_USER_FIELD_CODE') . ':', 'UF_CRM_1614779347', ['text', 30], 'N', Loc::getMessage('DASHBOARD_OPTION_REQURED_UF_LABEL'), 'N'],
        Loc::getMessage('DASHBOARD_OPTION_CITIZENSHIP_BLOCK_TITLE'),
        ['citizenship_deal_user_field_code', Loc::getMessage('DASHBOARD_OPTION_CITIZENSHIP_BLOCK_TITLE') . ':', 'UF_CRM_61E55B8853173', ['text', 30], 'N', Loc::getMessage('DASHBOARD_OPTION_REQURED_UF_LABEL'), 'N'],
        ['citizenship_deal', Loc::getMessage('DASHBOARD_OPTION_REGION_TITLE') . ':', $dealRegions['default'], ["multiselectbox", $dealRegions['options']]],
        Loc::getMessage('DASHBOARD_OPTION_STAGES_TITLE'),
        ['note' => Loc::getMessage('DASHBOARD_OPTION_NOTE_DEAL')],
        ['submittedBanks', Loc::getMessage('DASHBOARD_OPTION_SUBMITTED_BANKS') . ':', '', ["multiselectbox", $dealStages]],
        ['applicationApproved', Loc::getMessage('DASHBOARD_OPTION_APPLICATION_APPROVED') . ':', '', ["multiselectbox", $dealStages]],
        ['dealsAuth', Loc::getMessage('DASHBOARD_OPTION_DEALS_AUTH') . ':', '', ["multiselectbox", $dealStages]],
        ['technicallyRejected', Loc::getMessage('DASHBOARD_OPTION_TECHNICALLY_REJECTED') . ':', '', ["multiselectbox", $dealStages]],
        ['percentTechnicallyRejected', Loc::getMessage('DASHBOARD_OPTION_PERCENT_TECHNICALLY_REJECTED') . ':', '', ["multiselectbox", $dealStages]],
        ['dealsAllStages', Loc::getMessage('DASHBOARD_OPTION_DEALS_ALL_STAGES') . ':', '', ["multiselectbox", $dealStages]],
    ],
];

$optionsAgent = [
    'agent' => [
        Loc::getMessage('DASHBOARD_OPTION_AGENT_BLOCK_TITLE'),
        ['note' => Loc::getMessage('DASHBOARD_OPTION_NOTE_AGENT')],
        ['agents', Loc::getMessage('DASHBOARD_OPTION_APPROVED_APPLICATIONS') . ':', '', ["multiselectbox", $dealStages]],
    ],
];
/** Опции END **/

if (check_bitrix_sessid() && strlen($_POST['save']) > 0) {
    foreach ($optionsLead as $option) {
        __AdmSettingsSaveOptions($module_id, $option);
    }
    foreach ($optionsDeal as $option) {
        __AdmSettingsSaveOptions($module_id, $option);
    }
    foreach ($optionsAgent as $option) {
        __AdmSettingsSaveOptions($module_id, $option);
    }

    LocalRedirect($APPLICATION->GetCurPageParam());
}

$tabControl = new CAdminTabControl('tabControl', $tabs);
$tabControl->Begin(); ?>

<form method="POST"
      action="<?= $APPLICATION->GetCurPage(); ?>?mid=<?= htmlspecialcharsbx($module_id); ?>&lang=<?= LANGUAGE_ID; ?>">

    <?php foreach ($optionsLead as $aTab): ?>
        <?php $tabControl->BeginNextTab(); ?>
        <?php __AdmSettingsDrawList($module_id, $aTab); ?>
    <?php endforeach; ?>

    <?php foreach ($optionsDeal as $aTab): ?>
        <?php $tabControl->BeginNextTab(); ?>
        <?php __AdmSettingsDrawList($module_id, $aTab); ?>
    <?php endforeach; ?>

    <?php foreach ($optionsAgent as $aTab): ?>
        <?php $tabControl->BeginNextTab(); ?>
        <?php __AdmSettingsDrawList($module_id, $aTab); ?>
    <?php endforeach; ?>

    <?php $tabControl->Buttons(['btnApply' => false, 'btnCancel' => false, 'btnSaveAndAdd' => false]); ?>
    <?= bitrix_sessid_post(); ?>
    <?php $tabControl->End(); ?>
</form>


