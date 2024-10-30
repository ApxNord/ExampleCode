<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');

global $APPLICATION;
$module_id = 'exchange';

if ($APPLICATION->GetGroupRight($module_id) < 'W') {
    $APPLICATION->AuthForm(GetMessage('ACCESS_DENIED'));
}

IncludeModuleLangFile(__FILE__);

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');

// вкладки
$aTabs = [
    [
        'DIV' 	=> 'import',
        'TAB'	=> GetMessage('TAB_IMPORT'),
        'TITLE' => GetMessage('TITLE_IMPORT')
    ],
];
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<form name="exchange_form" id="exchange_form" action="<?$APPLICATION->GetCurPage()?>" enctype="multipart/form-data" method="post">

<?
$tabControl = new CAdminTabControl('tabControl', $aTabs);
$tabControl->Begin();
$tabControl->BeginNextTab();
?>
    <tr>
        <td width="40%">Импорт структуры данных</td>
        <td width="60%">
            <input type="file" name="data_structures" accept=".csv" style="width: 400px">
            <input type="button" class="btn_import" data-action="data_structures" value='Импортировать' />
            <input type="hidden" name="action" value="data_structures">
        </td>
    </tr>

    <tr>
        <td width="40%">Импорт показателей проекта</td>
        <td width="60%">
            <input type="text" name="name_project" style="width: 200px" placeholder="Название проекта">
            <input type="file" name="project_indicators" accept=".csv" style="width: 400px">
            <input type="button" class="btn_import" data-action="project_indicators" value='Импортировать' />
            <input type="hidden" name="action" value="project_indicators">
        </td>
    </tr>

    <?$tabControl->EndTab();?>
    <?$tabControl->End();?>
    <input type="hidden" name="sessid" id="sessid" value="<?=bitrix_sessid()?>" />
</form>

<div class="adm-info-message-wrap"><div id="log_imp" class="adm-info-message" style="display: none;"></div></div>
<script>
    $('.btn_import').on('click', function(){
        var formData = new FormData();

        var btn = $(this);
        var name = btn.val();
        var action = btn.data('action');
        var sessid = $('#sessid').val();

        var nameProjectValue = $('input[name="name_project"]').val();
        formData.append('name_project', nameProjectValue);

        formData.append('action', action);
        if($('input[name=' + action + ']').length > 0) {
            formData.append(action, $('input[name=' + action + ']')[0].files[0]);
        }
        formData.append('sessid', sessid);

        disabledOn(btn, 'ВЫПОЛНЯЕТСЯ');
        $.ajax({
            type: 'POST',
            url: '/local/modules/<?=$module_id;?>/run.php',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function (dataResult) {
                console.log(formData);
                if ( dataResult != '' ) {
                    $('#log_imp').html(dataResult).css('display','block');
                }else{
                    $('#log_imp').html('Ошибка '+name).css('display','block');
                }
                disabledOff(btn, name);
            }
        });
    });

    function disabledOn(btn, message){
        btn.val(message);
        btn.attr('disabled', true);
    }
    function disabledOff(btn, message)
    {
        btn.val(message);
        btn.attr('disabled', false);
    }
</script>

<?require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');?>
